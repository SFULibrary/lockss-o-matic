<?php

namespace LOCKSSOMatic\CrudBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CrudBundle\Entity\Au;
use LOCKSSOMatic\CrudBundle\Entity\AuProperty;
use LOCKSSOMatic\CrudBundle\Entity\Content;
use Monolog\Logger;
use Symfony\Component\Routing\Router;

class AuBuilder
{

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var ObjectManager
     */
    private $em;
    
    /**
     * @var Router
     */
    private $router;
    
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }
    
    public function setRegistry(Registry $registry)
    {
        $this->em = $registry->getManager();
    }
    
    public function setRouter(Router $router) {
        $this->router = $router;
    }

    public function buildProperty(Au $au, $key, $value = null, AuProperty $parent = null)
    {
        $property = new AuProperty();
        $property->setAu($au);
        $property->setPropertyKey($key);
        $property->setPropertyValue($value);
        $property->setParent($parent);
        $this->em->persist($property);
        return $property;
    }
    
    /**
     * Build an AU for the content item.
     *
     * @return Au
     * @param Content $content
     */
    public function fromContent(Content $content)
    {
        $au = new Au();
        $provider = $content->getDeposit()->getContentProvider();
        $owner = $provider->getContentOwner();

        $au->setContentprovider($provider);
        $au->setPln($provider->getPln());
        $au->setPlugin($provider->getPlugin());

        $root = $this->buildProperty($au, 'lockssomatic' . uniqid('', true));
        $this->buildProperty($au, 'journalTitle', $content->getContentPropertyValue('journalTitle'), $root);
        $this->buildProperty($au, 'title', 'LOCKSSOMatic AU ' . $content->getTitle() . ' ' . $content->getDeposit()->getTitle(), $root);
        $this->buildProperty($au, 'plugin', $au->getPlugin()->getPluginIdentifier(), $root);

        foreach ($au->getPlugin()->getDefinitionalProperties() as $index => $property) {
            $grouping = $this->buildProperty($au, 'param.d.' . ($index+1), null, $root);
            $this->buildProperty($au, 'key', $property, $grouping);
            $this->buildProperty($au, 'value', $content->getContentPropertyValue($property), $grouping);
        }
        foreach ($au->getPlugin()->getNonDefinitionalProperties() as $index => $property) {
            $grouping = $this->buildProperty($au, 'param.n.' . ($index+1), null, $root);
            $this->buildProperty($au, 'key', $property, $grouping);
            if($property === 'manifest_url') {
                $url = $this->router->generate(
                    'configs_manifest', 
                    array(
                        'plnId' => $au->getPln()->getId(),
                        'ownerId' => $owner->getId(),
                        'providerId' => $provider->getId(),
                        'filename' => "titledb.xml",
                    ), 
                    Router::ABSOLUTE_URL
                );
                $this->buildProperty($au, 'value', $url, $grouping);
            } else {
                $this->buildProperty($au, 'value', $content->getContentPropertyValue($property), $grouping);
            }
        }

        $permissionGroup = $this->buildProperty($au, 'param.permission', null, $root);
        $this->buildProperty($au, 'key', 'permission_url', $permissionGroup);
        $this->buildProperty($au, 'value', $provider->getPermissionurl(), $permissionGroup);

        foreach ($content->getContentProperties() as $property) {
            $this->buildProperty($au, "attributes.pkppln." . $property->getPropertyKey(), $property->getPropertyValue(), $root);
        }
        $this->em->persist($au);
        $this->em->flush();
        $this->em->refresh($au);
        return $au;
    }
}
