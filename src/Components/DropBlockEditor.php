<?php

namespace Jeffreyvr\DropBlockEditor\Components;

use Livewire\Component;
use Illuminate\Support\Str;
use Jeffreyvr\DropBlockEditor\Blocks\Block;

class DropBlockEditor extends Component
{
    public $initialRender = true;

    public $title;

    public $base = 'dropblockeditor::base';

    public $hash;

    public $result;

    public $activeBlockIndex = false;

    public $activeBlocks = [];

    public $history = [];

    public int $historyIndex = -1;

    public $buttons = [];

    public $blocks = [];

    protected $listeners = [
        'blockEditComponentSelected' => 'blockSelected',
        'blockEditComponentUpdated' => 'blockUpdated',
        'refreshComponent' => '$refresh',
    ];

    public function canUndo()
    {
        return $this->historyIndex > 0;
    }

    public function canRedo()
    {
        return $this->historyIndex < count($this->history) - 1;
    }

    public function undo()
    {
        if (! $this->canUndo()) {
            return;
        }

        $this->historyIndex--;

        $this->activeBlocks = $this->history[$this->historyIndex]['activeBlocks'];
        $this->activeBlockIndex = $this->history[$this->historyIndex]['activeBlockIndex'];
        $this->hash = Str::random(10);
    }

    public function redo()
    {
        if (! $this->canRedo()) {
            return;
        }

        $this->historyIndex++;

        $this->activeBlocks = $this->history[$this->historyIndex]['activeBlocks'];
        $this->activeBlockIndex = $this->history[$this->historyIndex]['activeBlockIndex'];
        $this->hash = Str::random(10);
    }

    public function recordInHistory()
    {
        $history = collect($this->history)
            ->slice(0, $this->historyIndex + 1)
            ->push([
                'activeBlocks' => $this->activeBlocks,
                'activeBlockIndex' => $this->activeBlockIndex,
            ])
            ->take(-5)
            ->values();

        $this->history = $history->toArray();

        $this->historyIndex = count($this->history) - 1;
    }

    public function blockUpdated($position, $data)
    {
        $this->activeBlocks[$position]['data'] = $data;

        $this->recordInHistory();
    }

    public function parse($context)
    {
        $parsers = config('dropblockeditor.parsers', []);

        $output = '';

        foreach ($parsers as $parser) {
            $output = (new $parser($output, $this->activeBlocks))
                ->base($this->base)
                ->context($context)
                ->parse()
                ->output();
        }

        return $output;
    }

    public function process()
    {
        $this->result = [
            'editor' => $this->parse('editor'),
            'rendered' => $this->parse('rendered'),
        ];
    }

    public function blockSelected($blockId)
    {
        $this->activeBlockIndex = $blockId;

        $this->recordInHistory();
    }

    public function cloneBlock()
    {
        $clone = $this->activeBlocks[$this->activeBlockIndex];

        $this->activeBlocks[] = $clone;

        $this->activeBlockIndex = array_key_last($this->activeBlocks);

        $this->recordInHistory();
    }

    public function deleteBlock()
    {
        $activeBlockId = $this->activeBlockIndex;

        $this->activeBlockIndex = false;

        unset($this->activeBlocks[$activeBlockId]);

        $this->recordInHistory();
    }

    public function getBlockFromClassName($name): Block
    {
        return Block::fromName($name);
    }

    public function getActiveBlock(): bool|Block
    {
        if (isset($this->activeBlockIndex) && $this->activeBlockIndex === false) {
            return false;
        }

        return Block::fromName($this->activeBlocks[$this->activeBlockIndex]['class']);
    }

    public function mount()
    {
        foreach (config('dropblockeditor.blocks', []) as $block) {
            $this->blocks[] = (new $block)->toArray();
        }

        $this->buttons = config('dropblockeditor.buttons', []);

        $this->hash = Str::random(10);

        $this->recordInHistory();
    }

    public function reorder($ids)
    {
        return collect($ids)
            ->map(function ($id) {
                return $this->activeBlocks[$id];
            })
            ->all();
    }

    public function insertBlock($id, $index = null, $placement = null)
    {
        if ($index === null) {
            $block = $this->blocks[$id];

            $this->activeBlocks[] = $block;

            return;
        }

        if ($placement === 'before') {
            $newIndex = $index - 1 == -1 ? 0 : $index - 1;
        } else {
            $newIndex = $index + 1;
        }

        $this->activeBlocks = array_merge(array_slice($this->activeBlocks, 0, $newIndex), [$this->blocks[$id]], array_slice($this->activeBlocks, $newIndex));

        $this->recordInHistory();
    }

    public function prepareActiveBlockKey($activeBlockIndex)
    {
        return "{$activeBlockIndex}-{$this->hash}";
    }

    public function render()
    {
        $this->process();

        if (! $this->initialRender) {
            $this->emit('editorIsUpdated', [
                'result' => $this->result,
                'activeBlocks' => $this->activeBlocks,
            ]);
        }

        $this->initialRender = false;

        return view('dropblockeditor::editor', [
            'activeBlock' => $this->getActiveBlock()
        ]);
    }
}
