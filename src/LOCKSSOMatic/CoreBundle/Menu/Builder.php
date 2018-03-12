<?php

namespace LOCKSSOMatic\CoreBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use LOCKSSOMatic\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Menu builder which generates the menus for the application.
 */
class Builder implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Current user or null if the user isn't authenticated.
     *
     * @var User|null
     */
    private $user;

    /**
     * Set the DI container.
     *
     * @param ContainerInterface $container The container.
     */
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    /**
     * Set the current user.
     *
     * @param User $user The user for the menu.
     */
    public function setUser(User $user) {
        $this->user = $user;
    }

    /**
     * Get the user the menu is being built for, or null if the user isn't
     * authenticated.
     *
     * @return User|null
     */
    protected function getUser() {
        if ($this->user instanceof User) {
            return $this->user;
        }
        if ($this->container) {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            if ($user instanceof User) {
                return $user;
            }
        }
        return null;
    }

    /**
     * Build the main menu for the application.
     *
     * @param FactoryInterface $factory The menu factory.
     *
     * @return ItemInterface
     */
    public function mainMenu(FactoryInterface $factory) {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav');

        $menu->addChild('Home', array('route' => 'home'));

        $user = $this->getUser();
        if ($user === null) {
            return $menu;
        }

        $menu->addChild('LOCKSS', array('uri' => '#', 'label' => 'LOCKSS'));
        $menu['LOCKSS']->setAttribute('dropdown', true);
        $menu['LOCKSS']->setLinkAttribute('class', 'dropdown-toggle');
        $menu['LOCKSS']->setLinkAttribute('data-toggle', 'dropdown');
        $menu['LOCKSS']->setChildrenAttribute('class', 'dropdown-menu');

        $menu['LOCKSS']->addChild('Owners', array('route' => 'contentowner'));
        $menu['LOCKSS']->addChild('Providers', array('route' => 'contentprovider'));
        $menu['LOCKSS']->addChild('Networks', array('route' => 'pln'));
        $menu['LOCKSS']->addChild('Plugins', array('route' => 'plugin'));

        $menu->addChild('networks', array('uri' => '#', 'label' => 'Networks'));
        $menu['networks']->setAttribute('dropdown', true);
        $menu['networks']->setLinkAttribute('class', 'dropdown-toggle');
        $menu['networks']->setLinkAttribute('data-toggle', 'dropdown');
        $menu['networks']->setChildrenAttribute('class', 'dropdown-menu');

        $menu['networks']->addChild('All networks', array('route' => 'pln'))->setAttribute('divider_append', true);

        $access = $this->container->get('lom.access');
        $em = $this->container->get('doctrine')->getManager();
        $networks = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->findAll();

        foreach ($networks as $pln) {
            if (!$access->hasAccess('PLN_MONITOR', $pln)) {
                continue;
            }
            $mid = 'network_'.$pln->getId(); // only for grouping.
            $menu['networks']->addChild($mid, array('uri' => '#', 'label' => $pln->getName()));
            $menu['networks'][$mid]->setAttribute('class', 'dropdown-submenu');
            $menu['networks'][$mid]->setChildrenAttribute('class', 'dropdown-menu');
            $menu['networks'][$mid]->setLinkAttribute('data-toggle', 'dropdown');
            $menu['networks'][$mid]->setLinkAttribute('class', 'dropdown-toggle');

            $menu['networks'][$mid]->addChild($pln->getName(), array('route' => 'pln_show', 'routeParameters' => array('id' => $pln->getId())));
            $menu['networks'][$mid]->addChild('Archival Units', array('route' => 'au', 'routeParameters' => array('plnId' => $pln->getId())));
            $menu['networks'][$mid]->addChild('Boxes', array('route' => 'box', 'routeParameters' => array('plnId' => $pln->getId())));
            $menu['networks'][$mid]->addChild('Content', array('route' => 'content', 'routeParameters' => array('plnId' => $pln->getId())));
            $menu['networks'][$mid]->addChild('Deposits', array('route' => 'deposit', 'routeParameters' => array('plnId' => $pln->getId())));
        }
        if ($access->hasAccess('ROLE_ADMIN')) {
            $menu->addChild('admin', array('uri' => '#', 'label' => 'Admin'));
            $menu['admin']->setAttribute('dropdown', true);
            $menu['admin']->setLinkAttribute('class', 'dropdown-toggle');
            $menu['admin']->setLinkAttribute('data-toggle', 'dropdown');
            $menu['admin']->setChildrenAttribute('class', 'dropdown-menu');

            $menu['admin']->addChild('Users', array('route' => 'admin_user'));
        }

        return $menu;
    }

    /**
     * Build the user menu (or a simple login link) for the application.
     *
     * @param FactoryInterface $factory The menu factory.
     *
     * @return ItemInterface
     */
    public function userMenu(FactoryInterface $factory) {
        $user = $this->getUser();

        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav navbar-right');

        if ($user !== null) {
            $menu->addChild('user', array('uri' => '#', 'label' => $user->getEmail()));
            $menu['user']->setAttribute('dropdown', true);
            $menu['user']->setLinkAttribute('class', 'dropdown-toggle');
            $menu['user']->setLinkAttribute('data-toggle', 'dropdown');
            $menu['user']->setChildrenAttribute('class', 'dropdown-menu');

            $menu['user']->addChild('Profile', array('route' => 'fos_user_profile_show'));
            $menu['user']->addChild('Change password', array('route' => 'fos_user_change_password'));
            $menu['user']->addChild('Logout', array('route' => 'fos_user_security_logout'));
        } else {
            $menu->addChild('Login', array('route' => 'fos_user_security_login'));
        }

        return $menu;
    }
}
