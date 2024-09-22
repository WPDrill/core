<?php

namespace WPDrill\Commands;

use DateTime;
use DateTimeZone;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use WPDrill\DB\Migration\Migrator;
use WPDrill\Facades\Config;

class PluginBuildCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('plugin:build')
             ->addOption('prod', 'p', null, 'Build the plugin for production, this build remove dev dependencies')
             ->setDescription('Build the plugin for production')
             ->setHelp('This build command allow you to build the plugin for production.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = time();

        $io = new SymfonyStyle($input, $output);

        $date = new DateTime("now", new DateTimeZone("UTC"));
        $buildName = 'build-' . $date->format("Y-m-d\TH:i:s\Z");
        if ($input->getOption('prod')) {
            $buildName .= '-prod';
        }

        $outputDir = WPDRILL_ROOT_PATH . '/' . Config::get('plugin.build.output_dir', '.dist') . '/' . $buildName;


        $io->newLine();
        $output->writeln('Building the plugin...');

        if ($input->getOption('prod')) {
            $this->process(['composer', 'install', '--no-dev']);
        }

        $buildProcess = $this->process(['./vendor/bin/php-scoper', 'add-prefix', '--force', '--output-dir=' . $outputDir]);

        if ($buildProcess->isSuccessful()) {
            $io->newLine();
            $this->executeCommands($outputDir);
            if ($input->getOption('prod')) {
                $this->process(['composer', 'install']);
                $io->newLine();
                $this->cleanup($outputDir);
            }

            $end = time();
            $totalTime = ($end - $start);

            $io->newLine();
            $output->writeln('<info>Plugin build successfully!</info>');
            $io->newLine();
            $output->writeln([
                '<comment>Build Name: ' . $buildName. '</comment>',
                '<comment>Time: ' . $totalTime . ' Seconds</comment>'
            ]);
        } else {
            $output->writeln('<error>' . $buildProcess->getErrorOutput() .'</error>');
            return Command::FAILURE;
        }


        return Command::SUCCESS;
    }


    protected function executeCommands(string $buildDir)
    {
        $commands = Config::get('plugin.build.commands', []);
        $this->output->writeln('<info>Executing commands: </info>');
        foreach ($commands as $command) {
            $cmd = ['bash', '-c', 'cd ' . $buildDir . ' && ' . implode(' ', $command)];
            $this->output->writeln('<comment> > ' . implode(' ', $command) . ' ...</comment>');
            try {
                $this->process($cmd);
            } catch (\Exception $e) {
                $this->output->writeln('<error>' . $e->getMessage() . '</error>');
            }

            sleep(1);
        }
    }

    protected function cleanup(string $buildDir)
    {
        $files = Config::get('plugin.build.cleanup', []);
        $this->output->writeln('<info>Cleaning: </info>');
        foreach ($files as $file) {
            try {
                if ($file === '/') {
                    throw new \Exception('You can not delete root directory');
                }

                if (str_starts_with($file, '/')) {
                    throw new \Exception('You can not delete system files');
                }

                if (is_dir($buildDir . '/' . $file)) {
                    $cmd = ['bash', '-c', 'cd ' . $buildDir . ' && rm -rf ./' . $file];
                } else {
                    $cmd = ['bash', '-c', 'cd ' . $buildDir . ' && rm ./' . $file];
                }

                $this->process($cmd);
                $this->output->writeln('<comment>' . $file . ' ... [DELETED]</comment>');
            } catch (\Exception $e) {
                $this->output->writeln('<error>' . $e->getMessage() . '</error>');
            }

        }
    }
}
