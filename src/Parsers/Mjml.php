<?php

namespace Jeffreyvr\DropBlockEditor\Parsers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Blade;
use Symfony\Component\Process\Process;
use Jeffreyvr\DropBlockEditor\Blocks\Block;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Mjml extends Parser implements ParserInterface
{
    public function parse()
    {
        $content = '<mj-raw>'.$this->dropPlaceholderHtml().'</mj-raw>';

        $blocks = collect($this->blocks)
            ->map(function ($block) {
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

        $content = $this->createBaseView(['slot' => $content]);

        $method = config('dropblockeditor.mjml.method');

        if ($method === 'api') {
            $this->output = $this->parseWithApi($content);
        } elseif ($method === 'node') {
            $this->output = $this->parseWithNode($content);
        }

        return $this;
    }

    public function parseWithNode($content)
    {
        $process = new Process([
            config('dropblockeditor.node_binary'),
            config('dropblockeditor.mjml.binary'),
            '--noStdoutFileComment',
            '--stdin',
            '--s',
        ]);

        $process->setInput($content);

        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }

    public function parseWithApi($content)
    {
        $mjml = Http::withBasicAuth(config('dropblockeditor.mjml.api.username'), config('dropblockeditor.mjml.api.password'))
            ->post(config('dropblockeditor.mjml.api.url'), [
                'mjml' => $content,
            ])
            ->json();

        return $mjml['html'];
    }

}
