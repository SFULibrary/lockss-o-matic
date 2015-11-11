<?php

namespace LOCKSSOMatic\ImportExportBundle\Command;

use Doctrine\ORM\EntityManager;
use Exception;
use LOCKSSOMatic\CrudBundle\Entity\Pln;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Private Lockss network plugin import command-line
 */
class ExportConfigsCommand extends ContainerAwareCommand {

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Filesystem
     */
    private $fs;

    public function configure() {
        $this->setName('lom:export:configs');
        $this->setDescription('Write all the configuration data to files.');
    }

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->em = $container->get('doctrine')->getManager();
        $this->logger = $container->get('logger');
        $this->fs = new Filesystem();
    }

    /**
     * @return Pln[]
     */
    private function getPlns() {
        return $this->em->getRepository('LOCKSSOMaticCrudBundle:Pln')->findAll();
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $tempPath = sys_get_temp_dir();
        $webPath =  $this->getContainer()->get('kernel')->getRootDir() . '/../web/plnconfigs';

        foreach($this->getPlns() as $pln) {
            try {
                $tempDirName = tempnam($tempPath, "lom-export-configs-{$pln->getId()}-");
                $this->fs->remove($tempDirName); // don't need the file.
                $this->fs->mkdir($tempDirName);

                $configDirName = "{$webPath}/{$pln->getId()}";
                // do the .htaccess here.
                $this->exportPlnConfig($pln, $tempDirName);
                $this->fs->mirror($tempDirName, $configDirName);
                $output->writeln("Exported {$pln->getName()} to {$configDirName})");
            } catch (Exception $ex) {
                $m = "Error while exporting {$pln->getName()}: {$ex->getMessage()}";
                $this->logger->critical($m);
                continue;
            }
        }
    }

    public function exportPlnConfig(Pln $pln, $dir) {
        $propPath = "{$dir}/properties";
        $this->fs->mkdir($propPath);
        $this->exportLockssXML($pln, "{$propPath}/lockss.xml");

        $pluginPath = "{$dir}/plugins";
        $this->fs->mkdir($pluginPath);
        foreach($pln->getContentProviders() as $provider) {
            $providerPath = "{$pluginPath}/{$provider->getContentOwner()->getId()}/{$provider->getId()}";
            $this->fs->mkdir($providerPath);
            // export the titledb files for the provider.
        }

        $titlePath = "{$dir}/titledbs";
        $this->fs->mkdir($titlePath);
        foreach($pln->getContentProviders() as $provider) {
            $providerPath = "{$titlePath}/{$provider->getContentOwner()->getId()}/{$provider->getId()}";
            $this->fs->mkdir($providerPath);
            // export the titledb files for the provider.
        }

        $manifestPath = "{$dir}/manifests";
        $this->fs->mkdir($manifestPath);
        foreach($pln->getContentProviders() as $provider) {
            $providerPath = "{$manifestPath}/{$provider->getContentOwner()->getId()}/{$provider->getId()}";
            $this->fs->mkdir($providerPath);
            // export the manifest files.
        }
    }

    public function exportLockssXML(Pln $pln, $path) {
        $boxes = $pln->getBoxes();
        foreach ($boxes as $box) {
            $boxList[] = "{$box->getProtocol()}:[{$box->getIpAddress()}]:{$box->getPort()}";
        }
        $boxProp = $pln->getProperty('id.initialV3PeerList');
        if( ! $boxProp) {
            throw new Exception("Cannot find id.initialV3PeerList in PLN properties.");
        }
        $boxProp->setPropertyValue($boxList);
        $this->em->flush();
        $twig = $this->getContainer()->get('templating');
        $xml = $twig->render('LOCKSSOMaticCrudBundle:Pln:lockss.xml.twig', array('entity' => $pln));
        file_put_contents($path, $xml);
    }

}
