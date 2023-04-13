<?php

namespace App\Command;

use App\Service\FruitService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * A console command that fetches data from an API and saves it to the database.
 */
class FruitsFetchCommand extends Command
{
    protected static $defaultName = 'fruits:fetch';

    private FruitService $fruitService;

    public function __construct(FruitService $fruitService)
    {
        $this->fruitService = $fruitService;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Fetches fruits data from an API and saves it to the database');
    }

    /**
     * Executes the console command.
     *
     * @param InputInterface $input The input interface.
     * @param OutputInterface $output The output interface.
     *
     * @return int The command exit code.
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->fruitService->fetchAndSaveData();

        if ($result) {
            $output->writeln('Data fetched and saved successfully');
            return Command::SUCCESS;
        } else {
            $output->writeln('Failed to fetch or save data');
            return Command::FAILURE;
        }
    }
}