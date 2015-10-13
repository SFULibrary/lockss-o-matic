<?php

namespace LOCKSSOMatic\SwordBundle\Controller;

use Exception;
use LOCKSSOMatic\CrudBundle\Entity\Content;
use LOCKSSOMatic\CrudBundle\Entity\ContentProvider;
use LOCKSSOMatic\CrudBundle\Entity\Deposit;
use LOCKSSOMatic\CrudBundle\Entity\Plugin;
use LOCKSSOMatic\CrudBundle\Utility\ContentBuilder;
use LOCKSSOMatic\CrudBundle\Utility\DepositBuilder;
use LOCKSSOMatic\LogBundle\Services\LoggingService;
use LOCKSSOMatic\SwordBundle\Exceptions\BadRequestException;
use LOCKSSOMatic\SwordBundle\Exceptions\DepositUnknownException;
use LOCKSSOMatic\SwordBundle\Exceptions\HostMismatchException;
use LOCKSSOMatic\SwordBundle\Exceptions\MaxUploadSizeExceededException;
use LOCKSSOMatic\SwordBundle\Exceptions\TargetOwnerUnknownException;
use LOCKSSOMatic\SwordBundle\Utilities\Namespaces;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SimpleXMLElement;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api/sword/2.0")
 */
class SwordController extends Controller
{

    /**
     * @var Namespaces
     */
    private $namespaces;

    /**
     * @var LoggingService
     */
    private $activityLog;

    public function __construct()
    {
        $this->namespaces = new Namespaces();
    }

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->activityLog = $this->container->get('activity_log');
    }

    private function fetchHeader(Request $request, $name, $required = false)
    {
        if ($request->headers->has($name)) {
            return $request->headers->get($name);
        }
        if ($request->headers->has("X-" . $name)) {
            return $request->headers->has("X-" . $name);
        }
        // only accept headers in query parameters for development purposes.
        if ($this->container->get( 'kernel' )->getEnvironment() === 'dev' && $request->query->has($name)) {
            return $request->query->get($name);
        }
        if ($required === true) {
            throw new BadRequestException("Required HTTP header {$name} missing.");
        }
        return null;
    }

    private function getSimpleXML($string)
    {
        $xml = new SimpleXMLElement($string);
        $this->namespaces->registerNamespaces($xml);
        return $xml;
    }

    /**
     *
     * @param type $uuid
     * @return ContentProvider
     * @throws TargetOwnerUnknownException
     */
    private function getContentProvider($uuid = null)
    {
        $contentProvider = $this
            ->getDoctrine()
            ->getRepository('LOCKSSOMaticCrudBundle:ContentProvider')
            ->findOneBy(array('uuid' => $uuid));
        if ($contentProvider === null) {
            throw new TargetOwnerUnknownException();
        }
        return $contentProvider;
    }

    /**
     *
     * @param type $uuid
     * @return Deposit
     * @throws DepositUnknownException
     */
    private function getDeposit($uuid = null)
    {
        $deposit = $this
            ->getDoctrine()
            ->getRepository('LOCKSSOMatic\CrudBundle\Entity\Deposit')
            ->findOneBy(array('uuid' => $uuid));

        if ($deposit === null) {
            throw new DepositUnknownException();
        }

        return $deposit;
    }

    private function matchDepositToProvider(Deposit $deposit, ContentProvider $contentProvider)
    {
        if ($deposit->getContentProvider()->getId() !== $contentProvider->getId()) {
            throw new BadRequestException(
            'Deposit or Content Provider incorrect. The '
            . 'requested deposit does not belong to the requested content provider.'
            );
        }
    }

    /**
     * @param type $url
     * @return Content
     * @throws BadRequestException
     */
    private function getContent(Deposit $deposit, $url)
    {
        $content = $this
            ->getDoctrine()
            ->getRepository('LOCKSSOMaticCrudBundle:Content')
            ->findOneBy(array(
            'url'     => $url,
            'deposit' => $deposit,
        ));
        if ($content === null) {
            throw new BadRequestException('Content item not in database: ' . $url);
        }

        return $content;
    }

    /**
     * SWORD service document, aka sd-iri
     * 
     * @Route("/sd-iri", name="sword_service")
     * @param Request $request
     */
    public function serviceDocumentAction(Request $request)
    {
        $obh = $this->fetchHeader($request, 'On-Behalf-Of', true);
        $provider = $this->getContentProvider($obh);
        $plugin = $provider->getPlugin();
        $checksumMethods = $this->container->getParameter('lockss_checksums');

        $this->activityLog->overrideUser($obh);
        $this->activityLog->log('Requested service document.');
        $this->activityLog->overrideUser(null);
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

    private function precheckContentProperties(SimpleXMLElement $content, Plugin $plugin) {
        foreach($plugin->getDefinitionalProperties() as $property) {
            $nodes = $content->xpath("lom:property[@name='$property']");
            if( count($nodes) === 0) {
                throw new BadRequestException("{$property} is a required property.");
            }
            if(count($nodes) > 1) {
                throw new BadRequestException("{$property} must be unique.");
            }
            $property = $nodes[0];
            if( ! $property->attributes()->value) {
                throw new BadRequestException("{$property} must have a value.");
            }
        }
    }

    private function precheckDeposit(SimpleXMLElement $atomEntry, ContentProvider $provider)
    {
        if (count($atomEntry->xpath('//lom:content')) === 0) {
            throw new BadRequestException("Empty deposits are not allowed.");
        }
        $plugin = $provider->getPlugin();
        
        $permissionHost = $provider->getPermissionHost();
        foreach ($atomEntry->xpath('//lom:content') as $content) {
            $this->get('logger')->error((string)($content));
            // check required properties.
            $this->precheckContentProperties($content, $plugin);
            $url = trim((string)$content);
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

    private function renderDepositReceipt($contentProvider, $deposit)
    {
        // @TODO this should be a call to render depsoitReceiptAction() or something.
        // Return the deposit receipt.
        $response = $this->render(
            'LOCKSSOMaticSwordBundle:Sword:depositReceipt.xml.twig',
            array(
                'contentProvider' => $contentProvider,
                'deposit'         => $deposit
            )
        );
        $response->headers->set('Content-Type', 'text/xml');
        return $response;
    }

    /**
     * Create a deposit by posting XML to this URL, aka col-iri
     *
     * @Route("/col-iri/{providerUuid}", name="sword_collection", requirements={
     *      "providerUuid": ".{36}"
     * })
     * @Method({"POST"})
     * @param Request $request
     * @param string $providerUuid
     */
    public function createDepositAction(Request $request, $providerUuid)
    {
        $em = $this->getDoctrine()->getManager();
        $provider = $this->getContentProvider($providerUuid);

        $this->activityLog->overrideUser($providerUuid);
        $atomEntry = $this->getSimpleXML($request->getContent());
        $this->precheckDeposit($atomEntry, $provider);

        $depositBuilder = new DepositBuilder();
        $contentBuilder = new ContentBuilder();
        $deposit = $depositBuilder->fromSimpleXML($atomEntry, $em);
        $deposit->setContentProvider($provider);
        foreach($atomEntry->xpath('lom:content') as $node) {
            /** @var Content $content */
            $content = $contentBuilder->fromSimpleXML($node, $em);
            $content->setDeposit($deposit);            
            $auid = $content->generateAuId();
            $this->container->get('logger')->error($auid);
            $au = $em->getRepository('LOCKSSOMaticCrudBundle:Au')->findOneBy(array(
                'auid' => $auid
            ));
            if($au === null) {
                $au = new Au();
                $em->persist($au);
            } else {
            }
            $content->setAu($au);
        }
        // TODO figure out the new logic for adding content to AUs.

        $em->flush();
        $this->activityLog->overrideUser(null);

        $response = $this->renderDepositReceipt($provider, $deposit);
        $editIri = $this->get('router')->generate(
            'sword_reciept', array(
                'providerUuid' => $provider->getUuid(),
                'depositUuid' => $deposit->getUuid()
            ), true
        );
        $response->headers->set('Location', $editIri);
        $response->setStatusCode(Response::HTTP_CREATED);
        return $response;
    }

    /**
     * Get a deposit statement, showing the status of the deposit in LOCKSS,
     * from this URL. Also known as state-iri
     * 
     * @Route("/cont-iri/{providerUuid}/{depositUuid}/state", name="sword_statement", requirements={
     *      "providerUuid": ".{36}",
     *      "depositUuid": ".{36}"
     * })

     * @Method({"GET"})
     */
    public function statementAction($providerUuid, $depositUuid)
    {
        $provider = $this->getContentProvider($providerUuid);
        $deposit = $this->getDeposit($depositUuid);
        $this->matchDepositToProvider($deposit, $provider);
        $boxes = array();
        if($provider->getPln()) {
            $boxes = $provider->getPln()->getBoxes();
        }
        $content = $deposit->getContent();

        $status = array();
        foreach($content as $item) {
            foreach($boxes as $box) {
                $status[$item->getId()][$box->getId()] = 'unknown';
                // TODO get the items status from the box via http request
                // OR get it from the database if we do that.
            }
        }
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('LOCKSSOMaticSwordBundle:Sword:statement.xml.twig', array(
            'contentProvider' => $provider,
            'boxes' => $boxes,
            'deposit' => $deposit,
            'content' => $content,
            'status' => $status,
        ), $response);
    }

    /**
     * Get a deposit receipt from this URL, also known as the edit-iri
     *
     * @Route("/cont-iri/{providerUuid}/{depositUuid}/edit", name="sword_reciept", requirements={
     *      "providerUuid": ".{36}",
     *      "depositUuid": ".{36}"
     * })
     * @Method({"GET"})
     */
    public function receiptAction($providerUuid, $depositUuid)
    {
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
     */
    public function editDepositAction(Request $request, $providerUuid, $depositUuid)
    {
        $this->activityLog->overrideUser($providerUuid);

        $provider = $this->getContentProvider($providerUuid);
        $deposit = $this->getDeposit($depositUuid);
        $this->matchDepositToProvider($deposit, $provider);

        $atomEntry = $this->getSimpleXML($request->getContent());
        $this->precheckDeposit($atomEntry, $provider);
        $updated = 0;
        foreach($atomEntry->xpath('//lom:content') as $contentChunk) {
            try {
                $content = $this->getContent($deposit, (string)$contentChunk);
            } catch (Exception $ex) {
                continue;
                // Sigh. SWORD says this isn't an error.
            }
            $recrawl = $contentChunk[0]->attributes()->recrawl;
            $content->setRecrawl($recrawl === 'true');
            $updated++;
        }

        $this->activityLog->overrideUser(null);
        $response = $this->renderDepositReceipt($provider, $deposit);
        $editIri = $this->get('router')->generate(
            'sword_reciept', array(
                'providerUuid' => $provider->getUuid(),
                'depositUuid' => $deposit->getUuid()
            ), true
        );
        $response->headers->set('Location', $editIri);
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * Fetch a representation of the deposit from this URL, aka cont-iri
     *
     * @Route("/cont-iri/{providerUuid}/{depositUuid}", name="sword_view", requirements={
     *      "providerUuid": ".{36}",
     *      "depositUuid": ".{36}"
     * })
     * @Method({"GET"})
     */
    public function viewDepositAction(Request $request, $providerUuid, $depositUuid)
    {
        $provider = $this->getContentProvider($providerUuid);
        $deposit = $this->getDeposit($depositUuid);
        $this->matchDepositToProvider($deposit, $provider);
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('LOCKSSOMaticSwordBundle:Sword:depositView.xml.twig', array(
            'contentProvider' => $provider,
            'deposit' => $deposit,
        ), $response);
    }

}
