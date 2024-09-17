<?php

namespace WPDrill\Commands;

use DateTime;
use DateTimeZone;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WPDrill\DB\Migration\Migrator;
use WPDrill\Facades\Config;

class PluginBuildCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('plugin:build')
             ->setDescription('Build the plugin for production')
             ->setHelp('This build command allow you to build the plugin for production.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = time();
        $date = new DateTime("now", new DateTimeZone("UTC"));
        $outputDir = '/.dist/build-' . $date->format("Y-m-d\TH:i:s\Z");
        $output->writeln('<info>Building the plugin...</info>');
        $buildProcess = $this->process(['vendor/bin/php-scoper', 'add-prefix', '--force', '--output-dir=' . $outputDir]);
        $end = time();
        $totalTime = ($end - $start);
        if ($buildProcess->isSuccessful()) {
            $output->writeln('<success>Plugin build successfully!</success>');
            $output->writeln('<info>Time: ' . $totalTime . ' Seconds</info>');
        } else {
            $output->writeln('<error>' . $buildProcess->getErrorOutput() .'</error>');
            return Command::FAILURE;
        }


        return Command::SUCCESS;
    }
}

