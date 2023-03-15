<?php

namespace Jeffreyvr\DropBlockEditor\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Jeffreyvr\DropBlockEditor\DropBlockEditor;
use Jeffreyvr\DropBlockEditor\DropBlockEditorServiceProvider;
use Livewire\Livewire;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Jeffreyvr\\DropBlockEditor\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        $this
            ->registerLivewireComponents();
    }

    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            DropBlockEditorServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_dropblockeditor_table.php.stub';
        $migration->up();
        */
    }

    private function registerLivewireComponents(): self
    {
        Livewire::component('dropblockeditor', DropBlockEditor::class);

        return $this;
    }
}
