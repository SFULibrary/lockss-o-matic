<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new LOCKSSOMatic\SWORDBundle\LOCKSSOMaticSWORDBundle(),
            new LOCKSSOMatic\CRUDBundle\LOCKSSOMaticCRUDBundle(),
            new LOCKSSOMatic\PLNExporterBundle\LOCKSSOMaticPLNExporterBundle(),
            new LOCKSSOMatic\PLNImporterBundle\LOCKSSOMaticPLNImporterBundle(),
            new LOCKSSOMatic\PLNMonitorBundle\LOCKSSOMaticPLNMonitorBundle(),
            new LOCKSSOMatic\CoreBundle\LOCKSSOMaticCoreBundle(),
            new Braincrafted\Bundle\BootstrapBundle\BraincraftedBootstrapBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Problematic\AclManagerBundle\ProblematicAclManagerBundle(),
            new LOCKSSOMatic\UserBundle\LOCKSSOMaticUserBundle(),
            new LOCKSSOMatic\PluginBundle\LOCKSSOMaticPluginBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new LOCKSSOMatic\LoggingBundle\LOCKSSOMaticLoggingBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
