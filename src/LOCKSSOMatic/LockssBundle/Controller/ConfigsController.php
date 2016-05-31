<?php

namespace LOCKSSOMatic\LockssBundle\Controller;

use LOCKSSOMatic\CoreBundle\Services\FilePaths;
use LOCKSSOMatic\CrudBundle\Entity\Box;
use LOCKSSOMatic\CrudBundle\Entity\Pln;
use Monolog\Logger;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/plnconfigs")
 */
class ConfigsController extends Controller
{
    /**
     * @var Logger
     */
    private $logger;
    
	/**
	 * @var FilePaths
	 */
	private $fp;
	
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->logger = $this->get('monolog.logger.lockss');
		$this->fp = $container->get('lom.filepaths');
    }
    
	private function checkIp(Request $request, Pln $pln) {
        $ip = $request->getClientIp();
        $allowed = array_map(function(Box $box) {return $box->getIpAddress();}, $pln->getBoxes()->toArray());
        $env = $this->container->get('kernel')->getEnvironment();
        if ($env === 'dev' || $env === 'test') {
            $allowed[] = '127.0.0.1';
        }
        $allowed = array_merge($allowed, $this->container->getParameter('lockss_allowed_ips'));
        IpUtils::checkIp($ip, $allowed);
        if(! IpUtils::checkIp($ip, $allowed)) {
			$this->logger->critical("Client IP {$ip} is not authorized for {$pln->getName()}({$pln->getId()}).");
            throw new AccessDeniedHttpException("Client IP {$ip} is not authorized for this PLN.");
        } else {
            $this->logger->notice("LOCKSS - {$ip} allowed for {$pln->getName()}({$pln->getId()}).");        
        }
    }
    
    /**
     * @Route(
     *  "/{plnId}/properties/lockss.{_format}", 
     *  name="configs_lockss",
     *  requirements={
     *      "_format": "xml"
     *  }
     * )
     * 
     * @param Request $request
     * @param string $plnId
     */
    public function lockssAction(Request $request, $plnId) {
		$this->logger->notice("lockss.xml - {$plnId} - {$request->getClientIp()}");
        $em = $this->getDoctrine()->getManager();
        $pln = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($plnId);
        $this->checkIp($request, $pln);		
		$lockssPath = $this->fp->getLockssXmlFile($pln);
        if(! file_exists($lockssPath)) {
            throw new NotFoundHttpException("The requested file does not exist.");
        }
        return new BinaryFileResponse($lockssPath, 200, array(
            'Content-Type' => 'text/xml'
        ));
    }
    
    /**
     * @Route("/{plnId}/titledbs/{ownerId}/{providerId}/{filename}", name="configs_titledb")
     */
    public function titleDbAction(Request $request, $plnId, $ownerId, $providerId, $filename) {
        $this->logger->notice("titledb - {$plnId} - {$request->getClientIp()} - {$ownerId} - {$providerId} - {$filename}");
        $em = $this->getDoctrine()->getManager();
        $pln = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($plnId);
        $this->checkIp($request, $pln);

		$provider = $em->getRepository('LOCKSSOMaticCrudBundle:ContentProvider')->find($providerId);
		$titleDbDir = $this->fp->getTitleDbDir($pln, $provider);
		$titleDbFile = $titleDbDir . '/' . $filename;
        if(! file_exists($titleDbFile)) {
            throw new NotFoundHttpException("The requested file {$filename} does not exist.");
        }
        return new BinaryFileResponse($titleDbFile);
    }
    
    /**
     * @Route("/{plnId}/manifests/{ownerId}/{providerId}/manifest_{auId}.html", name="configs_manifest")
     */
    public function manifestAction(Request $request, $plnId, $ownerId, $providerId, $auId) {
        $this->logger->notice("manifest - {$plnId} - {$request->getClientIp()} - {$ownerId} - {$providerId} - {$auId}");
        $em = $this->getDoctrine()->getManager();
        $pln = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($plnId);
        $this->checkIp($request, $pln);
        
		$au = $em->getRepository('LOCKSSOMaticCrudBundle:Au')->find($auId);
        $manifestPath = $this->fp->getManifestPath($au);
        if(! file_exists($manifestPath)) {
            throw new NotFoundHttpException("The requested AU manifest $manifestPath does not exist.");
        }
        return new BinaryFileResponse($manifestPath);
    }
	
	/**
	 * @Route("/{plnId}/plugins/lockss.keystore", name="configs_plugin_keystore")
	 * @Method("GET")
	 * 
	 * @param Request $request
	 * @param type $plnId
	 */
	public function keystoreAction(Request $request, $plnId) {
        $this->logger->notice("keystore - {$plnId} - {$request->getClientIp()}");
        $em = $this->getDoctrine()->getManager();
        $pln = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($plnId);
        $this->checkIp($request, $pln);
		$keystore = $pln->getKeystore();
		if(! $keystore) {
            throw new NotFoundHttpException("The requested keystore does not exist.");
		}
        $webPath =  $this->container->get('kernel')->getRootDir() . '/../data/plnconfigs';
		$pluginsDir = $webPath . '/' . $plnId . '/plugins';
		$keystorePath = $pluginsDir . '/lockss.keystore';
		if(! file_exists($keystorePath)) {
            throw new NotFoundHttpException("The requested keystore does not exist.");
		}
		return new BinaryFileResponse($keystorePath);
	}
    
    /**
     * @Route("/{plnId}/plugins/index.html", name="configs_plugin_list")
     * @Route("/{plnId}/plugins/")
     * @Route("/{plnId}/plugins")
	 * @Method("GET")
     * @Template()
     * 
     * @param Request $request
     * @param string $plnId
     */
    public function pluginListAction(Request $request, $plnId) {
        $this->logger->notice("pluginList - {$plnId} - {$request->getClientIp()}");
        $em = $this->getDoctrine()->getManager();
        $pln = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($plnId);
        $this->checkIp($request, $pln);
        return array(
            'pln' => $pln
        );
    }
    
    /**
     * @Route("/{plnId}/plugins/{filename}", name="configs_plugin")
	 * @Method("GET")
     * @param Request $request
     * @param $plnId
     * @param $filename
     */
    public function pluginAction(Request $request, $plnId, $filename) {
        $this->logger->notice("plugin - {$plnId} - {$request->getClientIp()} - {$filename}");
        $em = $this->getDoctrine()->getManager();
        $pln = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($plnId);
        $this->checkIp($request, $pln);

        $webPath =  $this->container->get('kernel')->getRootDir() . '/../data/plnconfigs';        
        $pluginPath = "{$webPath}/{$plnId}/plugins/{$filename}";
        if(! file_exists($pluginPath)) {
            throw new NotFoundHttpException("The requested file {$filename} does not exist.");
        }
        return new BinaryFileResponse($pluginPath);
    }
}
