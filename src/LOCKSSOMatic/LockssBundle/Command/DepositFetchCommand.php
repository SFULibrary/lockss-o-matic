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
use LOCKSSOMatic\CrudBundle\Entity\Box;
use LOCKSSOMatic\CrudBundle\Entity\Deposit;
use LOCKSSOMatic\CrudBundle\Entity\Pln;
use LOCKSSOMatic\CrudBundle\Service\AuIdGenerator;
use LOCKSSOMatic\LockssBundle\Utilities\LockssSoapClient;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Fetch a deposit's content items from the PLN. Verifies the checksum on the box
 * before downloading, then verifies it again after download.
 */
class DepositFetchCommand extends ContainerAwareCommand
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var AuIdGenerator
     */
    private $idGenerator;
    
    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setName('lom:deposit:fetch');
        $this->setDescription('Fetch one or more deposits from the PLN.');
        $this->addArgument('uuids', InputArgument::IS_ARRAY, 'One or more deposit UUIDs to fetch.');
    }

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->logger = $container->get('logger');
        $this->em = $container->get('doctrine')->getManager();
        $this->idGenerator = $this->getContainer()->get('crud.au.idgenerator');
    }
    
    /**
     * Gets the boxes for a PLN in a random order.
     * 
     * @return Box[]
     */
    public function loadBoxes(Pln $pln) {
        $boxes = $pln->getBoxes()->toArray();
        shuffle($boxes);
        return $boxes;
    }

    /**
     * Get the deposits to download.
     * 
     * @param string[] $uuids
     * @return Deposit[]|Collection
     */
    protected function getDeposits($uuids) {
        $repo = $this->em->getRepository('LOCKSSOMaticCrudBundle:Deposit');
        return $repo->findBy(array('uuid' => $uuids));
    }

    /**
     * Download a deposit from the network.
     * 
     * @todo I thought this was finished.
     * 
     * @param Deposit $deposit
     */
    protected function fetchDeposit(Deposit $deposit) {
        $pln = $deposit->getPln();
        $boxes = $this->loadBoxes($pln);
        $auid = $this->idGenerator->fromAu($deposit->getContent()->first()->getAu());
        
        foreach($deposit->getContent() as $content) {
            
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $uuids = $input->getArgument('uuids');        
        $deposits = $this->getDeposits($uuids);
        $this->logger->notice("Fetching " . count($deposits) . " deposit(s)");
        
        foreach($deposits as $deposit) {
            $this->logger->notice("Fetching {$deposit->getUuid()}");
            $result = $this->fetchDeposit($deposit);
        }
    }

}