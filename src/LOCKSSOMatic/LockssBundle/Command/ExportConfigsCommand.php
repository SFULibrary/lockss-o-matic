<?php


namespace LOCKSSOMatic\LockssBundle\Command;

use Doctrine\ORM\EntityManager;
use LOCKSSOMatic\CoreBundle\Services\FilePaths;
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
use Symfony\Component\Routing\Router;

/**
 * Private Lockss network export command. Exports one or more lockss.xml
 * config files.
 */
class ExportConfigsCommand extends ContainerAwareCommand
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

    /**
     * {@inheritDocs}
     */
    public function configure() {
        $this->setName('lom:export:configs');
        $this->setDescription('Write all the configuration data to files.');
        $this->addArgument(
            'pln',
            InputArgument::IS_ARRAY,
            'Optional list of PLN ids to export.'
        );
        $this->addOption('dry-run', '-d', InputOption::VALUE_NONE, 'Export only, do not update any internal configs.');
    }

    /**
     * {@inheritdoc}
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null) {
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
     * Get a list of PLNs based on $plnIds or all PLNs.
     *
     * @param array $plnIds
     *
     * @return Pln[]
     */
    private function getPlns($plnIds = null) {
        if ($plnIds === null || count($plnIds) === 0) {
            return $this->em->getRepository('LOCKSSOMaticCrudBundle:Pln')->findAll();
        }

        return $this->em->getRepository('LOCKSSOMaticCrudBundle:Pln')->findById($plnIds);
    }

    /**
     * {@inheritdoc}
     *
     * @param InputInterface $input
     */
    public function execute(InputInterface $input, OutputInterface $output) {
        $activityLog = $this->getContainer()->get('activity_log');
        $activityLog->disable();
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);

        if (!file_exists($this->fp->getLockssDir())) {
            $this->fs->mkdir($this->fp->getLockssDir());
        }
        $plnIds = $input->getArgument('pln');

        // do this first, because it modifies the db.
        foreach ($this->getPlns($plnIds) as $pln) {
            $auUrls = $this->exportAus($pln);
            $this->updatePeerList($pln);
            $this->updateTitleDbs($pln, $auUrls);
            $this->updateKeystoreLocation($pln);
            $this->updatePluginRegistries($pln);
            $this->updateAuthentication($pln);
        }
        $this->em->flush();
        $this->em->clear();

        foreach ($this->getPlns($plnIds) as $pln) {
            $plnDir = $this->fp->getConfigsDir($pln);
            if (!file_exists($plnDir)) {
                $this->logger->warning("Creating {$plnDir}");
                $this->fs->mkdir($plnDir);
            }
            $this->exportKeystore($pln);
            $this->exportPlugins($pln);
            $this->exportManifests($pln);
            $this->exportLockssXml($pln);
        }
    }

    /**
     * Update the list of peers. The updated list is stored as properties
     * in the PLN entity.
     *
     * @param Pln $pln
     */
    public function updatePeerList(Pln $pln) {
        $boxes = $pln->getBoxes();
        $list = array();
        foreach ($boxes as $box) {
            $list[] = "{$box->getProtocol()}:[{$box->getIpAddress()}]:{$box->getPort()}";
        }
        $pln->setProperty('org.lockss.id.initialV3PeerList', $list);
    }

    /**
     * Update the list of title.xml file URLs, and store that list as
     * a property in the PLN.
     *
     * @param Pln $pln
     * @param type $auUrls
     */
    public function updateTitleDbs(Pln $pln, $auUrls) {
        $pln->setProperty('org.lockss.titleDbs', $auUrls);
    }

    /**
     * Update the plugin registry list for the PLN and store the list as
     * a property in the PLN.
     *
     * @param Pln $pln
     */
    public function updatePluginRegistries(Pln $pln) {
        $pln->setProperty('org.lockss.plugin.registries', $this->router->generate(
            'configs_plugin_list',
            array('plnId' => $pln->getId()),
            Router::ABSOLUTE_URL
        ));
    }

    /**
     * Update the PLN keystore location and store that as a property in the PLN.
     *
     * @param Pln $pln
     */
    public function updateKeystoreLocation(Pln $pln) {
        if ($pln->getKeystore()) {
            $pln->setProperty('org.lockss.plugin.keystore.location', $this->router->generate(
                'configs_plugin_keystore',
                array('plnId' => $pln->getId()),
                Router::ABSOLUTE_URL
            ));
        } else {
            $pln->deleteProperty('org.lockss.plugin.keystore.location');
        }
    }

    /**
     * Update the PLN's authentication credentials.
     *
     * @param Pln $pln
     */
    public function updateAuthentication(Pln $pln) {
        $prefix = 'org.lockss.ui.users.lomauth';
        $hash = hash('SHA256', $pln->getPassword());
        $pln->setProperty("{$prefix}.user", $pln->getUsername());
        $pln->setProperty("{$prefix}.password", "SHA-256:$hash");
        $pln->setProperty("{$prefix}.roles", 'debugRole');
    }

    /**
     * Export the PLN configuration file by exporting all of the properties
     * associated with the PLN.
     *
     * @param Pln $pln
     */
    public function exportLockssXml(Pln $pln) {
        $twig = $this->getContainer()->get('templating');
        $xml = $twig->render(
            'LOCKSSOMaticLockssBundle:Configs:lockss.xml.twig',
            array(
                'pln' => $pln,
            )
        );
        $path = $this->fp->getLockssXmlFile($pln);
        $this->fs->dumpFile($path, $xml);
    }

    /**
     * Export the keystore file.
     *
     * @param Pln $pln
     * @return type
     */
    public function exportKeystore(Pln $pln) {
        $keystore = $pln->getKeystore();
        if (!$keystore) {
            return;
        }
        $path = $this->fp->getPluginsExportDir($pln);
        if (!$this->fs->exists($path)) {
            $this->fs->mkdir($path);
        }
        $this->fs->copy($keystore->getPath(), "{$path}/lockss.keystore");
    }

    /**
     * Export all of the LOCKSS plugins to the file system so that LOCKSS
     * can harvest them as needed.
     *
     * @param Pln $pln
     */
    public function exportPlugins(Pln $pln) {
        $path = $this->fp->getPluginsExportDir($pln);
        if (!$this->fs->exists($path)) {
            $this->fs->mkdir($path);
        }
        $plugins = $pln->getPlugins();
        foreach ($plugins as $plugin) {
            $this->fs->copy($plugin->getPath(), $this->fp->getPluginsExportFile($pln, $plugin));
        }
        $html = $this->twig->render('LOCKSSOMaticLockssBundle:Configs:pluginList.html.twig', array(
            'pln' => $pln,
        ));
        $this->fs->dumpFile($this->fp->getPluginsManifestFile($pln), $html);
    }

    /**
     * Export the manifest files for the PLN.
     *
     * @param Pln $pln
     */
    public function exportManifests(Pln $pln) {
        // make this an iterator.
        foreach ($pln->getAus() as $au) {
            $manifestDir = $this->fp->getManifestDir($pln, $au->getContentprovider());
            if (!$this->fs->exists($manifestDir)) {
                $this->fs->mkdir($manifestDir);
            }
            $manifestUrl = $this->router->generate('configs_manifest', array(
                'plnId' => $pln->getId(),
                'ownerId' => $au->getContentprovider()->getContentOwner()->getId(),
                'providerId' => $au->getContentprovider()->getId(),
                'auId' => $au->getId(),
            ));
            $manifestFile = $manifestDir.'/'.basename($manifestUrl);
            $query = $this->em->createQuery('SELECT c FROM LOCKSSOMatic\CrudBundle\Entity\Content c WHERE c.au = :au');
            $query->setParameter('au', $au);
            $iterator = $query->iterate();
            $html = $this->twig->render('LOCKSSOMaticLockssBundle:Configs:manifest.html.twig', array(
                'content' => $iterator,
            ));
            $this->fs->dumpFile($manifestFile, $html);

            $this->em->clear();
            gc_collect_cycles();
        }
    }

    /**
     * Export the AU titledb.xml files, and return URLs for each exported file.
     *
     * @param Pln $pln
     * @return type
     */
    public function exportAus(Pln $pln) {
        $auUrls = array();
        foreach ($pln->getContentProviders() as $provider) {
            $titleDir = $this->fp->getTitleDbDir($pln, $provider);
            if (!$this->fs->exists($titleDir)) {
                $this->fs->mkdir($titleDir);
            }
            $auUrl = $this->router->generate('configs_titledb', array(
                'plnId' => $pln->getId(),
                'ownerId' => $provider->getContentOwner()->getId(),
                'providerId' => $provider->getId(),
                'filename' => "titledb_{$provider->getId()}.xml",
            ), Router::ABSOLUTE_URL);
            $auUrls[] = $auUrl;
            $auFile = $titleDir.'/'.basename($auUrl);

            $xml = $this->twig->render('LOCKSSOMaticLockssBundle:Configs:titledb.xml.twig', array(
                'aus' => $provider->getAus(),
            ));
            $this->fs->dumpFile($auFile, $xml);
        }

        return $auUrls;
    }
}
