<?php

namespace LOCKSSOMatic\CoreBundle\Utilities;

use Closure;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Liip\FunctionalTestBundle\Test\WebTestCase as BaseTestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Thin wrapper around Liip\FunctionalTestBundle\Test\WebTestCase to preload
 * fixtures into the database.
 */
abstract class AbstractTestCase extends BaseTestCase {

	/**
	 * Location of testing source data. It may be copied to the right place
	 * in a test set up function.
	 */
	const SRCDIR = "test/data-src";

	/**
	 * Expected location of testing data.
	 */
	const DSTDIR = "test/data";

	/**
	 * @var ObjectManager
	 */
	protected $em;

	/**
	 * As the fixtures load data, they save references. Use $this->references
	 * to get them.
	 * 
	 * @var ReferenceRepository
	 */
	protected $references;
	
	/**
	 * Returns a list of data fixture classes for use in one test class. They 
	 * will be loaded into the database before each test function in the class.
	 * 
	 * @return array()
	 */
	public function fixtures() {
		return array();
	}

	/**
	 * Return an array(src => dst) of testing files, which will be copied from
	 * src to dst before each test in the class. Source files starting with a
	 * dot(.) will be skipped during copy, but the corresponding destination
	 * file will be removed in test cleanup.
	 * 
	 * @see DefaultControllerAnonTest for example usage.
	 * 
	 * @return array()
	 */
	public function dataFiles() {
		return array();
	}
	
	protected function setUp() {
		parent::setUp();
		$this->em = $this->getContainer()->get('doctrine')->getManager();
		$fixtures = $this->fixtures();
		if (count($fixtures) > 0) {
			$this->references = $this->loadFixtures($fixtures)->getReferenceRepository();
		}
		
		foreach($this->dataFiles() as $src => $dst) {
			if(substr($src, 0, 1) === '.') {
				continue;
			}
			$dir = self::DSTDIR . '/' . dirname($dst);
			if(! file_exists($dir)) {
				mkdir($dir, 0755, true);
			}
			copy(self::SRCDIR . '/' . $src, self::DSTDIR . '/' . $dst);
		}
	}

    private function delTree($path) {
        $directoryIterator = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
        $fileIterator = new RecursiveIteratorIterator($directoryIterator, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($fileIterator as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($path);
    }
	
	public function tearDown() {
		parent::tearDown();
		$this->em->clear();
		$this->em->close();
		
		foreach($this->dataFiles() as $src => $dst) {
			$path = self::DSTDIR . '/' . $dst;
			if(file_exists($path)) {
				if(is_dir($path)) {
					$this->delTree($path);
				} else {
					unlink($path);			
				}
			}
		}
	}
}
