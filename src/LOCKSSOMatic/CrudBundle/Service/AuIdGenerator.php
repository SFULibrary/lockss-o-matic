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

    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }
    
    public function setRouter(Router $router) {
        $this->router = $router;
    }
	
	public function fromContent(Content $content) {
        $plugin = $content->getDeposit()->getContentProvider()->getPlugin();
        if ($plugin === null) {
            return null;
        }
        $pluginKey = str_replace('.', '|', $plugin->getPluginIdentifier());
        $auKey = '';
        $propNames = $plugin->getDefinitionalProperties();
        sort($propNames);

        foreach ($propNames as $name) {
            $auKey .= '&' . $name . '~' . $content->getContentPropertyValue(
                $name,
                true
            );
        }
		return $pluginKey . $auKey;
	}
	
	public function fromAu(Au $au) {
		$content = $au->getContent()->first();
		if($content === null) {
			return null;
		}
		return $this->fromContent($content);
	}

}
