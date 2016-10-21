<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace LOCKSSOMatic\CoreBundle\Services;

use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;

/**
 * Description of FilePathsTest
 *
 * @author michael
 */
class FilePathsTest extends AbstractTestCase {

    /**
     * @var LOCKSSOMatic\CoreBundle\Services\FilePath
     */
    protected $fp;
    protected $root;

    public function setUp() {
        parent::setUp();

        $this->fp = $this->getContainer()->get('lom.filepaths');
    }

    public function fixtures() {
        return array(
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContent',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadDeposit',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContentOwner',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContentProvider',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadAu',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPlugin',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPln',
        );
    }

    public function testGet() {
        $this->assertInstanceOf('LOCKSSOMatic\CoreBundle\Services\FilePaths', $this->fp);
    }

    public function testGetLockssDir() {
        $this->assertStringEndsWith('/data/lockss', $this->fp->getLockssDir());
    }

    public function testGetPluginsDir() {
        $this->assertStringEndsWith('/data/lockss/plugins', $this->fp->getPluginsDir());
    }

    public function testGetConfigsDir() {
        $this->assertStringEndsWith('data/plnconfigs/1', $this->fp->getConfigsDir(
                $this->references->getReference('pln')
        ));
    }

    public function testGetLockssXmlFile() {
        $this->assertStringEndsWith(
            'data/plnconfigs/1/properties/lockss.xml', 
            $this->fp->getLockssXmlFile(
                $this->references->getReference('pln')
            )
        );
    }

    public function testGetPluginsExportDir() {
        $this->assertStringEndsWith(
            'data/plnconfigs/1/plugins', 
            $this->fp->getPluginsExportDir(
                $this->references->getReference('pln')
            )
        );
    }
    
    public function testGetPluginsExportFile() {
        $this->assertStringEndsWith(
            'data/plnconfigs/1/plugins/TestPlugin.jar', 
            $this->fp->getPluginsExportFile(
                $this->references->getReference('pln'),
                $this->references->getReference('plugin')
            )
        );
    }
    
    public function testGetManifestDir() {
        $this->assertStringEndsWith(
            'data/plnconfigs/1/manifests/1/1', 
            $this->fp->getManifestDir(
                $this->references->getReference('pln'),
                $this->references->getReference('contentprovider')
            )
        );
    }
    
    public function testGetManifestPath() {
        $this->assertStringEndsWith(
            'data/plnconfigs/1/manifests/1/1/manifest_1.html', 
            $this->fp->getManifestPath(
                $this->references->getReference('au')
            )
        );
    }
    
    public function testGetTitleDbDir() {
        $this->assertStringEndsWith(
            'data/plnconfigs/1/titledbs/1/1', 
            $this->fp->getTitleDbDir(
                $this->references->getReference('pln'),
                $this->references->getReference('contentprovider')
            )
        );
    }
}
