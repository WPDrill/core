<?php

namespace WPDrill;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use WPDrill\Facades\Config;
use Twig\Environment;
use Twig\Lexer;
use Twig\Loader\FilesystemLoader;

class ViewManager
{
    protected Plugin $plugin;
    protected Environment $twig;
    protected bool $enableTemplating = true;
    protected string $templateExtension;
    protected string $templatePath;
    protected FilesystemLoader $loader;

    public function __construct(Plugin $plugin)
    {
        $this->plugin           = $plugin;
        $this->enableTemplating = Config::get('view.enable_templating', true);
        $this->templateExtension = Config::get('view.template_extension', 'twig');
        $this->templateExtension = '.' . $this->templateExtension;
        $this->templatePath = $this->plugin->getPath(Config::get('view.template_path', 'resources/views'));
        $this->init();
    }

    protected function init()
    {
        $this->loader = new FilesystemLoader($this->templatePath);
        $twigConfig = [
            'cache' => $this->plugin->getPath( Config::get('view.cache_path', 'storage/cache/views')),
            'cache_lifetime' => Config::get('view.cache_lifetime', 0),
        ];

        if (!$this->plugin->isProduction()) {
            $twigConfig['debug'] = true;
            $twigConfig['auto_reload'] = true;
        }

        $this->twig = new Environment($this->loader, $twigConfig);

        $lexer = new Lexer($this->twig, [
            'tag_comment'   => Config::get('view.lexer.tag_comment', ['{#', '#}']),
            'tag_block'     => Config::get('view.lexer.tag_block', ['{%', '%}']),
            'tag_variable'  => Config::get('view.lexer.tag_variable', ['{{', '}}']),
            'interpolation' => Config::get('view.lexer.interpolation', ['#{', '}']),
        ]);

        $this->twig->setLexer($lexer);
    }

    public function templating(bool $enable): self
    {
        $this->enableTemplating = $enable;

        return $this;
    }

    protected function makeEnableTemplatingAsDefault(): void
    {
        $this->enableTemplating = Config::get('view.enable_templating', true);
    }

    public function render(string $view, array $data = []): string
    {
        if ($this->enableTemplating) {
            $this->makeEnableTemplatingAsDefault();
            return $this->renderTwig($view, $data);
        }

        $this->makeEnableTemplatingAsDefault();

        return $this->renderRaw($view, $data);
    }

    protected function renderTwig(string $view, array $data = []): string
    {
        return $this->twig->render($view . $this->templateExtension, $data);
    }

    public function renderRaw(string $view, array $data = []): string
    {
        ob_start();
        extract($data);
        require $this->templatePath . '/' . $view . '.php';

        return ob_get_clean();
    }

    public function output(string $view, array $data = []): void
    {
        if ($this->enableTemplating) {
            $this->makeEnableTemplatingAsDefault();
            echo $this->renderTwig($view, $data);

            return;
        }

        $this->makeEnableTemplatingAsDefault();

        echo $this->renderRaw($view, $data);

        return;
    }

    public function print(string $view, array $data = []): void
    {
        $this->output($view, $data);
    }

    public function compile()
    {
        $dir =  $this->templatePath;

        $directory = new RecursiveDirectoryIterator($dir);
        $iterator = new RecursiveIteratorIterator($directory);
        $regex = new RegexIterator($iterator, '/\\' . $this->templateExtension .'$/');  // Match .twig files
        foreach ($regex as $file) {
            $relativePath = str_replace($dir . DIRECTORY_SEPARATOR, '', $file->getPathname());
            echo $relativePath . " ...\n";
            try {
                // Load each template to compile and cache it
                $this->twig->load($relativePath);
                echo $relativePath . ' - [COMPILED]' . "\n";
            } catch (Exception $e) {
                echo $relativePath . ' - [FAILED]' . "\n";
            }
        }
    }
}
