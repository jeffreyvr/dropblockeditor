<?php

namespace Jeffreyvr\DropBlockEditor\Components;

use Livewire\Component;
use Illuminate\Support\Str;
use Jeffreyvr\DropBlockEditor\Blocks\Block;
use Jeffreyvr\DropBlockEditor\Parsers\Parse;

class DropBlockEditor extends Component
{
    public $initialRender = true;

    public $title;

    public $base = 'dropblockeditor::base';

    public $hash;

    public $parsers = [];

    public $result;

    public $activeBlockIndex = false;

    public $activeBlocks = [];

    public $history = [];

    public int $historyIndex = -1;

    public $buttons = null;

    public $blocks = null;

    protected $listeners = [
        'blockEditComponentSelected' => 'blockSelected',
        'blockEditComponentUpdated' => 'blockUpdated',
        'refreshComponent' => '$refresh',
    ];

    public function canUndo(): bool
    {
        return $this->historyIndex > 0;
    }

    public function canRedo(): bool
    {
        return $this->historyIndex < count($this->history) - 1;
    }

    public function undo(): void
    {
        if (! $this->canUndo()) {
            return;
        }

        $this->historyIndex--;

        $this->activeBlocks = $this->history[$this->historyIndex]['activeBlocks'];
        $this->activeBlockIndex = $this->history[$this->historyIndex]['activeBlockIndex'];
        $this->updateHash();
    }

    public function updateHash(): void
    {
        $this->hash = Str::random(10);
    }

    public function redo(): void
    {
        if (! $this->canRedo()) {
            return;
        }

        $this->historyIndex++;

        $this->activeBlocks = $this->history[$this->historyIndex]['activeBlocks'];
        $this->activeBlockIndex = $this->history[$this->historyIndex]['activeBlockIndex'];
        $this->updateHash();
    }

    public function recordInHistory(): void
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

    public function blockUpdated($position, $data): void
    {
        $this->activeBlocks[$position]['data'] = $data;

        $this->recordInHistory();
    }

    public function process(): void
    {
        $this->result = Parse::execute([
            'activeBlocks' => $this->activeBlocks,
            'base' => $this->base,
            'context' => 'editor',
            'parsers' => $this->parsers,
        ]);
    }

    public function blockSelected($blockId): void
    {
        $this->activeBlockIndex = $blockId;

        $this->recordInHistory();
    }

    public function cloneBlock(): void
    {
        $clone = $this->activeBlocks[$this->activeBlockIndex];

        $this->activeBlocks[] = $clone;

        $this->activeBlockIndex = array_key_last($this->activeBlocks);

        $this->recordInHistory();
    }

    public function deleteBlock(): void
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

        return Block::fromName($this->activeBlocks[$this->activeBlockIndex]['class'])
            ->data($this->activeBlocks[$this->activeBlockIndex]['data']);
    }

    public function mount(): void
    {
        $this->parsers = config('dropblockeditor.parsers', []);

        $this->blocks = collect(! is_null($this->blocks) ? $this->blocks : config('dropblockeditor.blocks', []))
            ->map(fn($block) => (new $block)->toArray())
            ->all();

        $this->buttons = ! is_null($this->buttons) ? $this->buttons : config('dropblockeditor.buttons', []);

        $this->updateHash();

        $this->recordInHistory();
    }

    public function reorder($ids): void
    {
        $this->activeBlocks = collect($ids)
            ->map(function ($id) {
                return $this->activeBlocks[$id];
            })
            ->all();

        $this->dispatch('editorIsUpdated', $this->updateProperties());
    }

    public function insertBlock($id, $index = null, $placement = null): void
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

    public function prepareActiveBlockKey($activeBlockIndex): string
    {
        return "{$activeBlockIndex}-{$this->hash}";
    }

    public function updateProperties(): array
    {
        return [
            'base' => $this->base,
            'parsers' => $this->parsers,
            'activeBlocks' => $this->activeBlocks
        ];
    }

    public function render()
    {
        $this->process();

        if (! $this->initialRender) {
            $this->dispatch('editorIsUpdated', $this->updateProperties());
        }

        $this->initialRender = false;

        return view('dropblockeditor::editor', [
            'activeBlock' => $this->getActiveBlock()
        ]);
    }
}
