<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Utils\Validator;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * A console command that deletes users from the database.
 */
#[AsCommand(
    name: 'app:delete-user',
    description: 'Deletes users from the database'
)]
class DeleteUserCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private Validator $validator,
        private UserRepository $users,
        private LoggerInterface $logger
    ) {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'The username of an existing user')
            ->setHelp(<<<'HELP'
                The <info>%command.name%</info> command deletes users from the database:

                  <info>php %command.full_name%</info> <comment>username</comment>

                If you omit the argument, the command will ask you to
                provide the missing value:

                  <info>php %command.full_name%</info>
                HELP
            );
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        if (null !== $input->getArgument('username')) {
            return;
        }

        $this->io->title('Delete User Command Interactive Wizard');
        $this->io->text([
            'If you prefer to not use this interactive wizard, provide the',
            'arguments required by this command as follows:',
            '',
            ' $ php bin/console app:delete-user username',
            '',
            'Now we\'ll ask you for the value of all the missing command arguments.',
            '',
        ]);

        $username = $this->io->ask('Username', null, [ $this->validator, 'validateUsername' ]);
        $input->setArgument('username', $username);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string|null $username */
        $username = $input->getArgument('username');
        $username = $this->validator->validateUsername($username);

        /** @var User|null $user */
        $user = $this->users->findOneByUsername($username);

        if (null === $user) {
            throw new RuntimeException(sprintf('User with username "%s" not found.', $username));
        }

        $userId = $user->getId();

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $userUsername = $user->getUsername();
        $userEmail = $user->getEmail();

        $this->io->success(sprintf('User "%s" (ID: %d, email: %s) was successfully deleted.', $userUsername, $userId, $userEmail));

        // Logging is helpful and important to keep a trace of what happened in the software runtime flow.
        $this->logger->info('User "{username}" (ID: {id}, email: {email}) was successfully deleted.', [
            'username' => $userUsername,
            'id'       => $userId,
            'email'    => $userEmail
        ]);

        return Command::SUCCESS;
    }
}
