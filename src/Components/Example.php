<?php

namespace Jeffreyvr\DropBlockEditor\Components;

class Example extends BlockEditComponent
{
    public function render()
    {
        return <<<'blade'
            <div class="space-y-4">
                <div>
                    <label for="title" class="mb-1">Title</label>
                    <input type="text" id="title" wire:model.live.debounce.500ms="data.title" class="w-full border border-gray-200 px-3 py-1 rounded-md">
                </div>
                <div>
                    <label for="content" class="mb-1">Content</label>
                    <textarea id="content" wire:model.live.debounce.500ms="data.content" class="w-full border border-gray-200 px-3 py-1 rounded-md"></textarea>
                </div>
            </div>
        blade;
    }
}
