<?php

use Jeffreyvr\DropBlockEditor\Components\DropBlockEditor;
use Livewire\Livewire;

it('can render the editor', function () {
    Livewire::test(DropBlockEditor::class, [
        'title' => 'The name of the campaign',
    ])
        ->assertSet('title', 'The name of the campaign');
});

it('displays the example button', function () {
    Livewire::test(DropBlockEditor::class, [
        'title' => 'The name of the campaign',
    ])
        ->assertSee('Save');
});

it('displays the example block', function () {
    Livewire::test(DropBlockEditor::class, [
        'title' => 'The name of the campaign',
    ])
        ->assertSee('Example');
});

it('displays the active block', function () {
    Livewire::test(DropBlockEditor::class, [
        'title' => 'The name of the campaign',
    ])
        ->call('insertBlock', 0)
        ->assertSee('Drop it like it\'s hot');
});

it('can undo and redo a change', function () {
    Livewire::test(DropBlockEditor::class, [
        'title' => 'The name of the campaign',
    ])
        ->call('insertBlock', 0)
        ->call('recordInHistory')
        ->assertSee('Drop it like it\'s hot')
        ->call('undo')
        ->assertDontSee('Drop it like it\'s hot')
        ->call('redo')
        ->assertSee('Drop it like it\'s hot');
});

it('can delete a block', function () {
    Livewire::test(DropBlockEditor::class, [
        'title' => 'The name of the campaign',
    ])
        ->call('insertBlock', 0)
        ->assertSee('Drop it like it\'s hot')
        ->call('deleteBlock', 0)
        ->assertDontSee('Drop it like it\'s hot');
});

it('sets initial active blocks when set', function () {
    Livewire::test(DropBlockEditor::class, [
        'title' => 'The name of the campaign',
        'activeBlocks' => json_decode('[{"data":{"title":"Picking my way out of here", "content": "One Song At A Time"},"class":"Jeffreyvr\\\\DropBlockEditor\\\\Blocks\\\\Example"}]', true),
    ])
        ->assertSee('One Song At A Time');
});
