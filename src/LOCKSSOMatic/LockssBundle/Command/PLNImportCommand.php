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

namespace LOCKSSOMatic\LockssBundle\Command;

use Doctrine\ORM\EntityManager;
use Exception;
use LOCKSSOMatic\CrudBundle\Entity\Pln;
use Monolog\Logger;
use SimpleXMLElement;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Private Lockss network import command. Imports a lockss.xml configuration
 * file. You can give it a file path (/path/to/file) or a URL.
 */
class PLNImportCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Logger
     */
    private $logger;

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->em = $container->get('doctrine')->getManager();
        $this->logger = $container->get('logger');
    }

    public function configure()
    {
        $this->setName('lom:import:pln')
            ->setDescription('Import PLN XML file.')
            ->addArgument(
                'id',
                null,
                InputArgument::REQUIRED,
                "LOCKSSOMatic's ID for the PLN"
            )
            ->addArgument(
                'file',
                null,
                InputArgument::REQUIRED,
                'Local file path to the lockss.xml file'
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $activityLog = $this->getContainer()->get('activity_log');
        $activityLog->disable();

        $id = $input->getArgument('id');
        $pln = $this->em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($id);
        if ($pln === null) {
            throw new Exception("Cannot find pln {$id}");
        }

        $xml = simplexml_load_file($input->getArgument('file'));
        $this->importProperties($pln, $xml);

        $this->em->flush();
	$activityLog->enable();
    }

    public function importProperties(Pln $pln, SimpleXMLElement $xml, $prefix = '')
    {
        foreach ($xml->children() as $node) {
            switch ($node->getName()) {
                case 'property':
                    $name = $node['name'];
                    if ($node['value']) {
                        $pln->setProperty("{$prefix}{$name}", (string) $node['value']);
                    } else {
                        $this->importProperties($pln, $node, "{$prefix}{$name}.");
                    }
                    break;
                case 'list':
                    $v = array();
                    foreach ($node->children() as $value) {
                        $v[] = (string) $value;
                    }
                    $pln->setProperty(rtrim($prefix, '.'), $v);
                    break;
                default:
                    $this->importProperties($pln, $node, $prefix);
            }
        }
    }
}
