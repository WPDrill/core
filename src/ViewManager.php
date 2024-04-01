<?php

namespace WPDrill;

use WPDrill\Facades\Config;
use Twig\Environment;
use Twig\Lexer;
use Twig\Loader\FilesystemLoader;

class ViewManager
{
    protected Plugin $plugin;
    protected Environment $twig;
    protected bool $enableTemplating = true;

    public function __construct(Plugin $plugin)
    {
        $this->plugin           = $plugin;
        $this->enableTemplating = Config::get('view.enable_templating', true);
        $this->init();
    }

    protected function init()
    {
        $loader = new FilesystemLoader($this->plugin->getPath('resources/views'));
        $this->twig = new Environment($loader, [
            'cache' => $this->plugin->getPath('storage/cache/views')
        ]);

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
            return $this->twig->render($view . '.html', $data);
        }

        $this->makeEnableTemplatingAsDefault();

        return $this->renderRaw($view, $data);
    }

    protected function renderTwig(string $view, array $data = []): string
    {
        return $this->twig->render($view . '.html', $data);
    }

    public function renderRaw(string $view, array $data = []): string
    {
        ob_start();
        extract($data);
        require $this->plugin->getPath('resources/views/' . $view . '.php');

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
}
