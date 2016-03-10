<?php

namespace LOCKSSOMatic\ImportExportBundle\Controller;

use LOCKSSOMatic\CrudBundle\Entity\Box;
use LOCKSSOMatic\CrudBundle\Entity\Pln;
use Monolog\Logger;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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
    
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->logger = $this->get('monolog.logger.lockss');
    }
    
	private function checkIp(Request $request, Pln $pln) {
        $ip = $request->getClientIp();
        $allowed = array_map(function(Box $box) {return $box->getIpAddress();}, $pln->getBoxes()->toArray());
        $env = $this->container->get('kernel')->getEnvironment();
		$logger = $this->get('monolog.logger.lockss');
        if ($env === 'dev' || $env === 'test') {
            $allowed[] = '127.0.0.1';
        }
        if(! in_array($ip, $allowed)) {
			$logger->critical("Client IP {$ip} is not authorized for {$pln->getName()}({$pln->getId()}).");
            throw new AccessDeniedHttpException("Client IP {$ip} is not authorized for this PLN.");
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
        $webPath =  $this->container->get('kernel')->getRootDir() . '/../data/plnconfigs';
        $lockssPath = "{$webPath}/{$plnId}/lockss.xml";
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
        
        $webPath =  $this->container->get('kernel')->getRootDir() . '/../data/plnconfigs';
        $titleDbPath = "{$webPath}/{$plnId}/titledbs/{$ownerId}/{$providerId}/{$filename}";
        if(! file_exists($titleDbPath)) {
            throw new NotFoundHttpException("The requested file {$filename} does not exist.");
        }
        return new BinaryFileResponse($titleDbPath);
    }
    
    /**
     * @Route("/{plnId}/manifests/{ownerId}/{providerId}/{filename}", name="configs_manifest")
     */
    public function manifestAction(Request $request, $ownerId, $plnId, $providerId, $filename) {
        $this->logger->notice("manifest - {$plnId} - {$request->getClientIp()} - {$ownerId} - {$providerId} - {$filename}");
        $em = $this->getDoctrine()->getManager();
        $pln = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($plnId);
        $this->checkIp($request, $pln);
        
        $webPath =  $this->container->get('kernel')->getRootDir() . '/../data/plnconfigs';
        $manifestPath = "{$webPath}/{$plnId}/manifests/{$ownerId}/{$providerId}/{$filename}";
        if(! file_exists($manifestPath)) {
            throw new NotFoundHttpException("The requested file {$filename} does not exist.");
        }
        return new BinaryFileResponse($manifestPath);
    }
    
    /**
     * @Route("/{plnId}/plugins/index.html", name="configs_plugin_list")
     * @Route("/{plnId}/plugins/")
     * @Route("/{plnId}/plugins")
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
