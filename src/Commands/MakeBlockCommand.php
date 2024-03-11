<?php

namespace Jeffreyvr\DropBlockEditor\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Pluralizer;
use Illuminate\Support\Str;

class MakeBlockCommand extends Command
{
    public $signature = 'dropblockeditor:make {name} {--without-edit-component}';

    public $description = 'Create a new editor block';

    protected function makeDirectory($path)
    {
        if (! File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        return $path;
    }

    public function getSingularClassName($name): string
    {
        return ucwords(Pluralizer::singular($name));
    }

    public function getStubContents($stub, $stubVariables = []): array|bool|string
    {
        $contents = file_get_contents($stub);

        foreach ($stubVariables as $search => $replace) {
            $contents = str_replace("[$search]", $replace, $contents);
        }

        return $contents;
    }

    public function getBlockSourceFilePath(): string
    {
        return base_path('app/DropBlockEditor/Blocks').'/'.$this->getSingularClassName($this->argument('name')).'.php';
    }

    public function getEditComponentSourceFilePath(): string
    {
        $path = str_replace(['App', '\\'], ['app', '/'], config('livewire.class_namespace'));

        return base_path($path).'/'.$this->getSingularClassName($this->argument('name')).'.php';
    }

    public function isFirstTimeMakingABlock(): bool
    {
        return ! File::isDirectory(base_path('app/DropBlockEditor/Blocks'));
    }

    public function handle(): int
    {
        $isFirstBlock = $this->isFirstTimeMakingABlock();
        $blockPath = $this->getBlockSourceFilePath();
        $withoutEditComponent = $this->option('without-edit-component');

        if (File::exists($blockPath)) {
            $this->line("<options=bold,reverse;fg=red> Uh-oh! </> 🫢 \n");
            $this->line("<fg=red;options=bold>A block with this name already exists at:</> {$blockPath}");

            return self::FAILURE;
        }

        $this->makeDirectory(dirname($blockPath));

        File::put($blockPath, $this->getStubContents(__DIR__.'/'.($withoutEditComponent ? 'block.stub' : 'block.edit-component.stub'), [
            'namespace' => 'App\\DropBlockEditor\\Blocks',
            'name' => Str::studly($this->argument('name')),
            'edit-component-name' => Str::kebab($this->argument('name')),
        ]));

        $this->line("<options=bold,reverse;fg=green> Block created </> 🚀\n");
        $this->line("<options=bold;fg=green>BLOCK PATH:</> {$blockPath}");

        if (! $withoutEditComponent) {
            $blockEditComponentPath = $this->getEditComponentSourceFilePath();

            if (File::exists($blockEditComponentPath)) {
                $this->line("<options=bold,reverse;fg=red> Uh-oh! </> 🫢 \n");
                $this->line("<fg=red;options=bold>A Livewire component with this name already exists at:</> {$blockEditComponentPath}");

                return self::FAILURE;
            }

            $this->makeDirectory(dirname($blockEditComponentPath));

            File::put($blockEditComponentPath, $this->getStubContents(__DIR__.'/edit-component.stub', [
                'namespace' => config('livewire.class_namespace'),
                'name' => Str::studly($this->argument('name')),
            ]));

            $this->line("<options=bold;fg=green>EDIT COMPONENT PATH:</> {$blockEditComponentPath}");
        }

        if ($isFirstBlock) {
            $this->line("\n<options=bold>Awesome, your first DropBlockEditor block has been created!</> 🎉\n");
        }

        return self::SUCCESS;
    }
}
