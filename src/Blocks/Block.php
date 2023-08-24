<?php

namespace Jeffreyvr\DropBlockEditor\Blocks;

use Illuminate\View\View;

abstract class Block
{
    public $title;

    public $icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 017.5 5.25h9a2.25 2.25 0 012.25 2.25v9a2.25 2.25 0 01-2.25 2.25h-9a2.25 2.25 0 01-2.25-2.25v-9z" /></svg>';

    public $blockEditComponent;

    public $data = [];

    abstract public function render();

    public function getIcon()
    {
        return $this->icon;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getData()
    {
        return $this->data;
    }

    public function data($data)
    {
        $this->data = $data;

        return $this;
    }

    public function makeView()
    {
        if ($this->render() instanceof View) {
            return file_get_contents($this->render()->getPath());
        }

        return $this->render();
    }

    public static function fromName($name, ...$args)
    {
        return new $name($args);
    }

    public function toArray()
    {
        return [
            'data' => $this->data,
            'class' => get_class($this),
        ];
    }
}
