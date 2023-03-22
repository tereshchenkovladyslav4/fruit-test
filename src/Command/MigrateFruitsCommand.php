<?php

namespace App\Command;

use App\Entity\Fruit;
use App\Repository\FruitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * A console command that migrate all the fruits.
 */
#[AsCommand(
    name: 'app:migrate-fruits',
    description: 'Fetch all fruits from external API and save them into DB',
    aliases: [ 'app:fruits' ]
)]
class MigrateFruitsCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MailerInterface $mailer,
        private readonly string $emailSender,
        private readonly FruitRepository $fruitRepository,
        private readonly HttpClientInterface $client,
    ) {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setHelp(<<<'HELP'
                The <info>%command.name%</info> command lists all the users registered in the application:
                
                  <info>php %command.full_name%</info>

                In addition to displaying the user list, you can also send this information to
                the email address specified in the <comment>--send-to</comment> option:

                  <info>php %command.full_name%</info> <comment>--send-to=fabien@symfony.com</comment>
                HELP
            )
            ->addOption('send-to', null, InputOption::VALUE_OPTIONAL, 'If set, the result is sent to the given email address');
    }

    /**
     * This method is executed after initialize(). It usually contains the logic
     * to execute to complete this command task.
     *
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $response = $this->client->request(
                'GET',
                'https://fruityvice.com/api/fruit/all'
            );

            $fruits = json_decode($response->getContent(), true);

            $newFruits = [];

            /** @var array<string, string> $item */
            foreach ($fruits as $item) {
                $name = (string) ($item['name']);
                $fruit = $this->fruitRepository->findOneBy([ 'name' => $name ]);
                if (!$fruit) {
                    $fruit = new Fruit();
                    $fruit->setName($name)
                          ->setFamily($item['family'])
                          ->setGenus($item['genus'])
                          ->setPlantOrder($item['order'])
                          ->setCalories((int) $item['nutritions']['calories'] * 100)
                          ->setCarbohydrates((int) $item['nutritions']['carbohydrates'] * 100)
                          ->setFat((int) $item['nutritions']['fat'] * 100)
                          ->setProtein((int) $item['nutritions']['protein'] * 100)
                          ->setSugar((int) $item['nutritions']['sugar'] * 100);

                    $newFruits[] = $fruit;

                    $this->entityManager->persist($fruit);
                    $this->entityManager->flush();
                }
            }

            $createFruitArray = static function (Fruit $fruit) {
                return [
                    $fruit->getId(),
                    $fruit->getName(),
                    $fruit->getFamily(),
                    $fruit->getGenus(),
                    $fruit->getPlantOrder(),
                ];
            };

            // Doctrine query returns an array of objects, and we need an array of plain arrays
            $fruitsAsPlainArrays = array_map($createFruitArray, $newFruits);

            // In your console commands you should always use the regular output type,
            // which outputs contents directly in the console window. However, this
            // command uses the BufferedOutput type instead, to be able to get the output
            // contents before displaying them. This is needed because the command allows
            // to send the list of users via email with the '--send-to' option
            $bufferedOutput = new BufferedOutput();
            $io = new SymfonyStyle($input, $bufferedOutput);
            $io->table(
                [ 'ID', 'Name', 'Family', 'Genus', 'Order' ],
                $fruitsAsPlainArrays
            );

            // instead of just displaying the table of users, store its contents in a variable
            $fruitsAsATable = $bufferedOutput->fetch();
            $output->write($fruitsAsATable);

            /** @var string|null $email */
            $email = $input->getOption('send-to');

            if (null !== $email) {
                $this->sendReport($fruitsAsATable, $email);
            }

            return Command::SUCCESS;
        } catch (TransportExceptionInterface|\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface|ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');

            return Command::FAILURE;
        }
    }

    /**
     * Sends the given $contents to the $recipient email address.
     *
     * @throws TransportExceptionInterface
     */
    private function sendReport(string $contents, string $recipient): void
    {
        $email = (new Email())
            ->from($this->emailSender)
            ->to($recipient)
            ->subject(sprintf('app:migrate-fruits report (%s)', date('Y-m-d H:i:s')))
            ->text($contents);

        $this->mailer->send($email);
    }
}
