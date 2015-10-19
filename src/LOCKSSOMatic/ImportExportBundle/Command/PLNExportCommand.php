<?php

namespace LOCKSSOMatic\ImportExportBundle\Command;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityManager;
use LOCKSSOMatic\CrudBundle\Entity\Pln;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Private Lockss network plugin import command-line
 */
class PLNExportCommand extends ContainerAwareCommand
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var OutputInterface
     */
    private $output;

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->em = $container->get('doctrine')->getManager();
    }

    public function configure()
    {
        $this->setName('lom:export:pln')
            ->setDescription('Export a PLN LOCKSS XML file.')
            ->addArgument('id', null, InputArgument::REQUIRED,
                "LOCKSSOMatic's ID for the PLN");
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $activityLog = $this->getContainer()->get('activity_log');
        $activityLog->disable();

        /** @var Pln $pln */
        $pln = $this->em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($input->getArgument('id'));
        $boxes = $pln->getBoxes();
        $boxList = array();
        foreach($boxes as $box) {
            $boxList[] = "{$box->getProtocol()}:[{$box->getIpAddress()}]:{$box->getPort()}";
        }
        $boxProp = $pln->getProperty('id.initialV3PeerList');
        $boxProp->setPropertyValue($boxList);
        $this->em->flush();

        if( $pln === null) {
            $output->writeln('Cannot find pln.');
            exit;
        }

        /** @var TwigEngine $twig */
        $twig = $this->getContainer()->get('templating');
        print $twig->render('LOCKSSOMaticCrudBundle:Pln:lockss.xml.twig', array('entity' => $pln));
    }
}
