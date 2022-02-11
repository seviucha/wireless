<?php

namespace App\Command;

use App\DataFetcher\DataFetcherInterface;
use JsonException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProductOptionsCommand extends Command
{
    protected static $defaultName = 'wireless:product-offers';

    private DataFetcherInterface $dataFetcher;

    /**
     * ProductOptionsCommand constructor.
     *
     * @param DataFetcherInterface $dataFetcher
     */
    public function __construct(DataFetcherInterface $dataFetcher)
    {
        $this->dataFetcher = $dataFetcher;

        parent::__construct(self::$defaultName);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     *
     * @throws JsonException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->dataFetcher->getData();

        $output->writeln(json_encode($result, JSON_THROW_ON_ERROR));

        return Command::SUCCESS;
    }
}
