<?php

namespace App\Command;

use App\Exception\ValidationException;
use App\Factory\CustomerRequestFactory;
use App\Factory\ProviderFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;

class MapRequestCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('app:map-request')
            ->setDescription('Map request for insurance provider')
            ->addOption('input', 'i', InputOption::VALUE_REQUIRED, 'Input JSON file path')
            ->addOption('provider', 'p', InputOption::VALUE_REQUIRED, 'Provider name (e.g., acme)', 'acme');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            // Read JSON input
            $jsonFile = $input->getOption('input');
            if (!file_exists($jsonFile)) {
                throw new \RuntimeException("Input file not found: $jsonFile");
            }

            $jsonContent = file_get_contents($jsonFile);
            
            // Create and validate customer request directly from JSON
            $customerRequest = CustomerRequestFactory::createFromJson($jsonContent);

            // Get provider
            $providerName = $input->getOption('provider');
            $provider = ProviderFactory::getInstance()->getProvider($providerName);

            // Transform and format request
            $providerRequest = $provider->transformRequest($customerRequest);
            $formattedResponse = $provider->formatResponse($providerRequest);

            $io->success('Request processed successfully');
            $output->writeln($formattedResponse);
            return Command::SUCCESS;

        } catch (ValidationException $e) {
            $io->error('Validation failed:');
            $io->listing(explode("\n", $e->getMessage()));
            return Command::FAILURE;
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}