<?php

namespace LOCKSSOMatic\SwordBundle\Controller;

use Exception;
use LOCKSSOMatic\CrudBundle\Entity\Content;
use LOCKSSOMatic\CrudBundle\Entity\ContentProvider;
use LOCKSSOMatic\CrudBundle\Entity\Deposit;
use LOCKSSOMatic\CrudBundle\Entity\Plugin;
use LOCKSSOMatic\LockssBundle\Services\ContentFetcherService;
use LOCKSSOMatic\LogBundle\Services\LoggingService;
use LOCKSSOMatic\SwordBundle\Exceptions\BadRequestException;
use LOCKSSOMatic\SwordBundle\Exceptions\DepositUnknownException;
use LOCKSSOMatic\SwordBundle\Exceptions\HostMismatchException;
use LOCKSSOMatic\SwordBundle\Exceptions\MaxUploadSizeExceededException;
use LOCKSSOMatic\SwordBundle\Exceptions\TargetOwnerUnknownException;
use LOCKSSOMatic\SwordBundle\Utilities\Namespaces;
use Monolog\Logger;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SimpleXMLElement;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bespoke SWORD 2.0 API controller for LOCKSSOMatic. You're welcome to
 * use it for another purpose, but it's probably LOCKSSOMatic specific.
 *
 * @Route("/api/sword/2.0")
 */
class SwordController extends Controller
{
    /**
     * @var Namespaces
     */
    private $namespaces;

    /**
     * @var Logger
     */
    private $swordLog;

    /**
     * Construct the controller.
     */
    public function __construct() {
        $this->namespaces = new Namespaces();
    }

    /**
     * Set the container.
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->swordLog = $this->container->get('monolog.logger.sword');
    }

    /**
     * Fetch a request header. Checks for the header and the X- variant. And
     * if the app is in the dev kernel it checks for the header in the
     * query parameters. If $required is true, throws an exception if the
     * header is missing.
     *
     * @param Request $request
     * @param string $name
     * @param boolean $required
     *
     * @return string|null
     *
     * @throws BadRequestException
     */
    private function fetchHeader(Request $request, $name, $required = false) {
        if ($request->headers->has($name)) {
            return $request->headers->get($name);
        }
        if ($request->headers->has('X-'.$name)) {
            return $request->headers->get('X-'.$name);
        }
        // only accept headers in query parameters for development purposes.
        if ($this->container->get('kernel')->getEnvironment() === 'dev' && $request->query->has($name)) {
            return $request->query->get($name);
        }
        if ($required === true) {
            throw new BadRequestException("Required HTTP header {$name} missing.");
        }

        return null;
    }

    /**
     * Get a SimpleXMLElement from a string, and assign the normal
     * namespaces to it.
     *
     * @param string $string
     * @return SimpleXMLElement
     */
    private function getSimpleXML($string) {
        $xml = new SimpleXMLElement($string);
        $this->namespaces->registerNamespaces($xml);

        return $xml;
    }

    /**
     * Get the content provider for a UUID or throw an exception.
     *
     * @param string $uuid
     *
     * @return ContentProvider
     *
     * @throws TargetOwnerUnknownException
     */
    private function getContentProvider($uuid = null) {
        $contentProvider = $this->getDoctrine()->getRepository('LOCKSSOMaticCrudBundle:ContentProvider')->findOneBy(array('uuid' => $uuid));
        if ($contentProvider === null) {
            throw new TargetOwnerUnknownException();
        }

        return $contentProvider;
    }

    /**
     * Get a deposit by UUID or throw an exception.
     *
     * @param string $uuid
     *
     * @return Deposit
     *
     * @throws DepositUnknownException
     */
    private function getDeposit($uuid = null) {
        $deposit = $this->getDoctrine()->getRepository('LOCKSSOMatic\CrudBundle\Entity\Deposit')->findOneBy(array('uuid' => $uuid));

        if ($deposit === null) {
            throw new DepositUnknownException();
        }

        return $deposit;
    }

    /**
     * Check that a deposit matches a content provider or throw an exception.
     *
     * @param Deposit $deposit
     * @param ContentProvider $contentProvider
     * @throws BadRequestException
     */
    private function matchDepositToProvider(Deposit $deposit, ContentProvider $contentProvider) {
        if ($deposit->getContentProvider()->getId() !== $contentProvider->getId()) {
            throw new BadRequestException(
                'Deposit or Content Provider incorrect. The '
                .'requested deposit does not belong to the requested content provider.'
            );
        }
    }

    /**
     * Get a content entry by deposit and URL. They must match.
     *
     * @param Deposit $deposit
     * @param string $url
     *
     * @return Content
     *
     * @throws BadRequestException
     */
    private function getContent(Deposit $deposit, $url) {
        $content = $this->getDoctrine()->getRepository('LOCKSSOMaticCrudBundle:Content')->findOneBy(array(
                'url' => $url,
                'deposit' => $deposit,
        ));
        if ($content === null) {
            throw new BadRequestException('Content item not in database: '.$url);
        }

        return $content;
    }

    /**
     * SWORD service document, aka sd-iri.
     *
     * @Route("/sd-iri", name="sword_service")
     *
     * @param Request $request
     */
    public function serviceDocumentAction(Request $request) {
        $this->swordLog->notice('service document');
        $obh = $this->fetchHeader($request, 'On-Behalf-Of', true);
        $provider = $this->getContentProvider($obh);
        $plugin = $provider->getPlugin();
        $checksumMethods = $this->container->getParameter('lockss_checksums');

        $response = $this->render(
            'LOCKSSOMaticSwordBundle:Sword:serviceDocument.xml.twig',
            array(
            'plugin' => $plugin,
            'contentProvider' => $provider,
            'checksumMethods' => $checksumMethods,
            )
        );
        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }

    /**
     * Make sure that the required content properties exist in the XML for a
     * plugin.
     *
     * @param SimpleXMLElement $content
     * @param Plugin $plugin
     * @throws BadRequestException
     */
    private function precheckContentProperties(SimpleXMLElement $content, Plugin $plugin) {
        $pluginId = $plugin->getPluginIdentifier();
        $nondefinitionalCPDs = $this->container->getParameter('lom_nondefinitional_cpds');

        foreach ($plugin->getDefinitionalProperties() as $property) {
            if (array_key_exists($pluginId, $nondefinitionalCPDs) &&
                in_array($property, $nondefinitionalCPDs[$pluginId])) {
                continue;
            }

            $nodes = $content->xpath("lom:property[@name='$property']");
            if (count($nodes) === 0) {
                throw new BadRequestException("{$property} is a required property.");
            }
            if (count($nodes) > 1) {
                throw new BadRequestException("{$property} must be unique.");
            }
            $property = $nodes[0];
            if (!$property->attributes()->value) {
                throw new BadRequestException("{$property} must have a value.");
            }
        }
    }

    /**
     * Precheck a deposit for the required properties and make sure the properties
     * all make some sense.
     *
     * @param SimpleXMLElement $atomEntry
     * @param ContentProvider $provider
     *
     * @throws BadRequestException
     * @throws HostMismatchException
     * @throws MaxUploadSizeExceededException
     */
    private function precheckDeposit(SimpleXMLElement $atomEntry, ContentProvider $provider) {
        if (count($atomEntry->xpath('//lom:content')) === 0) {
            throw new BadRequestException('Empty deposits are not allowed.');
        }
        $plugin = $provider->getPlugin();

        $permissionHost = $provider->getPermissionHost();
        foreach ($atomEntry->xpath('//lom:content') as $content) {
            // check required properties.
            $this->precheckContentProperties($content, $plugin);
            $url = trim((string) $content);
            $host = parse_url($url, PHP_URL_HOST);
            if ($permissionHost !== $host) {
                $msg = "Content host:{$host} Permission host: {$permissionHost}";
                throw new HostMismatchException($msg);
            }

            if ($content->attributes()->size > $provider->getMaxFileSize()) {
                $size = $content->attributes()->size;
                $max = $provider->getMaxFileSize();
                throw new MaxUploadSizeExceededException("Content size {$size} exceeds provider's maximum: {$max}");
            }
        }
    }

    /**
     * Given a deposit and content provider, render a deposit reciept.
     *
     * @param ContentProvider $contentProvider
     * @param Deposit $deposit
     *
     * @return Response containing the XML.
     */
    private function renderDepositReceipt(ContentProvider $contentProvider, Deposit $deposit) {
        // @TODO this should be a call to render depositReceiptAction() or something.
        // Return the deposit receipt.
        $response = $this->render(
            'LOCKSSOMaticSwordBundle:Sword:depositReceipt.xml.twig',
            array(
            'contentProvider' => $contentProvider,
            'deposit' => $deposit,
            )
        );
        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }

    /**
     * Create a deposit by posting XML to this URL, aka col-iri.
     *
     * @Route("/col-iri/{providerUuid}", name="sword_collection", requirements={
     *      "providerUuid": ".{36}"
     * })
     * @Method({"POST"})
     *
     * @param Request $request
     * @param string  $providerUuid
     *
     * @return Response
     */
    public function createDepositAction(Request $request, $providerUuid) {
        $this->swordLog->notice('create deposit');
        $em = $this->getDoctrine()->getManager();
        $provider = $this->getContentProvider($providerUuid);

        $atomEntry = $this->getSimpleXML($request->getContent());
        $this->precheckDeposit($atomEntry, $provider);

        $depositBuilder = $this->container->get('crud.builder.deposit');
        $contentBuilder = $this->container->get('crud.builder.content');
        $idGenerator = $this->container->get('crud.au.idgenerator');
        $deposit = $depositBuilder->fromSimpleXML($atomEntry, $em);
        $deposit->setContentProvider($provider);
        foreach ($atomEntry->xpath('lom:content') as $node) {
            /** @var Content $content */
            $content = $contentBuilder->fromSimpleXML($node, $em);
            $content->setDeposit($deposit);
            $auid = $idGenerator->fromContent($content, false);

            $au = $em->getRepository('LOCKSSOMaticCrudBundle:Au')->findOneBy(array(
                'auid' => $auid,
            ));
            if ($au === null) {
                $auBuilder = $this->container->get('crud.builder.au');
                $au = $auBuilder->fromContent($content);
            }
            $content->setAu($au);
        }

        $em->flush();

        $response = $this->renderDepositReceipt($provider, $deposit);
        $editIri = $this->get('router')->generate(
            'sword_reciept',
            array(
            'providerUuid' => $provider->getUuid(),
            'depositUuid' => $deposit->getUuid(),
            ),
            true
        );
        $response->headers->set('Location', $editIri);
        $response->setStatusCode(Response::HTTP_CREATED);

        return $response;
    }

    /**
     * Get a deposit statement, showing the status of the deposit in LOCKSS,
     * from this URL. Also known as state-iri. Includes a sword:originalDeposit element for
     * each content item in the deposit.
     *
     * @Route("/cont-iri/{providerUuid}/{depositUuid}/state", name="sword_statement", requirements={
     *      "providerUuid": ".{36}",
     *      "depositUuid": ".{36}"
     * })

     * @Method({"GET"})
     *
     * @param string $providerUuid
     * @param string $depositUuid
     *
     * @return Response
     */
    public function statementAction($providerUuid, $depositUuid) {
        $this->swordLog->notice("statement - {$providerUuid} - {$depositUuid}");
        $provider = $this->getContentProvider($providerUuid);
        $deposit = $this->getDeposit($depositUuid);
        $this->matchDepositToProvider($deposit, $provider);
        $boxes = array();
        if ($provider->getPln()) {
            $boxes = $provider->getPln()->getBoxes();
        }

        if ($deposit->getAgreement() == 1) {
            $state = 'agreement';
            $stateDescription = 'LOCKSS boxes have harvested the content and agree on the checksum.';
        } else {
            $state = 'inProgress';
            $stateDescription = 'LOCKSS boxes have not completed harvesting the content.';
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');

        return $this->render(
            'LOCKSSOMaticSwordBundle:Sword:statement.xml.twig',
            array(
                'state' => $state,
                'stateDescription' => $stateDescription,
                'deposit' => $deposit,
                ),
            $response
        );
    }

    /**
     * Attempt to fetch the original deposit from LOCKSS, store it to
     * the file system in a temp file, and then serve it to the user agent.
     *
     * @Route("/original/{providerUuid}/{depositUuid}/{filename}", name="original_deposit", requirements={
     *      "providerUuid": ".{36}",
     *      "depositUuid": ".{36}"
     * })
     *
     * @param string $providerUuid
     * @param string $depositUuid
     * @param string $filename
     *
     * @return BinaryFileResponse
     */
    public function originalDepositAction($providerUuid, $depositUuid, $filename) {
        $this->swordLog->notice("original deposit - {$providerUuid} - {$depositUuid} - {$filename}");
        $provider = $this->getContentProvider($providerUuid);
        $deposit = $this->getDeposit($depositUuid);
        $this->matchDepositToProvider($deposit, $provider);

        $repo = $this->getDoctrine()->getManager()->getRepository('LOCKSSOMaticCrudBundle:Content');
        $content = $repo->findByFilename($deposit, $filename);

        /** @var ContentFetcherService */
        $fetcher = $this->container->get('lockss.content.fetcher');
        $file = $fetcher->fetch($content[0]);
        $tmp = tempnam(sys_get_temp_dir(), 'lockss-');
        $handle = fopen($tmp, 'wb');
        while($data = fread($file, 65535)) {
            fwrite($handle, $data);
        }
        fclose($file);
        fclose($handle);
        $response = new BinaryFileResponse($tmp);
        $response->deleteFileAfterSend(true);
        return $response;
    }

    /**
     * Get a deposit receipt from this URL, also known as the edit-iri.
     *
     * @Route("/cont-iri/{providerUuid}/{depositUuid}/edit", name="sword_reciept", requirements={
     *      "providerUuid": ".{36}",
     *      "depositUuid": ".{36}"
     * })
     * @Method({"GET"})
     *
     * @param string $providerUuid
     * @param string $depositUuid
     *
     * @return Response
     */
    public function receiptAction($providerUuid, $depositUuid) {
        $this->swordLog->notice("receipt - {$providerUuid} - {$depositUuid}");
        $provider = $this->getContentProvider($providerUuid);
        $deposit = $this->getDeposit($depositUuid);
        $this->matchDepositToProvider($deposit, $provider);

        return $this->renderDepositReceipt($provider, $deposit);
    }

    /**
     * HTTP PUT to this URL to edit a deposit. This URL is the same as the
     * recieptAction URL (aka edit-iri) but requires an HTTP PUT.
     *
     * @Route("/cont-iri/{providerUuid}/{depositUuid}/edit", name="sword_edit", requirements={
     *      "providerUuid": ".{36}",
     *      "depositUuid": ".{36}"
     * })
     * @Method({"PUT"})
     *
     * @param Request $request
     * @param string $providerUuid
     * @param string $depositUuid
     *
     * @return Response
     */
    public function editDepositAction(Request $request, $providerUuid, $depositUuid) {
        $this->swordLog->notice("edit - {$providerUuid} - {$depositUuid}");

        $provider = $this->getContentProvider($providerUuid);
        $deposit = $this->getDeposit($depositUuid);
        $this->matchDepositToProvider($deposit, $provider);

        $atomEntry = $this->getSimpleXML($request->getContent());
        $this->precheckDeposit($atomEntry, $provider);
        $updated = 0;
        foreach ($atomEntry->xpath('//lom:content') as $contentChunk) {
            try {
                $content = $this->getContent($deposit, (string) $contentChunk);
            } catch (Exception $ex) {
                continue;
                // Sigh. SWORD says this isn't an error.
            }
            $recrawl = $contentChunk[0]->attributes()->recrawl;
            $content->setRecrawl($recrawl === 'true');
            ++$updated;
        }

        $response = $this->renderDepositReceipt($provider, $deposit);
        $editIri = $this->get('router')->generate(
            'sword_reciept',
            array(
            'providerUuid' => $provider->getUuid(),
            'depositUuid' => $deposit->getUuid(),
            ),
            true
        );
        $response->headers->set('Location', $editIri);
        $response->setStatusCode(Response::HTTP_OK);

        return $response;
    }

    /**
     * Fetch a representation of the deposit from this URL, aka cont-iri.
     *
     * @Route("/cont-iri/{providerUuid}/{depositUuid}", name="sword_view", requirements={
     *      "providerUuid": ".{36}",
     *      "depositUuid": ".{36}"
     * })
     * @Method({"GET"})
     *
     * @param string $providerUuid
     * @param string $depositUuid
     *
     * @return Response containing the deposit reciept.
     */
    public function viewDepositAction($providerUuid, $depositUuid) {
        $provider = $this->getContentProvider($providerUuid);
        $deposit = $this->getDeposit($depositUuid);
        $this->matchDepositToProvider($deposit, $provider);
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');

        return $this->render(
            'LOCKSSOMaticSwordBundle:Sword:depositView.xml.twig',
            array(
                'contentProvider' => $provider,
                'deposit' => $deposit,
                ),
            $response
        );
    }
}
