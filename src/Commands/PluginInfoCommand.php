<?php

namespace WPDrill\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WPDrill\DB\Migration\Migrator;
use WPDrill\Facades\Config;

class PluginInfoCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('plugin:info')
            ->setDescription('Display the plugin information')
            ->setHelp('This command allows you to display the plugin information.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = Config::get('plugin');

        $table = new Table($output);
        $table
            ->setRows([
                ['Name', $config['name']],
                new TableSeparator(),
                ['Version', $config['version']],
            ])
        ;
        $table->render();

        return Command::SUCCESS;
    }
}

