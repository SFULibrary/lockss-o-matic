<?php

namespace LOCKSSOMatic\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Change a user's password.
 */
class ChangePasswordCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure() {
        $this->setName('fos:user:change-password')->setDescription('Change the password of a user.')->setDefinition(array(
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password'),
        ))->setHelp(<<<EOT
The <info>fos:user:change-password</info> command changes the password of a user:

  <info>php app/console fos:user:change-password user@example.com</info>

This interactive shell will first ask you for a password.

You can alternatively specify the password as a second argument:

  <info>php app/console fos:user:change-password user@example.com mypassword</info>

EOT
        );
    }

    /**
     * {@inheritdoc}
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        $manipulator = $this->getContainer()->get('fos_user.util.user_manipulator');
        $manipulator->changePassword($email, $password);

        $output->writeln(sprintf('Changed password for user <comment>%s</comment>', $email));
    }

    /**
     * {@inheritdoc}
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output) {
        if (!$input->getArgument('email')) {
            $email = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please give the email:',
                function ($email) {
                    if (empty($email)) {
                        throw new \Exception('Username can not be empty');
                    }

                    return $email;
                }
            );
            $input->setArgument('email', $email);
        }

        if (!$input->getArgument('password')) {
            $password = $this->getHelper('dialog')->askHiddenResponseAndValidate(
                $output,
                'Please enter the new password:',
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
