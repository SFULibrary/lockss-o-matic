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
        $webPath =  $this->getContainer()->get('kernel')->getRootDir() . '/../data/plnconfigs';
        
        foreach($this->getPlns() as $pln) {
            try {
                $tempDirName = tempnam($tempPath, "lom-export-configs-{$pln->getId()}-");
                $output->writeln("Using {$tempDirName}");

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
        $this->exportPlugins($pln, $dir);
        $this->exportAuFiles($pln, $dir);
        $this->exportManifests($pln, $dir);
    }

    public function exportManifests(Pln $pln, $dir) {
        $twig = $this->getContainer()->get('templating');
        $manifestPath = "{$dir}/manifests";
        $this->fs->mkdir($manifestPath);
        $limit = $this->getContainer()->getParameter('lockss_urls_per_manifest');
        foreach($pln->getContentProviders() as $provider) {

            $content = $provider->getContent();
            $contentCount = count($content);
            if($contentCount === 0) {
                continue;
            }
            $manifestFiles = ceil($contentCount / $limit);
            $digits = ceil(log10($manifestFiles));

            $providerPath = "{$manifestPath}/{$provider->getContentOwner()->getId()}/{$provider->getId()}";
            $this->fs->mkdir($providerPath);

            for($i = 1; $i < $manifestFiles; $i++) {
                $filename = sprintf("au_%0{$digits}d.html", $i);
                $slice = array_slice($content, ($i-1) * $limit, $limit);
                $html = $twig->render('LOCKSSOMaticImportExportBundle:Configs:manifest.html.twig', array(
                    'content' => $slice
                ));
                file_put_contents("$providerPath/$filename", $html);
            }
        }
    }

    public function exportAuFiles(Pln $pln, $dir) {
        $twig = $this->getContainer()->get('templating');
        $limit = $this->getContainer()->getParameter('lockss_aus_per_titledb');
        $titlePath = "{$dir}/titledbs";
        $this->fs->mkdir($titlePath);
        foreach($pln->getContentProviders() as $provider) {
            $aus = $provider->getAus();
            $auCount = $aus->count();

            if($auCount === 0) {
                continue;
            }
            $titleDbFiles = ceil($auCount / $limit);
            $digits = ceil(log10($titleDbFiles));

            $providerPath = "{$titlePath}/{$provider->getContentOwner()->getId()}/{$provider->getId()}";
            $this->fs->mkdir($providerPath);

            for($i = 1; $i <= $titleDbFiles; $i++) {
                $filename = sprintf("titledb_%0{$digits}d.xml", $i);
                $slice = array_slice($aus->toArray(), ($i-1) * $limit, $limit);
                $xml = $twig->render('LOCKSSOMaticImportExportBundle:Configs:titledb.xml.twig', array(
                    'aus' => $slice,
                ));
                file_put_contents("$providerPath/$filename", $xml);
            }
        }
    }

    /**
     * Export the plugins for one PLN. Does not write out the index.html
     * file - that's left to the ConfigsController to do.
     * 
     * @param Pln $pln
     * @param string $dir
     */
    public function exportPlugins(Pln $pln, $dir) {
        $pluginPath = "{$dir}/plugins";
        $this->fs->mkdir($pluginPath);
        $plugins = $pln->getPlugins();
        foreach($plugins as $plugin) {
            copy($plugin->getPath(), $pluginPath . '/' . $plugin->getFilename());
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
        $xml = $twig->render(
            'LOCKSSOMaticImportExportBundle:Configs:lockss.xml.twig', 
            array(
                'entity' => $pln
        ));
        file_put_contents($path, $xml);
    }

}
