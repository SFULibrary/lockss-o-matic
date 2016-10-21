<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LOCKSSOMatic\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Overrides the CreateUserCommand from FOSUserBundle to add support for
 * fullname and institution.
 */
class CreateUserCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('fos:user:create')
            ->setDescription('Create a user.')
            ->setDefinition(array(
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password'),
                new InputArgument('fullname', InputArgument::REQUIRED, 'The full name'),
                new InputArgument('institution', InputArgument::REQUIRED, 'The institution'),
                new InputOption('super-admin', null, InputOption::VALUE_NONE, 'Set the user as super admin'),
                new InputOption('inactive', null, InputOption::VALUE_NONE, 'Set the user as inactive'),
            ))
            ->setHelp(<<<EOT
The <info>fos:user:create</info> command creates a user:

  <info>php app/console fos:user:create user@example.com</info>

This interactive shell will ask you for a password.

You can alternatively specify the email and password as the first and second arguments:

  <info>php app/console fos:user:create matthieu@example.com mypassword</info>

You can create a super admin via the super-admin flag:

  <info>php app/console fos:user:create admin@example.com --super-admin</info>

You can create an inactive user (will not be able to log in):

  <info>php app/console fos:user:create user@example.com --inactive</info>

EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $fullname = $input->getArgument('fullname');
        $institution = $input->getArgument('institution');
        $inactive = $input->getOption('inactive');
        $superadmin = $input->getOption('super-admin');

        $manipulator = $this->getContainer()->get('lomuserbundle.user_manipulator');
        $manipulator->create($email, $password, $fullname, $institution, !$inactive, $superadmin);

        $output->writeln(sprintf('Created user <comment>%s</comment>', $email));
    }

    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('email')) {
            $email = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose an email:',
                function ($email) {
                    if (empty($email)) {
                        throw new \Exception('Email can not be empty');
                    }

                    return $email;
                }
            );
            $input->setArgument('email', $email);
        }

        if (!$input->getArgument('fullname')) {
            $fullname = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a fullname:',
                function ($fullname) {
                    if (empty($fullname)) {
                        throw new \Exception('fullname can not be empty');
                    }

                    return $fullname;
                }
            );
            $input->setArgument('fullname', $fullname);
        }

        if (!$input->getArgument('institution')) {
            $institution = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a institution:',
                function ($institution) {
                    if (empty($institution)) {
                        throw new \Exception('institution can not be empty');
                    }

                    return $institution;
                }
            );
            $input->setArgument('institution', $institution);
        }

        if (!$input->getArgument('password')) {
            $password = $this->getHelper('dialog')->askHiddenResponseAndValidate(
                $output,
                'Please choose a password:',
                function ($password) {
                    if (empty($password)) {
                        throw new \Exception('Password can not be empty');
                    }

                    return $password;
                }
            );
            $input->setArgument('password', $password);
        }
    }
}
