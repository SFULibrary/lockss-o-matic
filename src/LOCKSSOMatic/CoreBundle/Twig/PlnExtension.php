<?php

namespace LOCKSSOMatic\CoreBundle\Twig;

use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use LOCKSSOMatic\UserBundle\Security\Services\Access;
use Symfony\Component\HttpFoundation\Session\Session;
use Twig_Extension;
use Twig_SimpleFunction;

class PlnExtension extends Twig_Extension {

    /**
     * @var Doctrine
     */
    private $doctrine;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var Access
     */
    private $access;

    public function __construct(Doctrine $doctrine, Session $session, Access $access)
    {
        $this->doctrine = $doctrine;
        $this->session = $session;
        $this->access = $access;
    }

    public function getFunctions()
    {
        return array(
            'plnList' => new Twig_SimpleFunction('plnList', array($this, 'plnList')),
            'currentPln' => new Twig_SimpleFunction('currentPln', array($this, 'currentPln')),
        );
    }

    public function plnList() {
        $em = $this->doctrine->getManager();
        $plns = array();
        foreach($em->getRepository('LOCKSSOMaticCrudBundle:Pln')->findAll() as $pln) {
            if($this->access->hasAccess('MONITOR', $pln)) {
                $plns[] = $pln;
            }
        }
        return $plns;
    }

    public function currentPln() {
        $plnId = $this->session->get('plnId');
        if(! $plnId) {
            return null;
        }
        $em = $this->doctrine->getManager();
        return $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($plnId);
    }

     public function getName()
    {
        return 'lom_plnsextension';
    }
}