<?php

namespace Jeffreyvr\DropBlockEditor\Parsers;

interface ParserInterface
{
    public function base($base);

    public function context($context);

    public function parse();

    public function output();
}
