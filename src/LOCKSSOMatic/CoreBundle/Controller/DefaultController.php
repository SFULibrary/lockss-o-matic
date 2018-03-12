<?php

namespace LOCKSSOMatic\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Default controller, handles requests that don't fit into any of the larger
 * controllers.
 */
class DefaultController extends Controller
{
    /**
     * Home page for LOCKSSOMatic.
     *
     * @Route("/", name="home")
     * @Template()
     */
    public function indexAction() {
        return array();
    }
}
