<?php

namespace LOCKSSOMatic\LockssBundle\Command;

use DateTime;
use Doctrine\ORM\EntityManager;
use Exception;
use LOCKSSOMatic\CrudBundle\Entity\BoxStatus;
use LOCKSSOMatic\CrudBundle\Entity\CacheStatus;
use LOCKSSOMatic\CrudBundle\Service\AuIdGenerator;
use LOCKSSOMatic\LockssBundle\Utilities\LockssSoapClient;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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

    public function configure()
    {
        $this->setName('lom:box:status');
        $this->setDescription('Check the status of the LOCKSS AUs');
        $this->addArgument(
            'boxes',
            InputArgument::IS_ARRAY,
            'Optional list of box ids to check.'
        );
        $this->addOption(
            'dry-run',
            '-d',
            InputOption::VALUE_NONE,
            'Do not update box status, just report results to console.'
        );
    }

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->logger = $container->get('logger');
        $this->em = $container->get('doctrine')->getManager();
        $this->idGenerator = $this->getContainer()->get('crud.au.idgenerator');
    }

    protected function getBoxes($boxIds = null)
    {
        if ($boxIds === null || count($boxIds) === 0) {
            return $this->em->getRepository('LOCKSSOMaticCrudBundle:Box')->findAll();
        }
        return $this->em->getRepository('LOCKSSOMaticCrudBundle:Box')->findById($boxIds);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $boxIds = $input->getArgument('boxes');
        foreach ($this->getBoxes($boxIds) as $box) {
            $wsdl = "http://{$box->getHostname()}:{$box->getWebservicePort()}/ws/DaemonStatusService?wsdl";
            $this->logger->notice("checking {$wsdl}");
            $client = new LockssSoapClient();
            $client->setWsdl($wsdl);
            $client->setOption('login', $box->getPln()->getUsername());
            $client->setOption('password', $box->getPln()->getPassword());

            $boxStatus = new BoxStatus();
            $this->em->persist($boxStatus);

            $boxStatus->setBox($box);
            $boxStatus->setQueryDate(new DateTime());
            $status = $client->call(
                'queryRepositorySpaces',
                array(
                    'repositorySpaceQuery' => 'SELECT *'
                )
            );

            if ($status === null) {
                $this->logger->warning("{$wsdl} failed.");
                $boxStatus->setSuccess(false);
                $boxStatus->setErrors($client->getErrors());
                continue;
            }
            $r = $status->return;
            if (!is_array($r)) {
                $r = array($r);
            }
            foreach ($r as $c) {
                $cache = new CacheStatus();
                $cache->setBoxStatus($boxStatus);
                $cache->setResponse(get_object_vars($c));
                $boxStatus->addCache($cache);
                $boxStatus->setSuccess(true);
            }
        }
        if ($input->getOption('dry-run')) {
            return;
        }
        $this->em->persist($boxStatus);
        $this->em->flush();
    }
}
