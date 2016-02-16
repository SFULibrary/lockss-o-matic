<?php

namespace LOCKSSOMatic\ImportExportBundle\Command;

use Doctrine\ORM\EntityManager;
use Exception;
use LOCKSSOMatic\CrudBundle\Entity\Pln;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Router;

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
    
    /**
     * @var Router
     */
    private $router;
    
    /**
     * @var int
     */
    private $titlesPerAu;

    public function configure() {
        $this->setName('lom:export:configs');
        $this->setDescription('Write all the configuration data to files.');
        $this->addArgument(
            'pln', 
            InputArgument::IS_ARRAY, 
            'List of PLN ids to export.'
        );
    }

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->em = $container->get('doctrine')->getManager();
        $this->logger = $container->get('logger');
        $this->router = $container->get('router');
        $this->titlesPerAu = $container->getParameter('lockss_aus_per_titledb');
        $this->fs = new Filesystem();
    }

    /**
     * @param array plnIds
     * @return Pln[]
     */
    private function getPlns($plnIds = null) {
        if($plnIds === null || count($plnIds) === 0) {
            return $this->em->getRepository('LOCKSSOMaticCrudBundle:Pln')->findAll();
        }
        return $this->em->getRepository('LOCKSSOMaticCrudBundle:Pln')->findById($plnIds);
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $plnIds = $input->getArgument('pln');
        $webPath =  $this->getContainer()->get('kernel')->getRootDir() . '/../data/plnconfigs';
        if( ! $this->fs->exists($webPath)) {
            $this->fs->mkdir($webPath);
        }
        foreach($this->getPlns($plnIds) as $pln) {
            try {
                $this->updatePlnConfig($pln);
                $this->em->flush();
                $this->exportPln($pln);
            } catch (Exception $ex) {
                $m = "Error while exporting {$pln->getName()}: {$ex->getMessage()}\nIn{$ex->getFile()}:{$ex->getLine()}";
                $this->logger->critical($m);
                $this->logger->critical($ex->getTraceAsString());
                continue;
            }
        }
    }
    
    public function generateUrl($route, $parameters, $referenceType) {
        return $this->router->generate($route, $parameters, $referenceType);
    }
    
    public function updatePlnConfig(Pln $pln) {
        $this->updatePeerList($pln);
        $this->updatePluginRegistryList($pln);
        $this->updateTitleDbs($pln);
    }

    public function updatePeerList(Pln $pln) {
        $boxes = $pln->getBoxes();
        $boxList = array();
        foreach ($boxes as $box) {
            $boxList[] = "{$box->getProtocol()}:[{$box->getIpAddress()}]:{$box->getPort()}";
        }
        $boxProp = $pln->getProperty('id.initialV3PeerList');
        $boxProp->setPropertyValue($boxList);
    }
    
    public function updatePluginRegistryList(Pln $pln) {
        $pluginUrlList = array(
            $this->generateUrl(
                'configs_plugin_list', 
                array('plnId' => $pln->getId()),
                UrlGeneratorInterface::ABSOLUTE_URL),
        );
        $pluginProp = $pln->getProperty('plugin.registries');
        $pluginProp->setPropertyValue($pluginUrlList);        
    }
    
    public function updateTitleDbs(Pln $pln) {
        $urls = array();
        
        foreach($pln->getContentProviders() as $provider) {
            $auCount = $provider->countAus();
            if($auCount === 0) {
                continue;
            }
            $titleDbFiles = ceil($auCount / $this->titlesPerAu);
            $digits = ceil(log10($titleDbFiles));
            
            for($i = 1; $i <= $titleDbFiles; $i++) {
                $urls[] = $this->generateUrl('configs_titledb', array(
                    'plnId' => $pln->getId(),
                    'ownerId' => $provider->getContentOwner()->getId(),
                    'providerId' => $provider->getId(),
                    'filename' => sprintf("titledb_%0{$digits}d.xml", $i)
                ), 
                UrlGeneratorInterface::ABSOLUTE_URL
                );
            }
        }

        $titleDbProp = $pln->getProperty('titleDbs');
        $titleDbProp->setPropertyValue($urls);
        $this->em->flush($titleDbProp);
    }
    
    public function exportPln($pln) {
        $tempPath = sys_get_temp_dir();        
        $tempDirName = tempnam($tempPath, "lom-export-configs-{$pln->getId()}-");
        $this->fs->remove($tempDirName); // just in case.
        $this->fs->mkdir($tempDirName);

        $webPath =  $this->getContainer()->get('kernel')->getRootDir() . '/../data/plnconfigs';
        $configDirName = "{$webPath}/{$pln->getId()}";

        $this->exportPlnConfig($pln, $tempDirName);
        $this->fs->remove($configDirName);
        $this->fs->rename($tempDirName, $configDirName);
    }
    
    public function exportPlnConfig(Pln $pln, $dir) {
        $this->exportPlugins($pln, $dir);
        $auFiles = $this->exportAuFiles($pln, $dir);
        $this->exportManifests($pln, $dir);
        $this->exportLockssXML($pln, $dir, $auFiles);
    }

    public function exportManifests(Pln $pln, $dir) {
        $twig = $this->getContainer()->get('templating');
        $manifestPath = "{$dir}/manifests";
        $this->fs->mkdir($manifestPath);
        foreach($pln->getContentProviders() as $provider) {
            $aus = $provider->getAus();
            $providerPath = "{$manifestPath}/{$provider->getContentOwner()->getId()}/{$provider->getId()}";
            $this->fs->mkdir($providerPath);
            foreach($aus as $au) {
                $content = $au->getContent();
                if(count($content) === 0) {
                    continue;
                }
                $html = $twig->render('LOCKSSOMaticImportExportBundle:Configs:manifest.html.twig', array(
                    'content' => $content
                ));
                file_put_contents("{$providerPath}/au_{$au->getId()}.html", $html);
            }
        }
    }

    public function exportAuFiles(Pln $pln, $dir) {
        $twig = $this->getContainer()->get('templating');
        $limit = $this->getContainer()->getParameter('lockss_aus_per_titledb');
        $titlePath = "{$dir}/titledbs";
        $this->fs->mkdir($titlePath);
        $auFiles = array();
        $auBuilder = $this->getContainer()->get('crud.builder.au');
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

            foreach($aus as $au) {
                $manifestProp = $au->getAuProperty('manifest_url');
                if($manifestProp === "") {
                    $this->getContainer()->get('logger')->warn('Building manifest url for ' . $au->getId());
                    $root = $au->getRootPluginProperties();
                    $manifestProp = $auBuilder->buildProperty($au, 'param.manifest', null, $root[0]);
                    $auBuilder->buildProperty($au, 'key', 'manifest_url', $manifestProp);
                    $auBuilder->buildProperty($au, 'value', 
                        $this->generateUrl('config_manifest', array(
                            'plnId' => $pln->getId(),
                            'ownerId' => $provider->getContentOwner()->getId(),
                            'providerId' => $provider->getId(),
                            'filename' => "au_{$au->getId()}.html"                                
                        ),
                        UrlGeneratorInterface::ABSOLUTE_URL)); 
                } 
            }

            for($i = 1; $i <= $titleDbFiles; $i++) {
                $filename = sprintf("titledb_%0{$digits}d.xml", $i);
                $auFiles[] = "titleDbs/{$provider->getContentOwner()->getId()}/{$provider->getId()}/$filename";
                $slice = array_slice($aus->toArray(), ($i-1) * $limit, $limit);
                $xml = $twig->render('LOCKSSOMaticImportExportBundle:Configs:titledb.xml.twig', array(
                    'aus' => $slice,
                ));
                file_put_contents("$providerPath/$filename", $xml);
            }
        }
        return $auFiles;
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
        $twig = $this->getContainer()->get('templating');
        $html = $twig->render('LOCKSSOMaticImportExportBundle:Configs:pluginList.html.twig', array(
            'pln' => $pln
        ));
        file_put_contents("{$dir}/plugins/index.html", $html);
    }

    public function exportLockssXML(Pln $pln, $dir, $auFiles) {
        $boxes = $pln->getBoxes();
        foreach ($boxes as $box) {
            $boxList[] = "{$box->getProtocol()}:[{$box->getIpAddress()}]:{$box->getPort()}";
        }
        $boxProp = $pln->getProperty('id.initialV3PeerList');
        if( ! $boxProp) {
            $this->logger->warning("Cannot find id.initialV3PeerList in PLN properties.");
        }
        $boxProp->setPropertyValue($boxList);

        $titleProp = $pln->getProperty('titleDbs');
        $titleProp->setPropertyValue($auFiles);

        $this->em->flush();
        $twig = $this->getContainer()->get('templating');
        $xml = $twig->render(
            'LOCKSSOMaticImportExportBundle:Configs:lockss.xml.twig', 
            array(
                'pln' => $pln
            )
        );
        file_put_contents("{$dir}/lockss.xml", $xml);
    }

}
