<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\UserRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:create-user';
    private $entityManager;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    protected function configure()
    {
        $this
            ->setName('app:create-user')
            ->setDescription('Creates a new user.')
            ->setHelp('This command allows you to create a user...');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $questionHelper = $this->getHelper('question');
        $helper = $this->getHelper('question');

        $email = $questionHelper->ask($input, $output, new Question('Please enter the user email: '));
        $plainPassword = $questionHelper->ask($input, $output, new Question('Please enter the user password: '));
        $firstName = $questionHelper->ask($input, $output, new Question('Please enter the user first name: '));
        $lastName = $questionHelper->ask($input, $output, new Question('Please enter the user last name: '));

        $role = $helper->ask($input, $output, new Question('Please enter the role name: '));
        $dbRole = $this->entityManager->getRepository(UserRole::class)->findOneBy(['name' => $role]);

        if (!$dbRole) {
            $output->writeln('Role doesnt exists');
            return Command::FAILURE;
        }

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $dbRole->addUser($user);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('User created successfully!');

        return Command::SUCCESS;
    }
}