<?php

namespace App\Command;

use App\Entity\UserRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CreateUserRoleCommand extends Command
{
    protected static $defaultName = 'app:create-user-role';
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setName('app:create-user-role')
            ->setDescription('Creates a new user role.')
            ->setHelp('This command allows you to create a user role...');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        $name = $helper->ask($input, $output, new Question('Please enter the role name: '));
        $isAdminAnswer = $helper->ask($input, $output, new Question('Is this role an admin role? (yes/no): '));

        $isAdmin = null;
        if (strtolower($isAdminAnswer) === 'yes') {
            $isAdmin = true;
        } elseif (strtolower($isAdminAnswer) === 'no') {
            $isAdmin = false;
        }

        if ($isAdmin === null) {
            $output->writeln('Invalid input for admin status. Please enter "yes" or "no".');
            return Command::FAILURE;
        }

        $userRole = new UserRole();
        $userRole->setName($name);
        $userRole->setIsAdmin($isAdmin);

        $this->entityManager->persist($userRole);
        $this->entityManager->flush();

        $output->writeln(sprintf('User role "%s" created successfully!', $name));

        return Command::SUCCESS;
    }
}