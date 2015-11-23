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

use Symfony\Component\Console\Output\OutputInterface;
use FOS\UserBundle\Util\UserManipulator;

/**
 * @author Antoine Hérault <antoine.herault@gmail.com>
 * @author Lenar Lõhmus <lenar@city.ee>
 */
class DemoteUserCommand extends RoleCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('fos:user:demote')
            ->setDescription('Demote a user by removing a role')
            ->setHelp(<<<EOT
The <info>fos:user:demote</info> command demotes a user by removing a role

  <info>php app/console fos:user:demote user@example.com ROLE_CUSTOM</info>
  <info>php app/console fos:user:demote --super user@example.com</info>
EOT
            );
    }

    protected function executeRoleCommand(UserManipulator $manipulator, OutputInterface $output, $email, $super, $role)
    {
        if ($super) {
            $manipulator->demote($email);
            $output->writeln(sprintf('User "%s" has been demoted as a simple user.', $email));
        } else {
            if ($manipulator->removeRole($email, $role)) {
                $output->writeln(sprintf('Role "%s" has been removed from user "%s".', $role, $email));
            } else {
                $output->writeln(sprintf('User "%s" didn\'t have "%s" role.', $email, $role));
            }
        }
    }
}
