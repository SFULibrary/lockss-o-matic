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
			$auUrls = $this->exportAus($pln);
			$this->exportLockssXml($pln, $auUrls);
        }
    }
	
	public function exportLockssXml(Pln $pln, $auUrls) {
		$boxes = $pln->getBoxes();
		$boxList = array();
		foreach($boxes as $box) {
			$boxList[] = "{$box->getProtocol()}:[{$box->getIpAddress()}]:{$box->getPort()}";
		}
		$boxProp = $pln->getProperty('id.initialV3PeerList');
		if( ! $boxProp) {
            throw new Exception("Cannot find id.initialV3PeerList in PLN properties.");
        }
		$boxProp->setPropertyValue($boxList);
		$titleProp = $pln->getProperty('titleDbs');
		$titleProp->setPropertyValue($auUrls);
		$this->em->flush();
		$twig = $this->getContainer()->get('templating');
		$xml = $twig->render(
			'LOCKSSOMaticImportExportBundle:Configs:lockss.xml.twig', 
			array(
				'pln' => $pln
			)
		);
		$path = $this->fp->getLockssXmlFile($pln);
		$this->fs->dumpFile($path);
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
			$manifestUrl = $this->router->generate('configs_manifest', array(
				'plnId' => $pln->getId(),
				'ownerId' => $au->getContentprovider()->getContentOwner()->getId(),
				'providerId' => $au->getContentprovider()->getId(),
				'auId' => $au->getId(),
			));			
			$manifestFile = $manifestDir . '/' . basename($manifestUrl);
			$html = $this->twig->render('LOCKSSOMaticImportExportBundle:Configs:manifest.html.twig', array(
				'content' => $au->getContent()
			));
			$this->fs->dumpFile($manifestFile, $html);
		}
	}
	
	public function exportAus(Pln $pln) {
		$auUrls = array();
		foreach($pln->getContentProviders() as $provider) {
			$titleDir = $this->fp->getTitleDbDir($pln, $provider);
			if(! $this->fs->exists($titleDir)) {
				$this->fs->mkdir($titleDir);
			}
			$auUrl = $this->router->generate('configs_titledb', array(
				'plnId' => $pln->getId(),
				'ownerId' => $provider->getContentOwner()->getId(),
				'providerId' => $provider->getId(),
			), Router::ABSOLUTE_URL);
			$auUrls[] = $auUrl;
			$auFile = $titleDir . '/' . basename($auUrl);
			
			$xml = $this->twig->render('LOCKSSOMaticImportExportBundle:Configs:titledb.xml.twig', array(
				'aus' => $provider->getAus(),
			));
			$this->fs->dumpFile($auFile, $xml);
		}
		return $auUrls;
	}
}
