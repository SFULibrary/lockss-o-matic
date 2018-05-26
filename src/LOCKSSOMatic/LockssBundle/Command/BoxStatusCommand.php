<?php

namespace LOCKSSOMatic\LockssBundle\Command;

use DateTime;
use Doctrine\ORM\EntityManager;
use LOCKSSOMatic\CrudBundle\Entity\Box;
use LOCKSSOMatic\CrudBundle\Entity\BoxStatus;
use LOCKSSOMatic\CrudBundle\Entity\CacheStatus;
use LOCKSSOMatic\CrudBundle\Entity\Pln;
use LOCKSSOMatic\CrudBundle\Service\AuIdGenerator;
use LOCKSSOMatic\LockssBundle\Utilities\LockssSoapClient;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Check the status of the boxes in one or more PLNs.
 */
class BoxStatusCommand extends ContainerAwareCommand
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
     * Configure the command.
     */
    public function configure() {
        $this->setName('lom:box:status');
        $this->setDescription('Check the status of the LOCKSS boxes');
        $this->addOption('pln', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Optional list of PLNs to check.');
        $this->addOption('dry-run', '-d', InputOption::VALUE_NONE, 'Do not update box status, just report results to console.');
    }

    /**
     * Set the container.
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->logger = $container->get('logger');
        $this->em = $container->get('doctrine')->getManager();
        $this->idGenerator = $this->getContainer()->get('crud.au.idgenerator');
    }

    /**
     * Get the boxes for the requested PLNs or all the PLNs.
     *
     * @param array $plnIds
     * @return Pln[]
     */
    protected function getBoxes($plnIds = null) {
        if ($plnIds === null || count($plnIds) === 0) {
            return $this->em->getRepository('LOCKSSOMaticCrudBundle:Box')->findBy(array(
                'active' => true,
            ));
        }
        $plns = $this->em->getRepository('LOCKSSOMaticCrudBundle:Pln')->findById($plnIds);

        return $this->em->getRepository('LOCKSSOMaticCrudBundle:Box')->findBy(array(
            'pln' => $plns,
            'active' => true,
        ));
    }
    
    /**
     * @param \LOCKSSOMatic\LockssBundle\Command\Box $box
     * @return LockssSoapClient
     */
    protected function getClient(Box $box) {
        $wsdl = "http://{$box->getHostname()}:{$box->getWebservicePort()}/ws/DaemonStatusService?wsdl";
        $client = new LockssSoapClient();
        $client->setWsdl($wsdl);
        $client->setOption('login', $box->getPln()->getUsername());
        $client->setOption('password', $box->getPln()->getPassword());
        return $client;
    }
    
    /**
     * @param \LOCKSSOMatic\LockssBundle\Command\Box $box
     * @return BoxStatus
     */
    protected function createBoxStatus(Box $box) {
        $boxStatus = new BoxStatus();
        $boxStatus->setSuccess(false); 
        $boxStatus->setBox($box);
        $boxStatus->setQueryDate(new DateTime());
        $this->em->persist($boxStatus);
        return $boxStatus;
    }
    
    /**
     * @param LockssSoapClient $client
     * @param \LOCKSSOMatic\LockssBundle\Command\Box $box
     * @return array
     */
    protected function getStatus(LockssSoapClient $client, Box $box) {
        $status = $client->call('queryRepositorySpaces', array(
                'repositorySpaceQuery' => 'SELECT *',
        ));
        if ($status === null) {
            return null;
        }
        $response = $status->return;
        if (!is_array($response)) {
            $response = array($response);
        }
        return $response;
    }
    
    protected function triggerError(LockssSoapClient $client, Box $box, BoxStatus $boxStatus) {
        $this->logger->warning("{$box->getHostname()} status failed: {$client->getErrors()}");
        $boxStatus->setErrors($client->getErrors());
    }
    
    /**
     * @param array $response
     * @param Box $box
     * @param BoxStatus $boxStatus
     */
    protected function checkCaches($response, Box $box, BoxStatus $boxStatus) {
        foreach ($response as $cacheResponse) {
            $cacheStatus = new CacheStatus();
            $cacheStatus->setBoxStatus($boxStatus);
            $cacheStatus->setResponse(get_object_vars($cacheResponse));
            if ($cacheResponse->percentageFull > 0.90) {
                $this->logger->warning("{$box->getHostname()} has cache {$cacheResponse->repositorySpaceId} which is more than 90% full.");
                $boxStatus->appendErrors("Cache {$cacheResponse->repositorySpaceId} which is more than 90% full.");
            }
            $this->em->persist($cacheStatus);
            $boxStatus->addCache($cacheStatus);
        }
        $boxStatus->setSuccess(true);
    }
    
    public function notifyAdmin(Box $box, BoxStatus $boxStatus) {
        $templating = $this->getContainer()->get('templating');
        
        if( ! $box->getSendNotifications() || ! $box->getContactEmail()) {
            return;
        }
        $subject = $this->getContainer()->getParameter('lom_boxstatus_subject');
        $message = new \Swift_Message($subject, null, 'text/plain', '7bit');
        $message->setTo($box->getContactEmail());
        $message->setFrom($this->getContainer()->getParameter('lom_boxstatus_sender'));
        $message->setBody($templating->render('LOCKSSOMaticLockssBundle:Emails:box_error.text.twig', array(
            'box' => $box,
            'boxStatus' => $boxStatus,
            'contact' => $this->getContainer()->getParameter('lom_boxstatus_contact'),
        )));
        $this->getContainer()->get('mailer')->send($message);
        print $message;
    }
    
    /**
     * @param Box $box
     * @return BoxStatus
     */
    public function getBoxStatus(Box $box) {
        $client = $this->getClient($box);
        $boxStatus = $this->createBoxStatus($box);
        $response = $this->getStatus($client, $box);

        if ($response === null) {
            $this->triggerError($client, $box, $boxStatus);
        } else {
            $this->checkCaches($response, $box, $boxStatus);
        }
        return $boxStatus;
    }

    /**
     * Execute the command.
     *
     * @param InputInterface $input
     * @return null
     */
    public function execute(InputInterface $input, OutputInterface $output) {
        $dryRun = $input->getOption('dry-run');
        $plnIds = $input->getOption('pln');
        foreach ($this->getBoxes($plnIds) as $box) {
            $this->logger->notice("checking {$box->getHostname()}");            
            $boxStatus = $this->getBoxStatus($box);
            if( ! $boxStatus->getSuccess() && ! $dryRun) {
                $this->notifyAdmin($box, $boxStatus);
            }
        }
        if ($dryRun) {
            return;
        }
        $this->em->flush();
    }
}
