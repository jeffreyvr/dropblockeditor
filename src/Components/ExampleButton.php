<?php

namespace Jeffreyvr\DropBlockEditor\Components;

use Livewire\Component;

class ExampleButton extends Component
{
    public $properties;

    protected $listeners = [
        'editorIsUpdated' => 'editorIsUpdated',
    ];

    public function editorIsUpdated($properties)
    {
        $this->properties = $properties;
    }

    public function save()
    {
        // Example of getting a json string of the active blocks.
        // $activeBlocks = collect($properties['activeBlocks'])
        //     ->toJson();
    }

    public function render()
    {
        return <<<'blade'
            <div>
                <button wire:click="save" class="bg-blue-200 text-blue-900 rounded px-3 py-1 text-sm">Save</button>
            </div>
        blade;
    }
}
