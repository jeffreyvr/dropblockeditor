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

        // Injecting CSS into the editor frame.
        $styleElement = $dom->createElement('style', htmlentities(file_get_contents(__DIR__.'/../../public/editor.css')));
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
