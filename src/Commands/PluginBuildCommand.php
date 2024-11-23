<?php

namespace WPDrill\Commands;

use DateTime;
use DateTimeZone;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use WPDrill\Facades\Config;

class PluginBuildCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('plugin:build')
             ->addOption('prod', 'p', null, 'Build the plugin for production, this build remove dev dependencies')
             ->addOption('set-version', 'x', InputOption::VALUE_REQUIRED, 'Added version to the newly build')
             ->addOption('archive', 'a', InputOption::VALUE_REQUIRED, 'Archive the build. Supported formats: zip, tar')
             ->setDescription('Build the plugin for production')
             ->setHelp('This build command allow you to build the plugin for production.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = time();

        $io = new SymfonyStyle($input, $output);
        $version = '';

        $date = new DateTime("now", new DateTimeZone("UTC"));
        $buildName = 'build-' . $date->format("Y-m-d\THis\Z");
        if ($input->getOption('prod')) {
            $buildName .= '-prod';
        }

        if ($input->getOption('set-version')) {
            $version = $input->getOption('set-version');
            $buildName .= '-' . $version;
        }

        $outputDir = WPDRILL_ROOT_PATH . '/' . Config::get('plugin.build.output_dir', '.dist');
        $buildDir = $outputDir . '/' . $buildName;

        $io->newLine();
        $output->writeln('Building the plugin...');

        if ($input->getOption('prod')) {
            $this->process(['composer', 'install', '--no-dev']);
        }

        $this->process(['./wpdrill', 'view:cache']);
        $this->executeCommandsBefore(WPDRILL_ROOT_PATH);

        if (!file_exists(WPDRILL_ROOT_PATH . '/php-scoper')) {
            $output->writeln([
                '<error>No php-scoper executable file available</error>',
            ]);

            return Command::FAILURE;
        }

        $buildProcess = $this->process(['./php-scoper', 'add-prefix', '--force', '--output-dir=' . $outputDir]);

        if (!$buildProcess->isSuccessful()) {
            $output->writeln('<error>' . $buildProcess->getErrorOutput() .'</error>');
            return Command::FAILURE;
        }

        if ($version !== '') {
            $io->newLine();
            $this->updateBuildVersion($version, $buildDir);
        }

        $this->executeCommandsAfter($buildDir);
        if ($input->getOption('prod')) {
            $this->process(['composer', 'install', '--dev']);
            $io->newLine();
            $this->cleanup($buildDir);
        }

        if ($archive = $input->getOption('archive')) {
            $io->newLine();
            $this->archive($outputDir, $buildName, $archive);
        }

        $end = time();
        $totalTime = ($end - $start);

        $io->newLine();
        $output->writeln('<info>Plugin build successfully completed!</info>');
        $io->newLine();
        $output->writeln([
            '<comment>Build Name: ' . $buildName. '</comment>',
            '<comment>Time: ' . $totalTime . ' Seconds</comment>'
        ]);

        return Command::SUCCESS;
    }

    protected function executeCommandsBefore(string $buildDir)
    {
        $commands = Config::get('plugin.build.commands.before', []);

        $this->executeCommands($commands, $buildDir);
    }

    protected function executeCommandsAfter(string $buildDir)
    {
        $commands = Config::get('plugin.build.commands.after', []);

        $this->executeCommands($commands, $buildDir);
    }

    protected function executeCommands(array $commands, string $dir)
    {
        $this->output->writeln('<comment>Executing commands: </comment>');
        foreach ($commands as $command) {
            $cmd = ['bash', '-c', 'cd ' . $dir . ' && ' . implode(' ', $command)];
            $this->output->writeln(' > ' . implode(' ', $command) . ' ...');
            try {
                $this->process($cmd);
                $this->output->writeln('<info> > ' . implode(' ', $command) . ' [DONE]</info>');
            } catch (\Exception $e) {
                $this->output->writeln('<error>' . $e->getMessage() . '</error>');
            }

            sleep(1);
        }
    }

    protected function cleanup(string $buildDir)
    {
        $files = Config::get('plugin.build.cleanup', []);
        $this->output->writeln('<comment>Cleaning: </comment>');
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
                $this->output->writeln('<info>' . $file . ' ... [DELETED]</info>');
            } catch (\Exception $e) {
                $this->output->writeln('<error>' . $e->getMessage() . '</error>');
            }

        }
    }

    protected function updateBuildVersion(string $version, string $buildDir): void
    {
        $slug = Config::get('plugin.slug');
        $pluginFile = $buildDir . '/' . $slug . '.php';
        $pluginFileContents = file_get_contents($pluginFile);

        $pluginFileContents = preg_replace('/Version: (.*)/', 'Version: ' . $version, $pluginFileContents);
        file_put_contents($pluginFile, $pluginFileContents);

        $pluginConfigFile = $buildDir . '/config/plugin.php';
        $pluginConfigFileContents = file_get_contents($pluginConfigFile);
        $pluginConfigFileContents = preg_replace('/(\'version\' => \s*\')([0-9\.]+)(\')/', '\'version\' => \'' .$version . '\'', $pluginConfigFileContents);
        file_put_contents($pluginConfigFile, $pluginConfigFileContents);

        $this->output->writeln('<info>Version updated to - ' . $version . ' [DONE]</info>');

    }

    protected function archive(string $outputDir, string $archiveName, string $ext = 'zip'): void
    {
        $this->output->writeln('<comment>Archiving the build ...</comment>');
        $supportedFormats = ['zip', 'tar'];

        if (!in_array($ext, $supportedFormats)) {
            $this->output->writeln('<error>Unsupported archive format</error>');
            return;
        }

        $archive = $archiveName . '.' . $ext;

        if ($ext === 'zip') {
            $cmd = ['bash', '-c', 'cd ' . $outputDir . ' && zip -r ' . $archive . ' ' . $archiveName . '/.'];
        }

        if ($ext === 'tar') {
            $cmd = [ 'bash', '-c', 'cd ' . $outputDir . ' && tar -cvf ' . $archive . ' ' . $archiveName . '/.' ];
        }

        $cmd[2] = $cmd[2] . ' && rm -rf ./'. $archiveName;

        $this->process($cmd);

        $this->output->writeln('<info>Archived [DONE]</info>');
    }
}
