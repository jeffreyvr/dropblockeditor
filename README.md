# A simple block editor made with Livewire.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jeffreyvanrossum/dropblockeditor.svg?style=flat-square)](https://packagist.org/packages/jeffreyvanrossum/dropblockeditor)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/jeffreyvanrossum/dropblockeditor/run-tests?label=tests)](https://github.com/jeffreyvanrossum/dropblockeditor/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/jeffreyvanrossum/dropblockeditor/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/jeffreyvanrossum/dropblockeditor/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/jeffreyvanrossum/dropblockeditor.svg?style=flat-square)](https://packagist.org/packages/jeffreyvanrossum/dropblockeditor)

A nice block based editor made with [Laravel Livewire](http://laravel-livewire.com).

## Installation

You can install the package via composer:

```bash
composer require jeffreyvanrossum/dropblockeditor
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="dropblockeditor-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="dropblockeditor-views"
```

## Usage

In the below examples, you'll find that Tailwind CSS is used for basic styling.

## Basic setup

First, make sure Livewire is installed in your Laravel app.

Create a blade template that renders the block editor component, like so:

```blade
@livewire('dropblockeditor', [
    'title' => 'Your example campaign'
])
```

The editor also uses [Alpine](http://alpinejs.dev), you need to add this on the page where you are rendering the editor.

```html
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
```

Your editor should already be working now.

## Creating custom block

A block has a visual side which is the `Block` class and an optional modified Livewire component (`BlockEditComponent`) to make it editable.

Run the following artisan command to create a new Block with an edit component:

```bash
php artisan dropblockeditor:make Text --with-edit-component
```

Note that the values of the block are accessable in your edit component through the `data` attribute.

##  Buttons

Buttons are regular Livewire components which are rendered in the upper right corner of the editor.

To keep the button informed of editor changes, you can use the `editorIsUpdated` listener.

In the below example, we create a Save button.

```php
class ExampleButton extends Component
{
    public $editor;

    protected $listeners = [
        'editorIsUpdated' => 'editorIsUpdated'
    ];

    public function editorIsUpdated($newEditor)
    {
        $this->editor = $newEditor;
    }

    public function save()
    {
        // do something on save
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
```

## Parsers

Your components can be parsed as standard HTML or as [MJML](https://mjml.io).

MJML is a framework that looks somewhat like HTML, but is created to make creating responsive email templates much easier.

To enable MJML parsing, make sure of the following:

- Make sure Node and MJML (`npm install mjml`) are installed and the paths to `node_binary` and `mjml_binary` are configured correctly in the config file
- Make sure the `Jeffreyvr\DropBlockEditor\Parsers\Mjml::class` is added as a parser in your config file

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Jeffrey van Rossum](https://github.com/jeffreyvr)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
