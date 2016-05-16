<?php

namespace LOCKSSOMatic\CoreBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use LOCKSSOMatic\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Builder implements ContainerAwareInterface
{

    /**
     *
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return User
     */
    protected function getUser()
    {
        if ($this->container) {
            return $this->container->get('security.token_storage')->getToken()->getUser();
        } 
        return null;
    }

    /**
     * @param FactoryInterface $factory
     * @param array $options
     * @return ItemInterface
     */
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav');

        $menu->addChild('Home', array('route' => 'home'));

        $menu->addChild('LOCKSS', array('uri' => '#'));
        $menu['LOCKSS']->setLinkAttribute('class', 'dropdown-toggle');
        $menu['LOCKSS']->setLinkAttribute('data-toggle', 'dropdown');
        $menu['LOCKSS']->setChildrenAttribute('class', 'dropdown-menu');
        
        $menu['LOCKSS']->addChild('Owners', array('route' => 'contentowner'));
        $menu['LOCKSS']->addChild('Providers', array('route' => 'contentprovider'));
        $menu['LOCKSS']->addChild('Networks', array('route' => 'pln'));
        $menu['LOCKSS']->addChild('Plugins', array('route' => 'plugin'));

        $menu->addChild('networks', array('uri' => '#', 'label' => 'Networks'));
        $menu['networks']->setLinkAttribute('class', 'dropdown-toggle');
        $menu['networks']->setLinkAttribute('data-toggle', 'dropdown');
        $menu['networks']->setChildrenAttribute('class', 'dropdown-menu');
        
        $menu['networks']->addChild('pkp', array('uri' => '#', 'label' => 'PKP PLN'));        
        $menu['networks']['pkp']->setAttribute('class', 'dropdown-submenu');
        $menu['networks']['pkp']->setChildrenAttribute('class', 'dropdown-menu');
        $menu['networks']['pkp']->setLinkAttribute('data-toggle', 'dropdown');
        $menu['networks']['pkp']->setLinkAttribute('class', 'dropdown-toggle');
        
        $menu['networks']['pkp']->addChild('Archival Units', array('route' => 'au', 'routeParameters' => array('plnId' => 1)));
        $menu['networks']['pkp']->addChild('Boxes', array('route' => 'box', 'routeParameters' => array('plnId' => 1)));
        $menu['networks']['pkp']->addChild('Content', array('route' => 'content', 'routeParameters' => array('plnId' => 1)));
        $menu['networks']['pkp']->addChild('Deposits', array('route' => 'deposit', 'routeParameters' => array('plnId' => 1)));
        return $menu;
    }

    public function userMenu(FactoryInterface $factory, array $options)
    {
        $user = $this->getUser();

        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav navbar-right');
        
        $menu->addChild('user', array('uri' => '#', 'label' => $user->getEmail()));
        $menu['user']->setLinkAttribute('class', 'dropdown-toggle');
        $menu['user']->setLinkAttribute('data-toggle', 'dropdown');
        $menu['user']->setChildrenAttribute('class', 'dropdown-menu');
        
        $menu['user']->addChild('Profile', array('route' => 'fos_user_profile_show'));
        $menu['user']->addChild('Change password', array('route' => 'fos_user_change_password'));
        $menu['user']->addChild('Logout', array('route' => 'fos_user_security_logout'));
        
        
        return $menu;
    }

}
