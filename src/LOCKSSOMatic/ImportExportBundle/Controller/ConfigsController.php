<?php

namespace LOCKSSOMatic\ImportExportBundle\Controller;

use LOCKSSOMatic\CrudBundle\Entity\Box;
use LOCKSSOMatic\CrudBundle\Entity\Pln;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/plnconfigs")
 */
class ConfigsController extends Controller
{

    private function checkIp(Request $request, Pln $pln) {
        $ip = $request->getClientIp();
        $allowed = array_map(function(Box $box) {return $box->getIpAddress();}, $pln->getBoxes()->toArray());
        $env = $this->container->get('kernel')->getEnvironment();
        if ($env === 'dev' || $env === 'test') {
            $allowed[] = '127.0.0.1';
        }
        if( ! in_array($ip, $allowed)) {
            throw new AccessDeniedException("Client IP {$ip} is not authorized for this PLN.");
        }
    }
    
    private function updatePeerList(Pln $pln) {
        $boxes = $pln->getBoxes();
        $boxList = array();
        foreach ($boxes as $box) {
            $boxList[] = "{$box->getProtocol()}:[{$box->getIpAddress()}]:{$box->getPort()}";
        }
        $boxProp = $pln->getProperty('id.initialV3PeerList');
        $boxProp->setPropertyValue($boxList);
    }
    
    private function updatePluginRegistryList(Pln $pln) {
        $pluginUrlList = array(
            $this->generateUrl(
                'configs_plugin_list', 
                array('plnId' => $pln->getId()),
                UrlGeneratorInterface::ABSOLUTE_URL),
        );
        $pluginProp = $pln->getProperty('plugin.registries');
        $pluginProp->setPropertyValue($pluginUrlList);        
    }
    
    private function updateTitleDbs(Pln $pln) {
        $urls = array();
        
        $limit = $this->container->getParameter('lockss_aus_per_titledb');
        
        foreach($pln->getContentProviders() as $provider) {
            $auCount = $provider->countAus();
            if($auCount === 0) {
                continue;
            }
            $titleDbFiles = ceil($auCount / $limit);
            $digits = ceil(log10($titleDbFiles));
            
            for($i = 1; $i <= $titleDbFiles; $i++) {
                $urls[] = $this->generateUrl('configs_titledb', array(
                    'plnId' => $pln->getId(),
                    'ownerId' => $provider->getContentOwner()->getId(),
                    'providerId' => $provider->getId(),
                    'filename' => sprintf("titledb_%0{$digits}d.xml", $i)
                ), 
                UrlGeneratorInterface::ABSOLUTE_URL
                );
            }
        }
        
        $titleDbProp = $pln->getProperty('titleDbs');
        $titleDbProp->setPropertyValue($urls);
    }
    
    /**
     * @Route(
     *  "/{plnId}/properties/lockss.{_format}", 
     *  name="configs_lockss",
     *  requirements={
     *      "_format": "xml"
     *  }
     * )
     * @Template()
     * 
     * @param Request $request
     * @param string $plnId
     */
    public function lockssAction(Request $request, $plnId) {
        $em = $this->getDoctrine()->getManager();
        $pln = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($plnId);
        $this->checkIp($request, $pln);

        $this->updatePeerList($pln);
        $this->updatePluginRegistryList($pln);
        $this->updateTitleDbs($pln);
        
        $em->flush();
        return array(
            'pln' => $pln
        );
    }
    
    /**
     * @Route("/{plnId}/titledbs/{ownerId}/{providerId}/{filename}", name="configs_titledb")
     */
    public function titleDbAction(Request $request, $plnId, $ownerId, $providerId, $filename) {
        $em = $this->getDoctrine()->getManager();
        $pln = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($plnId);
        $this->checkIp($request, $pln);
        
        $webPath =  $this->container->get('kernel')->getRootDir() . '/../data/plnconfigs';
        $titleDbPath = "{$webPath}/{$plnId}/titledbs/{$ownerId}/{$providerId}/{$filename}";
        if( ! file_exists($titleDbPath)) {
            throw new NotFoundHttpException("The requested file {$filename} does not exist.");
        }
        return new BinaryFileResponse($titleDbPath);
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
        $em = $this->getDoctrine()->getManager();
        $pln = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($plnId);
        $this->checkIp($request, $pln);

        $webPath =  $this->container->get('kernel')->getRootDir() . '/../data/plnconfigs';        
        $pluginPath = "{$webPath}/{$plnId}/plugins/{$filename}";
        if( ! file_exists($pluginPath)) {
            throw new NotFoundHttpException("The requested file {$filename} does not exist.");
        }
        return new BinaryFileResponse($pluginPath);
    }
}
