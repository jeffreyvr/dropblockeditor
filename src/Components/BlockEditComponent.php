<?php

namespace Jeffreyvr\DropBlockEditor\Components;

use Livewire\Component;

class BlockEditComponent extends Component
{
    public $position;

    public array $block;

    public $data = [];

    public function mount()
    {
        foreach ($this->block['data'] as $key => $value) {
            $this->data[$key] = $value;
        }
    }

    public function updated()
    {
        $this->dispatch('blockEditComponentUpdated', $this->position, $this->data);
    }
}
