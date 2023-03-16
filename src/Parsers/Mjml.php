<?php

namespace Jeffreyvr\DropBlockEditor\Parsers;

use Illuminate\Support\Facades\Blade;
use Jeffreyvr\DropBlockEditor\Blocks\Block;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Mjml extends Parser implements ParserInterface
{
    public function parse()
    {
        $content = '<mj-raw>'.$this->dropPlaceholderHtml().'</mj-raw>';

        $blocks = collect($this->blocks)
            ->map(function($block) {
                return Block::fromName($block['class'])
                    ->data($block['data']);
            })
            ->map(function ($block, $key) {
                $view = $block->makeView();

                if ($this->context === 'editor') {
                    $this->setBlockArguments('before', ['before' => '<mj-raw>', 'after' => '</mj-raw>'], 'wrap');
                    $this->setBlockArguments('after', ['before' => '<mj-raw>', 'after' => '</mj-raw>'], 'wrap');

                    $view = $this->prepareBlockForEditor(['id' => $key, 'blockHtml' => $view]);
                }

                return Blade::render($view, $block->getData());
            })
            ->values();

        if (! $blocks->isEmpty()) {
            $content = $blocks->implode("\n");
        }

        $content = view($this->base, ['slot' => $content])->render();

        $process = new Process([
            config('dropblockeditor.node_binary'),
            config('dropblockeditor.mjml_binary'),
            '--noStdoutFileComment',
            '--stdin',
            '--s',
        ]);

        $process->setInput($content);

        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $this->output = $process->getOutput();

        return $this;
    }
}
