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

use LOCKSSOMatic\CrudBundle\Entity\Au;
use LOCKSSOMatic\CrudBundle\Entity\Content;
use Monolog\Logger;
use Symfony\Component\Routing\Router;

/**
 * Generate a LOCKSS AuId.
 */
class AuIdGenerator
{
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
    /**
     * List of non-definitional ConfigParamDescr from the parameters.yml file. This
     * list overrides the plugin XML file but only sometimes.
     *
     * @var type 
     */
    private $nondefinitionalCPDs;

    /**
     * Set a logger.
     * 
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Set a pager router.
     * 
     * @param Router $router
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Set the non-definitional CPDs.
     * 
     * @param array $data
     */
    public function setNondefinitionalCPDs($data)
    {
        $this->nondefinitionalCPDs = $data;
    }

    /**
     * Generate an AUid from a piece of content.
     * 
     * @param Content $content
     * @param boolean $lockssAuid
     * @return string
     */
    public function fromContent(Content $content, $lockssAuid = true)
    {
        $plugin = $content->getDeposit()->getContentProvider()->getPlugin();
        if ($plugin === null) {
            return;
        }
        $pluginId = $plugin->getIdentifier();
        $pluginKey = str_replace('.', '|', $pluginId);
        $auKey = '';
        $propNames = $plugin->getDefinitionalProperties();
        sort($propNames);

        foreach ($propNames as $name) {
            if (!$lockssAuid &&
                array_key_exists($pluginId, $this->nondefinitionalCPDs) &&
                in_array($name, $this->nondefinitionalCPDs[$pluginId])) {
                continue;
            }
            $value = $content->getContentPropertyValue($name, true);
            if (!$value && $lockssAuid) {
                $value = $content->getAu()->getAuPropertyValue($name, true);
            }
            $auKey .= '&'.$name.'~'.$value;
        }
        $id = $pluginKey.$auKey;

        return $id;
    }

    /**
     * Build an AUid from an AU. It's a cheat really - the AUid is built
     * from the first piece of content in the AU.
     * 
     * @param Au $au
     * @param type $lockssAuid
     * @return type
     */
    public function fromAu(Au $au, $lockssAuid = true)
    {
        $content = $au->getContent()->first();
        if ($content === null) {
            return;
        }

        return $this->fromContent($content, $lockssAuid);
    }
}
