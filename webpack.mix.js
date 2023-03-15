const mix = require('laravel-mix');

mix.js('resources/js/editor.js', 'public/')
    .postCss("resources/css/editor.css", "public/", [
        require("tailwindcss"),
    ]);
