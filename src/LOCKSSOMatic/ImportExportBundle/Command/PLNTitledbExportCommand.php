<?php

namespace LOCKSSOMatic\ImportExportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PLNTitledbExportCommand extends ContainerAwareCommand {
    
    public function configure() {
        $this->setName('lom:export:titledb')
            ->setDescription('Export a PLN titledb file.')
            ->addArgument('plnId', null, InputArgument::REQUIRED, 
                "LOCKSSOMatic's ID for the PLN");
    }
    
    public function execute(InputInterface $input, OutputInterface $output) {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $pln = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($input->getArgument('plnId'));
        if($pln === null) {
            $output->writeln('Cannot find PLN.');
            exit;
        }
        
        $aus = $em->getRepository('LOCKSSOMaticCrudBundle:Au')->findBy(array(
            'pln' => $pln
        ));
        $twig = $this->getContainer()->get('templating');
        print $twig->render('LOCKSSOMaticCrudBundle:Pln:titledb.xml.twig', array(
            'aus' => $aus
        ));
    }
    
}
