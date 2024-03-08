<?php

namespace Jeffreyvr\DropBlockEditor;

use Illuminate\Support\Facades\View;
use Jeffreyvr\DropBlockEditor\Commands\MakeBlockCommand;
use Jeffreyvr\DropBlockEditor\Components\DropBlockEditor;
use Jeffreyvr\DropBlockEditor\Components\Example;
use Jeffreyvr\DropBlockEditor\Components\ExampleButton;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class DropBlockEditorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('dropblockeditor')
            ->hasConfigFile()
            ->hasViews()
            ->hasCommand(MakeBlockCommand::class);
    }

    public function bootingPackage(): void
    {
        Livewire::component('dropblockeditor', DropBlockEditor::class);
        Livewire::component('dropblockeditor-example', Example::class);
        Livewire::component('dropblockeditor-example-button', ExampleButton::class);

        View::composer('dropblockeditor::editor', function ($view) {
            if (config('dropblockeditor.include_js', true)) {
                $view->jsPath = __DIR__.'/../public/editor.js';
            }

            if (config('dropblockeditor.include_css', true)) {
                $view->cssPath = __DIR__.'/../public/editor.css';
            }
        });
    }
}
