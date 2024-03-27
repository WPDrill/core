<?php

namespace WPDrill\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WPDrill\DB\Migration\Migrator;

class MigrateRefreshCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('db:refresh')
            ->setDescription('Run the database refresh migrations')
            ->setHelp('This command allows you to run the database migrations refresh.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $migrator = new Migrator(WPDRILL_ROOT_PATH . '/database/migrations', $input, $output);
        $migrator->reset();
        $migrator->run();
        return Command::SUCCESS;
    }
}
