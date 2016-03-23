<?php

namespace LOCKSSOMatic\CrudBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Util\Debug;
use Exception;
use LOCKSSOMatic\CrudBundle\Entity\Au;
use LOCKSSOMatic\CrudBundle\Entity\AuProperty;
use Monolog\Logger;
use Symfony\Component\Routing\Router;

class AuPropertyGenerator
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
        $this->logger->error("building prop {$key} for {$au->getId()}");
        $property = new AuProperty();
        $property->setAu($au);
        $property->setPropertyKey($key);
        $property->setPropertyValue($value);
        $property->setParent($parent);
        $this->em->persist($property);
        return $property;
    }
    
    public function generateSymbol(Au $au, $name) {
        $plugin = $au->getPlugin();
        if (!$plugin) {
            throw new Exception("Au requires plugin to generate $name.");
        }
        $property = $plugin->getProperty($name);
        if ($property === null) {
			$this->logger->error("{$plugin->getName()} is missing parameter {$name}.");
			return null;
        }
		
        $formatStr = null;
        $matches = array();
        $propertyValue = $property->getPropertyValue();
        if (preg_match('/^"([^"]*)"/', $propertyValue, $matches)) {
            $formatStr = $matches[1];
        } else {
            throw new Exception("$name property cannot be parsed: {$propertyValue}");
        }
		// substr/strlen skips the $formatstr part of the property
        $parts = preg_split('/, */', substr($propertyValue, strlen($formatStr)+2));
		$values = array();
        foreach (array_slice($parts, 1) as $parameterName) {
            $values[] = $au->getAuPropertyValue($parameterName, false);
        }
        $paramCount = preg_match_all('/%[a-zA-Z]/', $formatStr);
        if ($paramCount != count($values)) {
            throw new Exception("Wrong number of parameters for format string: {$formatStr}/{$paramCount} --" . print_r(array(
                $parts), true));
        }
        return vsprintf($formatStr, $values);
    }
    
    /**
     * Check each content item in the AU, and make sure that they all have 
     * the same definitional content properties.
     * 
     * @param Au $au
     */
    public function validateContent(Au $au) {
        $content = $au->getContent();
        if(count($content) === 0) {
            throw new Exception("AU must have content to generate properties.");
        }
        
        if(count($content) === 1) {
            return;
        }
        $plugin = $au->getPlugin();
        $definitional = $plugin->getDefinitionalProperties();
        foreach(array_slice($content, 1) as $c) {
            // compare each content item to the first one, looking for differences.
            foreach($definitional as $prop) {
                if($c->getContentPropertyValue($prop) !== $content[0]->getContentProperties()) {
                    throw new Exception("Content property mismatch in AU #{$au->getId()}");
                }
            }
        }
    }
    
    public function generateProperties(Au $au, $clear = false) {
        $this->validateContent($au);
        
        if($clear) {
            foreach($au->getAuProperties() as $prop) {
                $au->removeAuProperty($prop);
                $this->em->remove($prop);
            }
            $this->em->flush();
        }
        
        $rootName = str_replace('.', '', uniqid('lockssomatic', true));
        $content = $au->getContent()[0];

        $root = $this->buildProperty($au, $rootName);
        
		// config params are used to build other properties. So set them first.
        foreach ($au->getPlugin()->getDefinitionalProperties() as $index => $property) {
            $grouping = $this->buildProperty($au, 'param.' . ($index+1), null, $root);
            $this->buildProperty($au, 'key', $property, $grouping);
            $this->buildProperty($au, 'value', $content->getContentPropertyValue($property), $grouping);
        }
        
        foreach ($au->getPlugin()->getNonDefinitionalProperties() as $index => $property) {
			if($property === 'manifest_url') {
				$value = $this->router->generate('configs_manifest', array(
					'plnId' => $au->getPln()->getId(),
					'ownerId' => $au->getContentprovider()->getContentOwner()->getId(),
					'providerId' => $au->getContentprovider()->getId(),
					'auId' => $au->getId(),
				), Router::ABSOLUTE_URL);
			} else {
				$value = $content->getContentPropertyValue($property);
			}
            $grouping = $this->buildProperty($au, 'param.' . ($index+1), null, $root);
            $this->buildProperty($au, 'key', $property, $grouping);
            $this->buildProperty($au, 'value', $value, $grouping);
        }

        $this->buildProperty($au, 'journalTitle', $content->getContentPropertyValue('journalTitle'), $root);
        $this->buildProperty($au, 'title', 'LOCKSSOMatic AU ' . $content->getTitle() . ' ' . $content->getDeposit()->getTitle(), $root);
        $this->buildProperty($au, 'plugin', $au->getPlugin()->getPluginIdentifier(), $root);
        foreach ($content->getContentProperties() as $property) {
            $this->buildProperty($au, "attributes.pkppln." . $property->getPropertyKey(), $property->getPropertyValue(), $root);
        }

		$this->em->flush();
    }
}
