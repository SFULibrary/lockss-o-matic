<?php

namespace LOCKSSOMatic\ImportExportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TitledbExportCommand extends ContainerAwareCommand
{
    
    public function configure()
    {
        $this->setName('lom:export:titledb')
            ->setDescription('Export a PLN titledb file.')
            ->addArgument(
                'plnId',
                InputArgument::REQUIRED,
                "LOCKSSOMatic's ID for the PLN"
            )
            ->addArgument(
                'file',
                InputArgument::OPTIONAL,
                "Optional Output file"
            );

    }
    
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $pln = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($input->getArgument('plnId'));
        if ($pln === null) {
            $output->writeln('Cannot find PLN.');
            exit;
        }
        $fh = fopen('php://output', 'w');
        if ($input->hasArgument('file') && $input->getArgument('file')) {
            $fh = fopen($input->getArgument('file'), 'w');
        }

        $aus = $em->getRepository('LOCKSSOMaticCrudBundle:Au')->findBy(array(
            'pln' => $pln
        ));
        $twig = $this->getContainer()->get('templating');
        fputs($fh, $twig->render('LOCKSSOMaticCrudBundle:Pln:titledb.xml.twig', array(
            'aus' => $aus
        )));
    }
}
