<?php

namespace LOCKSSOMatic\CrudBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Exception;
use LOCKSSOMatic\CrudBundle\Entity\Au;

class AuAttributeGenerator
{

    protected function generateAuid(Au $au)
    {
        $plugin = $au->getPlugin();
        if ($plugin === null) {
            return;
        }
        $pluginKey = str_replace('.', '|', $plugin->getPluginIdentifier());
        $auKey = '';
        $propNames = $plugin->getDefinitionalProperties();
        sort($propNames);

        foreach ($propNames as $name) {
            $propertyValue = $au->getAuProperty($name, true);
            $auKey .= "&{$name}~{$propertyValue}";
        }
        return $pluginKey . $auKey;
    }

    // should this be in a service? Adds logging options.
    // make this more testable.
    protected function generateSymbol(Au $au, $name)
    {
        $plugin = $au->getPlugin();
        if (!$plugin) {
            throw new Exception("Au requires plugin to generate $name.");
        }
        $property = $plugin->getProperty($name);
        if (!$property) {
            throw new Exception("$name plugin property is required.");
        }
        $formatStr = null;
        $matches = array();
        if (preg_match('/^"([^"]*)"/', $property, $matches)) {
            $formatStr = $matches[1];
        } else {
            throw new Exception("$name property cannot be parsed: {$property}");
        }
        // substr/strlen skips the $formatstr part of the property
        $parts = preg_split('/, */', substr($property, strlen($formatStr)+2));
        $values = array();
        foreach (array_slice($parts, 1) as $parameterName) {
            $values[] = $au->getAuProperty($parameterName, false);
        }
        $paramCount = preg_match_all('/%[a-zA-Z]/', $formatStr);
        if ($paramCount != count($values)) {
            throw new Exception("Wrong number of parameters for format string: {$formatStr}/{$paramCount} --" . print_r(array(
                $parts), true));
        }
        return vsprintf($formatStr, $values);
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof Au) {
            return;
        }
        $entity->setAuStartUrl($this->generateSymbol($entity, 'au_start_url'));
        $entity->setAuName($this->generateSymbol($entity, 'au_name'));
        $entity->setAuid($this->generateAuid($entity));
    }
}
