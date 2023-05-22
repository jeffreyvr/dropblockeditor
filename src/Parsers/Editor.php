<?php

namespace Jeffreyvr\DropBlockEditor\Parsers;

use DOMDocument;

class Editor extends Parser implements ParserInterface
{
    public function output()
    {
        if ($this->context !== 'editor') {
            return $this->input;
        }

        $dom = new DOMDocument;

        $internalErrors = libxml_use_internal_errors(true);

        $dom->loadHTML($this->input);

        libxml_use_internal_errors($internalErrors);

        $configCss = config('dropblockeditor.preview_css');

        if ($configCss && file_exists(public_path($configCss))) {
            $editorCss = file_get_contents(public_path($configCss));
        } else {
            $editorCss = file_get_contents(__DIR__ . '/../../public/editor.css');
        };

        // Injecting CSS into the preview frame.
        $styleElement = $dom->createElement('style', htmlentities($editorCss));
        $styleElement->setAttribute('type', 'text/css');

        $head = $dom->getElementsByTagName('head')->item(0);

        if ($head) {
            $head->appendChild($styleElement);
        } else {
            $dom->appendChild($styleElement);
        }

        return $dom->saveHTML();
    }
}
