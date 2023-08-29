# Changelog

All notable changes to `dropblockeditor` will be documented in this file.

## Unreleased

- upgrade to Livewire v3
- `php artisan dropblockeditor:make` puts Livewire component in `App\Livewire` namespace unless otherwise configured
- `php artisan dropblockeditor:make` now assumes you want an edit component by default

## 0.1.3 - 2022-06-23

- added `preview_css` config option (thanks @TechTomaz)
- fix path make block command (lowercase `app`) (thanks @Carnicero90)

## 0.1.2 - 2022-05-12

- allow html parser base template to be a string

## 0.1.1 - 2022-05-11

- fix not passing update properties to buttons on initial load

## 0.1.0 - 2022-05-10

- first pre release
