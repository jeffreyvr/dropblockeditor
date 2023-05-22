<?php

return [

    /*
    |-----------------------------------------------------------------------------
    | Customize Editor
    |-----------------------------------------------------------------------------
    |
    | include_js: Whether to load the editor JavaScript in the editor template.
    | include_css: Whether to load the editor CSS in the editor template.
    | preview_css: Public path to CSS to override in the preview.
    | brand: Custom branding for the editor.
    |
    */

    'include_js' => true,

    'include_css' => true,

    'preview_css' => null,

    'brand' => [
        'logo' => '<svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">   <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" /> </svg> ',
        'colors' => [
            'topbar_bg' => 'bg-white text-gray-800',
        ],
    ],

    /*
    |--------------------------------------------------------------------------------
    | Blocks
    |--------------------------------------------------------------------------------
    |
    | Here you may register the blocks that should be available in the editor by
    | default. However, you choose to override available blocks by declaring
    | them in the in the livewire dropblockeditor component.
    |
    */

    'blocks' => [
        Jeffreyvr\DropBlockEditor\Blocks\Example::class,
    ],

    /*
    |--------------------------------------------------------------------------------
    | Buttons
    |--------------------------------------------------------------------------------
    |
    | Here you may register the buttons that should be available in the editor by
    | default. However, you choose to override available buttons by declaring
    | them in the in the livewire dropblockeditor component.
    |
    */

    'buttons' => [
        'dropblockeditor-example-button',
    ],

    /*
    |-----------------------------------------------------------------------------
    | Register Parsers
    |-----------------------------------------------------------------------------
    |
    | Here you may register the parsers that the base template and blocks
    | should go through before they are ready as output.
    |
    */

    'parsers' => [
        // Jeffreyvr\DropBlockEditor\Parsers\Mjml::class,
        Jeffreyvr\DropBlockEditor\Parsers\Html::class,
        Jeffreyvr\DropBlockEditor\Parsers\Editor::class,
    ],

    /*
    |-----------------------------------------------------------------------------
    | MJML
    |-----------------------------------------------------------------------------
    |
    | Here you can define the settings for the MJML parsing. Choosing the API
    | requires username and password, while choosing Node, requires you
    | to install the MJML package.
    |
    */

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
