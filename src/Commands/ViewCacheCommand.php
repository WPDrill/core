<?php

namespace WPDrill\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WPDrill\Plugin;
use WPDrill\Views\ViewManager;

class ViewCacheCommand extends BaseCommand
{
    protected ViewManager $view;

    public function __construct(Plugin $plugin, ?string $name = null ) {
        $this->view = new ViewManager($plugin);

        parent::__construct( $name );
    }

    protected function configure()
    {
        $this->setName('view:cache')
            ->setDescription('Compiled and cache all the twig files')
            ->setHelp('This command allows compiled and cached all the twig files.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<comment>Compiling and caching all the twig files...</comment>');
        $this->view->compile();

        $output->writeln('<info>Twig files compiled and cached successfully.</info>');

        return Command::SUCCESS;
    }
}

