<?php

/* 
 * The MIT License
 *
 * Copyright 2014. Michael Joyce <ubermichael@gmail.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace LOCKSSOMatic\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * app:console command to purge all data from the database and reload it from
 * fixtures.
 */
class AuIdCommand extends ContainerAwareCommand
{
    /**
     * Configure the command - set the name and description.
     *
     */
    protected function configure()
    {
        $this->setName('lom:auid')
                ->setDescription('Report AuId for one or more AUs.');
		$this->addArgument('auid', InputArgument::IS_ARRAY, "One or more database ids");
    }

    /**
     * Entry point for the command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return type
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$auids = $input->getArgument('auid');
		$em = $this->getContainer()->get('doctrine')->getManager();
		$repo = $em->getRepository('LOCKSSOMaticCrudBundle:Au');
		$idGenerator = $this->getContainer()->get('crud.au.idgenerator');
		$propGenerator = $this->getContainer()->get('crud.propertygenerator');
		
		if( !$auids || count($auids) === 0) {
			return;
		}
		
		foreach($auids as $id) {
			$au = $repo->find($id);
			$output->writeln($au->getId());
			$output->writeln($propGenerator->generateSymbol($au, 'au_name'));
			$output->writeln("FROM AU:");
			$output->writeln("LOM AuId: " . $idGenerator->fromAu($au, false));
			$output->writeln("LOCKSS AuId: " . $idGenerator->fromAu($au, true));
			$output->writeln("FROM CONTENT:");
			$output->writeln("LOM AuId: " . $idGenerator->fromContent($au->getContent()->first(), false));
			$output->writeln("LOCKSS AuId: " . $idGenerator->fromContent($au->getContent()->first(), true));
		}
    }
}
