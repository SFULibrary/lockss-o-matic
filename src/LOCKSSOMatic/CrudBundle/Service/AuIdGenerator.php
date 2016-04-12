<?php

namespace LOCKSSOMatic\CrudBundle\Service;

use LOCKSSOMatic\CrudBundle\Entity\Au;
use LOCKSSOMatic\CrudBundle\Entity\Content;
use Monolog\Logger;
use Symfony\Component\Routing\Router;

class AuIdGenerator {

	/**
	 * @var Logger
	 */
	private $logger;

	/**
	 * @var Router
	 */
	private $router;

    //  Array (     
    //      [ca.ca.sfu.lib.plugin.pkppln.PkpPlnPlugin] => Array (
    //          [0] => lom_manifest_url
    //          [1] => lom_permission_url         
    //      )
    //  ) 
    private $nondefinitionalCPDs;

    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }
    
    public function setRouter(Router $router) {
        $this->router = $router;
    }
    
    public function setNondefinitionalCPDs($data) {
        $this->nondefinitionalCPDs = $data;
    }
	
	public function fromContent(Content $content, $lockssAuid = true) {
        $plugin = $content->getDeposit()->getContentProvider()->getPlugin();
        if ($plugin === null) {
            return null;
        }
        $pluginId = $plugin->getPluginIdentifier();
        $pluginKey = str_replace('.', '|', $pluginId);
        $auKey = '';
        $propNames = $plugin->getDefinitionalProperties();
        sort($propNames);

        foreach ($propNames as $name) {
            if( ! $lockssAuid && 
                array_key_exists($pluginId, $this->nondefinitionalCPDs) &&
                in_array($name, $this->nondefinitionalCPDs[$pluginId])) {
                continue;
            }
            $auKey .= '&' . $name . '~' . $content->getContentPropertyValue(
                $name,
                true
            );
        }
        $id = $pluginKey . $auKey;
        $this->logger->critical('winnder: ' . $id);
        return $id;
	}
	
	public function fromAu(Au $au, $lockssAuid = true) {
		$content = $au->getContent()->first();
		if($content === null) {
			return null;
		}
		return $this->fromContent($content, $lockssAuid);
	}
}
