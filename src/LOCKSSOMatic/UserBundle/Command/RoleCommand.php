<?php

namespace LOCKSSOMatic\UserBundle\Command;

use Exception;
use FOS\UserBundle\Util\UserManipulator;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Change a user's role. This class was copied and modified from FOSUserBundle.
 */
abstract class RoleCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure() {
        $this->setDefinition(array(
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                new InputArgument('role', InputArgument::OPTIONAL, 'The role'),
                new InputOption('super', null, InputOption::VALUE_NONE, 'Instead specifying role, use this to quickly add the super administrator role'),
        ));
    }

    /**
     * {@inheritdoc}
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $email = $input->getArgument('email');
        $role = $input->getArgument('role');
        $super = (true === $input->getOption('super'));

        if (null !== $role && $super) {
            throw new InvalidArgumentException('You can pass either the role or the --super option (but not both simultaneously).');
        }

        if (null === $role && !$super) {
            throw new RuntimeException('Not enough arguments.');
        }

        $manipulator = $this->getContainer()->get('fos_user.util.user_manipulator');
        $this->executeRoleCommand($manipulator, $output, $email, $super, $role);
    }

    /**
     * {@inheritdoc}
     *
     * @param UserManipulator $manipulator
     * @param OutputInterface $output
     * @param string          $email
     * @param bool            $super
     * @param string          $role
     */
    abstract protected function executeRoleCommand(UserManipulator $manipulator, OutputInterface $output, $email, $super, $role);

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
                'Please choose a email:',
                function ($email) {
                    if (empty($email)) {
                        throw new Exception('Username can not be empty');
                    }

                    return $email;
                }
            );
            $input->setArgument('email', $email);
        }
        if ((true !== $input->getOption('super')) && !$input->getArgument('role')) {
            $role = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a role:',
                function ($role) {
                    if (empty($role)) {
                        throw new Exception('Role can not be empty');
                    }

                    return $role;
                }
            );
            $input->setArgument('role', $role);
        }
    }
}
