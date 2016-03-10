<?php

namespace LOCKSSOMatic\ImportExportBundle\Command;

use Doctrine\ORM\EntityManager;
use Exception;
use LOCKSSOMatic\CoreBundle\Services\FilePaths;
use LOCKSSOMatic\CrudBundle\Entity\Au;
use LOCKSSOMatic\CrudBundle\Entity\Pln;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
	
	/**
	 * @var FilePaths
	 */
	private $fp;
	
	/**
	 * @var TwigEngine
	 */
	private $twig;

    public function configure() {
        $this->setName('lom:export:configs');
        $this->setDescription('Write all the configuration data to files.');
        $this->addArgument(
            'pln', 
            InputArgument::IS_ARRAY, 
            'List of PLN ids to export.'
        );
		$this->addOption('dry-run', '-d', InputOption::VALUE_NONE, 'Export only, do not update any internal configs.');
    }

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->em = $container->get('doctrine')->getManager();
        $this->logger = $container->get('logger');
        $this->router = $container->get('router');
        $this->titlesPerAu = $container->getParameter('lockss_aus_per_titledb');
        $this->fs = new Filesystem();
		$this->fp = $container->get('lom.filepaths');
		$this->twig = $container->get('templating');
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
        foreach($this->getPlns($plnIds) as $pln) {
			$this->exportPlugins($pln);
			$this->exportManifests($pln);
			$this->exportAus($pln);
        }
    }
	
	public function exportPlugins(Pln $pln) {
		$path = $this->fp->getPluginsExportDir($pln);
		if(! $this->fs->exists($path)) {
			$this->fs->mkdir($path);
		}
		$plugins = $pln->getPlugins();
		foreach($plugins as $plugin) {
			$this->fs->copy($plugin->getPath(), $this->fp->getPluginsExportFile($pln, $plugin));
		}
		$html = $this->twig->render('LOCKSSOMaticImportExportBundle:Configs:pluginList.html.twig', array(
			'pln' => $pln,
		));
		$this->fs->dumpFile($this->fp->getPluginsManifestFile($pln), $html);
	}

	public function exportManifests(Pln $pln) {
		foreach($pln->getAus() as $au) {
			$manifestDir = $this->fp->getManifestDir($pln, $au->getContentprovider());
			if(! $this->fs->exists($manifestDir)) {
				$this->fs->mkdir($manifestDir);
			}
			$manifestFile = $this->fp->getManifestPath($au);
			$html = $this->twig->render('LOCKSSOMaticImportExportBundle:Configs:manifest.html.twig', array(
				'content' => $au->getContent()
			));
			$this->fs->dumpFile($manifestFile, $html);
		}
	}
	
	/**
	 * This should really be called from execute() as updateAu($au) or something.
	 */
	private function buildManifestProp(Au $au) {
		$auBuilder = $this->getContainer()->get('crud.builder.au');
		$manifestFile = $this->fp->getManifestPath($au);
		$url = $this->router->generate('configs_manifest', array(
			'plnId' => $au->getPln(),
			'ownerId' => $au->getContentprovider()->getContentOwner()->getId(),
			'providerId' => $au->getContentprovider()->getId(),
			'filename' => basename($manifestFile),
		));
		$root = $au->getRootPluginProperties();
		$manifestProp = $auBuilder->buildProperty($au, 'manifest_url', null, $root[0]);
		$auBuilder->buildProperty($au, 'key', 'manifest_url', $manifestProp);
		$auBuilder->buildProperty($au, 'value', $url, $manifestProp);
		$this->em->flush();
	}
	
	public function exportAus(Pln $pln) {
		foreach($pln->getContentProviders() as $provider) {
			$titleDir = $this->fp->getTitleDbDir($pln, $provider);
			if(! $this->fs->exists($titleDir)) {
				$this->fs->mkdir($titleDir);
			}
			
			$aus = $provider->getAus();
			foreach($aus as $au) {
				$this->logger->critical('mu: ' . $au->getAuProperty('manifest_url'));
				if(! $au->getAuProperty('manifest_url')) {
					$this->buildManifestProp($au);
				}				
				$this->logger->critical('mu: ' . $au->getAuProperty('manifest_url'));
				$xml = $this->twig->render('LOCKSSOMaticImportExportBundle:Configs:titledb.xml.twig', array(
					'aus' => array($au)
				));
				$this->fs->dumpFile("{$titleDir}/{$au->getId()}.xml", $xml);
			}
		}
	}
}
