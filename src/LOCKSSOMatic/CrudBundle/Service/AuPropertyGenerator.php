<?php

/*
 * The MIT License
 *
 * Copyright 2014-2016. Michael Joyce <ubermichael@gmail.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace LOCKSSOMatic\CrudBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use LOCKSSOMatic\CrudBundle\Entity\Au;
use LOCKSSOMatic\CrudBundle\Entity\AuProperty;
use Monolog\Logger;
use Symfony\Component\Routing\Router;

/**
 * Build a property on an AU.
 */
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

    /**
     * Set the logger.
     * 
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Set the entity manager through a poorly named method.
     * 
     * @param Registry $registry
     */
    public function setRegistry(Registry $registry)
    {
        $this->em = $registry->getManager();
    }

    /**
     * Set the page router.
     * 
     * @param Router $router
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Build a property.
     * 
     * @param Au $au
     * @param string $key
     * @param string $value
     * @param AuProperty $parent
     * @return AuProperty
     */
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
     * So, LOCKSS properties can be C-style vsprintf strings, but with 
     * named parameters. The entire thing is encoded in a plugin's XML file as
     * single string. It's complicated.
     * 
     * @param AU $au
     * @param string $name
     * @param string $propertyValue
     * @return string
     * @throws Exception
     */
    private function generate(AU $au, $name, $propertyValue)
    {
        $matches = array();
        if (preg_match('/^"([^"]*)"/', $propertyValue, $matches)) {
            $formatStr = $matches[1];
        } else {
            throw new Exception("$name property cannot be parsed: {$propertyValue}");
        }
        // substr/strlen skips the $formatstr part of the property
        $parts = preg_split('/, */', substr($propertyValue, strlen($formatStr) + 2));
        $values = array();
        foreach (array_slice($parts, 1) as $parameterName) {
            $values[] = $au->getAuPropertyValue($parameterName, false);
        }
        $paramCount = preg_match_all('/%[a-zA-Z]/', $formatStr);
        if ($paramCount != count($values)) {
            throw new Exception("Wrong number of parameters for format string: {$formatStr}/{$paramCount} --".print_r(array(
                $parts, ), true));
        }

        return vsprintf($formatStr, $values);
    }

    /**
     * Generate a symbol, according to a LOCKSS vstring-like property.
     * 
     * @param Au $au
     * @param string $name
     * @return string|array
     * @throws Exception
     */
    public function generateSymbol(Au $au, $name)
    {
        $plugin = $au->getPlugin();
        if (!$plugin) {
            throw new Exception("Au requires plugin to generate $name.");
        }
        $property = $plugin->getProperty($name);
        if ($property === null) {
            $this->logger->error("{$plugin->getName()} is missing parameter {$name}.");

            return;
        }
        if (!$property->isList()) {
            return $this->generate($au, $name, $property->getPropertyValue());
        }
        $values = array();
        foreach ($property->getPropertyValue() as $v) {
            $values[] = $this->generate($au, $name, $v);
        }

        return $values;
    }

    /**
     * Check each content item in the AU, and make sure that they all have 
     * the same definitional content properties.
     * 
     * @param Au $au
     */
    public function validateContent(Au $au)
    {
        $content = $au->getContent();
        if (count($content) === 0) {
            throw new Exception('AU must have content to generate properties.');
        }

        if (count($content) === 1) {
            return;
        }
        $plugin = $au->getPlugin();
        $definitional = $plugin->getDefinitionalProperties();
        $baseContent = $content[0];
        foreach (array_slice($content->toArray(), 1) as $c) {
            // compare each content item to the first one, looking for differences.
            foreach ($definitional as $prop) {
                if ($c->getContentPropertyValue($prop) !== $baseContent->getContentPropertyValue($prop)) {
                    throw new Exception("Content property mismatch in AU #{$au->getId()}: "
                    ."content {$c->getId()} {$prop} is ".$c->getContentPropertyValue($prop)
                    ."Expected {$baseContent->getContentPropertyValue($prop)} ");
                }
            }
        }
    }
    
    /**
     * Generate all of the properties for an AU, optionally clearing all
     * the previously set properties.
     * 
     * @param Au $au
     * @param boolean $clear
     */
    public function generateProperties(Au $au, $clear = false)
    {
        $this->validateContent($au);

        if ($clear) {
            foreach ($au->getAuProperties() as $prop) {
                $au->removeAuProperty($prop);
                $this->em->remove($prop);
            }
            $this->em->flush();
        }

        $rootName = str_replace('.', '', uniqid('lockssomatic', true));
        $content = $au->getContent()[0];

        $root = $this->buildProperty($au, $rootName);

        // config params are used to build other properties. So set them first.
        $offset = 0;

        $properties = array_merge(
            $au->getPlugin()->getDefinitionalProperties(),
            $au->getPlugin()->getNonDefinitionalProperties()
        );

        foreach ($properties as $index => $property) {
            if ($property === 'manifest_url') {
                $value = $this->router->generate('configs_manifest', array(
                    'plnId' => $au->getPln()->getId(),
                    'ownerId' => $au->getContentprovider()->getContentOwner()->getId(),
                    'providerId' => $au->getContentprovider()->getId(),
                    'auId' => $au->getId(),
                ), Router::ABSOLUTE_URL);
            } else {
                $value = $content->getContentPropertyValue($property);
            }
            $grouping = $this->buildProperty($au, 'param.'.($index + 1 + $offset), null, $root);
            $this->buildProperty($au, 'key', $property, $grouping);
            $this->buildProperty($au, 'value', $value, $grouping);
        }

        $this->buildProperty($au, 'journalTitle', $content->getContentPropertyValue('journalTitle'), $root);
        $this->buildProperty($au, 'title', 'LOCKSSOMatic AU '.$content->getTitle().' '.$content->getDeposit()->getTitle(), $root);
        $this->buildProperty($au, 'plugin', $au->getPlugin()->getPluginIdentifier(), $root);
        $this->buildProperty($au, 'attributes.publisher', $content->getContentPropertyValue('publisher'), $root);
        foreach ($content->getContentProperties() as $property) {
            $value = $property->getPropertyValue();
            if (is_array($value)) {
                $this->logger->warn("AU {$au->getId()} has unsupported property value list {$property->getPropertyKey()}");
                continue;
            }
            $this->buildProperty($au, 'attributes.pkppln.'.$property->getPropertyKey(), $value, $root);
        }

        $this->em->flush();
    }
}
