<?php

namespace LOCKSSOMatic\SwordBundle\Controller;

use LOCKSSOMatic\CrudBundle\Entity\Content;
use LOCKSSOMatic\CrudBundle\Entity\ContentProvider;
use LOCKSSOMatic\CrudBundle\Entity\Deposit;
use LOCKSSOMatic\CrudBundle\Utility\DepositBuilder;
use LOCKSSOMatic\LogBundle\Services\LoggingService;
use LOCKSSOMatic\SwordBundle\Event\DepositContentEvent;
use LOCKSSOMatic\SwordBundle\Event\ServiceDocumentEvent;
use LOCKSSOMatic\SwordBundle\Exceptions\BadRequestException;
use LOCKSSOMatic\SwordBundle\Exceptions\DepositUnknownException;
use LOCKSSOMatic\SwordBundle\Exceptions\HostMismatchException;
use LOCKSSOMatic\SwordBundle\Exceptions\MaxUploadSizeExceededException;
use LOCKSSOMatic\SwordBundle\Exceptions\TargetOwnerUnknownException;
use LOCKSSOMatic\SwordBundle\SwordEvents;
use LOCKSSOMatic\SwordBundle\Utilities\Namespaces;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SimpleXMLElement;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
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
        if ($request->query->has($name)) {
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
     * @Route("/sd-iri", name="sword_service")
     * @param Request $request
     */
    public function serviceDocumentAction(Request $request)
    {
        $obh = $this->fetchHeader($request, 'On-Behalf-Of', true);
        $provider = $this->getContentProvider($obh);
        $this->activityLog->overrideUser($obh);
        $this->activityLog->log('Requested service document.');
        $this->activityLog->overrideUser(null);

        $xml = $this->getSimpleXML('<root/>');
        $event = new ServiceDocumentEvent($xml);
        /** @var EventDispatcher */
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch(SwordEvents::SERVICEDOC, $event);

        $response = $this->render(
            'LOCKSSOMaticSwordBundle:Sword:serviceDocument.xml.twig',
            array(
            'contentProvider' => $provider,
            'xml'             => $xml
            )
        );
        $response->headers->set('Content-Type', 'text/xml');
        return $response;
    }

    private function precheckDeposit(SimpleXMLElement $atomEntry, $provider)
    {
        if (count($atomEntry->xpath('//lom:content')) === 0) {
            throw new BadRequestException("Empty deposits are not allowed.");
        }
        $permissionHost = $provider->getPermissionHost();
        foreach ($atomEntry->xpath('//lom:content') as $contentChunk) {
            $chunk = preg_replace('/\s*/', '', (string) $contentChunk);
            $host = parse_url((string) $chunk, PHP_URL_HOST);
            if ($permissionHost !== $host) {
                throw new HostMismatchException();
            }
            if ($contentChunk->attributes()->size > $provider->getMaxFileSize()) {
                $size = $contentChunk->attributes()->size;
                $max = $provider->getMaxFileSize();
                throw new MaxUploadSizeExceededException("Content size {$size} exceeds provider's maximum: {$max}");
            }
        }
    }

    private function getLomPlugin(SimpleXMLElement $atomEntry)
    {
        $pluginName = 'lomplugin.aus.size';
        $pluginAttr = $atomEntry->xpath('lom:plugin/@name');
        if (count($pluginAttr)) {
            $pluginName = (string) $pluginAttr[0];
        }
        return $pluginName;
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
     * Create a deposit by posting XML to this URL.
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
        $lomPlugin = $this->getLomPlugin($atomEntry);

        $this->get('logger')->warn("LOM Plugin: {$lomPlugin}");

        $depositBuilder = new DepositBuilder();
        $deposit = $depositBuilder->fromSimpleXML($atomEntry);
        $deposit->setContentProvider($provider);
        $em->persist($deposit);

        /** @var EventDispatcher */
        $dispatcher = $this->get('event_dispatcher');
        foreach ($atomEntry->xpath('//lom:content') as $contentChunk) {
            $event = new DepositContentEvent($lomPlugin, $deposit, $provider, $contentChunk);
            $dispatcher->dispatch(SwordEvents::DEPOSITCONTENT, $event);
        }
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
     * Get a deposit statement, showing the status of the deposit in LOCKSS, from this URL.
     * 
     * @Route("/cont-iri/{providerUuid}/{depositUuid}/state", name="sword_statement", requirements={
     *      "providerUuid": ".{36}",
     *      "depositUuid": ".{36}"
     * })

     * @Method({"GET"})
     */
    public function statementAction(Request $request, $providerUuid, $depositUuid)
    {
        
    }

    /**
     * Get a deposit receipt from this URL.
     *
     * @Route("/cont-iri/{providerUuid}/{depositUuid}/edit", name="sword_reciept", requirements={
     *      "providerUuid": ".{36}",
     *      "depositUuid": ".{36}"
     * })
     * @Method({"GET"})
     */
    public function receiptAction(Request $request, $providerUuid, $depositUuid)
    {

    }

    /**
     * Fetch a representation of the deposit.
     *
     * @Route("/cont-iri/{providerUuid}/{depositUuid}", name="sword_view", requirements={
     *      "providerUuid": ".{36}",
     *      "depositUuid": ".{36}"
     * })
     * @Method({"GET"})
     */
    public function viewDepositAction(Request $request, $providerUuid, $depositUuid)
    {

    }

    /**
     * HTTP PUT to this URL to edit a deposit.
     *
     * @Route("/cont-iri/{providerUuid}/{depositUuid}/edit", name="sword_edit", requirements={
     *      "providerUuid": ".{36}",
     *      "depositUuid": ".{36}"
     * })
     * @Method({"PUT"})
     */
    public function editDepositAction(Request $request, $providerUuid, $depositUuid)
    {
        
    }

}
