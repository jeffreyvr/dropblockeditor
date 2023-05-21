<?php

return [
    'include_js' => true,

    'include_css' => true,

    /*
    |--------------------------------------------------------------------------
    | Customize editor
    |--------------------------------------------------------------------------
    |
    | preview_css - Overide with custom css for the preview.
    | brand - Custom branding for the editor.
    |
    */

    'preview_css' => null,

    'brand' => [
        'logo' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">   <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" /> </svg> ',
        'colors' => [
            'topbar_bg' => 'bg-white text-gray-800',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Register global blocks
    |--------------------------------------------------------------------------
    |
    | Available blocks if not declared in the livewire dropblockeditor component
    |
    */

    'blocks' => [
        Jeffreyvr\DropBlockEditor\Blocks\Example::class,
    ],

    'parsers' => [
        // Jeffreyvr\DropBlockEditor\Parsers\Mjml::class,
        Jeffreyvr\DropBlockEditor\Parsers\Html::class,
        Jeffreyvr\DropBlockEditor\Parsers\Editor::class,
    ],

    'buttons' => [
        'dropblockeditor-example-button',
    ],

    'mjml' => [
        'method' => env('DROPBLOCKEDITOR_MJML_METHOD', 'api'),

        'binary' => env('DROPBLOCKEDITOR_MJML_BINARY', '../node_modules/.bin/mjml'),

        'api' => [
            'url' => env('DROPBLOCKEDITOR_MJML_API_URL', 'https://api.mjml.io/v1/render'),
            'username' => env('DROPBLOCKEDITOR_MJML_API_USERNAME'),
            'password' => env('DROPBLOCKEDITOR_MJML_API_PASSWORD'),
        ],
    ],

    'node_binary' => env('DROPBLOCKEDITOR_NODE_BINARY', '/usr/local/bin/node'),
];
