<?php

namespace Jeffreyvr\DropBlockEditor\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Pluralizer;
use Illuminate\Support\Str;

class MakeBlockCommand extends Command
{
    public $signature = 'dropblockeditor:make {name} {--with-edit-component}';

    public $description = 'Create a new editor block';

    protected function makeDirectory($path)
    {
        if (! File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        return $path;
    }

    public function getSingularClassName($name)
    {
        return ucwords(Pluralizer::singular($name));
    }

    public function getStubContents($stub, $stubVariables = [])
    {
        $contents = file_get_contents($stub);

        foreach ($stubVariables as $search => $replace) {
            $contents = str_replace("[$search]", $replace, $contents);
        }

        return $contents;
    }

    public function getBlockSourceFilePath()
    {
        return base_path('app/DropBlockEditor/Blocks').'/'.$this->getSingularClassName($this->argument('name')).'.php';
    }

    public function getEditComponentSourceFilePath()
    {
        $path = str_replace(['App', '\\'], ['app', '/'], config('livewire.class_namespace'));

        return base_path($path).'/'.$this->getSingularClassName($this->argument('name')).'.php';
    }

    public function handle(): int
    {
        $blockPath = $this->getBlockSourceFilePath();
        $withEditComponent = $this->option('with-edit-component', false);

        if (File::exists($blockPath)) {
            $this->error("Block already exists at: {$blockPath}");

            return self::FAILURE;
        }

        $this->makeDirectory(dirname($blockPath));

        File::put($blockPath, $this->getStubContents(__DIR__.'/'.($withEditComponent ? 'block.edit-component.stub' : 'block.stub'), [
            'namespace' => 'App\\DropBlockEditor\\Blocks',
            'name' => Str::studly($this->argument('name')),
            'edit-component-name' => Str::kebab($this->argument('name')),
        ]));

        $this->info("Block created at: {$blockPath}");

        if ($withEditComponent) {
            $blockEditComponentPath = $this->getEditComponentSourceFilePath();

            if (File::exists($blockEditComponentPath)) {
                $this->error("Edit component already exists at: {$blockEditComponentPath}");

                return self::FAILURE;
            }

            $this->makeDirectory(dirname($blockEditComponentPath));

            File::put($blockEditComponentPath, $this->getStubContents(__DIR__.'/edit-component.stub', [
                'namespace' => config('livewire.class_namespace'),
                'name' => Str::studly($this->argument('name')),
            ]));

            $this->info("Edit component created at: {$blockEditComponentPath}");
        }

        return self::SUCCESS;
    }
}
