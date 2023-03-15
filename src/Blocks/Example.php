<?php

namespace Jeffreyvr\DropBlockEditor\Blocks;

class Example extends Block
{
    public $title = 'Example';

    public $icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">   <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12H12m-8.25 5.25h16.5" /> </svg>';

    public $blockEditComponent = 'dropblockeditor-example';

    public $data = [
        'title' => 'Drop it like it\'s hot! 🔥',
        'content' => 'This block example block gives you a glimpse of what you could do with this editor. I hope you enjoy using it!',
    ];

    public function render()
    {
        return <<<'blade'
        <div class="mx-auto my-4 max-w-screen-md p-8 bg-white rounded-xl">
            <div class="text-3xl font-extrabold mb-2 text-orange-600">{{ $title }}</div>
            <div class="text-lg text-gray-700">{{ $content }}</div>
        </div>
        blade;
    }
}
