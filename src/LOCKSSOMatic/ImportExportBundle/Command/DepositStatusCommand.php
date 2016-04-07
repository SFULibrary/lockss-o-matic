<?php

namespace LOCKSSOMatic\ImportExportBundle\Command;

use Monolog\Logger;
use SoapClient;
use SoapFault;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DepositStatusCommand extends ContainerAwareCommand
{

    /**
     * @var Logger
     */
    private $logger;

    public function configure()
    {
        $this->setName('lom:deposit:status');
    }

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->logger = $container->get('logger');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('soap.wsdl_cache_ttl', false);

        $statusClient = new SoapClient('http://localhost:8081/ws/DaemonStatusService?wsdl',
            array(
            'soap_version' => SOAP_1_1,
            'login'        => 'lockss-u',
            'password'     => 'lockss-p',
            'trace'        => true,
            'exceptions'   => true,
            'cache'        => WSDL_CACHE_NONE,
            )
        );

        $ready = $statusClient->isDaemonReady();
        if (!$ready) {
            die("DAEMON is not ready.");
        }

        $hasherClient = new SoapClient('http://localhost:8081/ws/HasherService?wsdl',
            array(
            'soap_version' => SOAP_1_1,
            'login'        => 'lockss-u',
            'password'     => 'lockss-p',
            'trace'        => true,
            'exceptions'   => true,
            'cache'        => WSDL_CACHE_NONE,
        ));

        $hashReq = $hasherClient->hash(array(
            'hasherParams' => array(
                'recordFilterStream' => true,
                'hashType'           => 'V3File',
                'algorithm'          => 'SHA-1',
                'url'                => 'http://pkppln.dv/web/fetch/051A6CDD-C5F0-48C6-851D-C865F43F27CD/2068BCB5-2862-4056-8A02-50650CD9BD7D.zip',
                'auId'               => 'ca|sfu|lib|plugin|pkppln|PkpPlnPlugin&base_url~http%3A%2F%2Fpkppln%2Edv%2Fweb%2F&container_number~1&manifest_url~http%3A%2F%2Flom%2Edv%2Fweb%2Fapp_dev%2Ephp%2Fplnconfigs%2F1%2Fmanifests%2F1%2F1%2Fmanifest_1%2Ehtml&permission_url~http%3A%2F%2Fpkppln%2Edv%2Fweb%2Fpermission'
        )));
        $matches = array();
        $bdf = $hashReq->return->blockFileDataHandler;
        if( ! preg_match("/^([a-fA-F0-9]+)\s+http/m", $bdf, $matches)) {
            die("NO MAS.");
        }
        print $matches[1] . "\n";
    }

}
