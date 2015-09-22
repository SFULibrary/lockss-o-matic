<?php

namespace LOCKSSOMatic\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/api/resolve", name="resolve_host")
     * @param Request $request
     */
    public function resolveHostNameAction(Request $request) {
        $hostname = $request->query->get('hostname');
        $ip = gethostbyname($hostname);
        $response = new Response(json_encode(array('hostname' => $hostname, 'address' => $ip)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
