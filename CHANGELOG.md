# Changelog

All notable changes to `dropblockeditor` will be documented in this file.

## Unreleased

## 0.3.1 - 2024-03-06

- hide blocks sidebar with x-show to prevent dom changes (related to https://github.com/jeffreyvr/dropblockeditor/discussions/30)

## 0.3.0 - 2023-11-17

- added tablet preview (thanks @JesusChrist69)
- categories for blocks (thanks @JesusChrist69)

## 0.2.0 - 2023-08-29

- upgrade to Livewire v3
- requires php 8.1 or higher
- `php artisan dropblockeditor:make` puts Livewire component in `App\Livewire` namespace unless otherwise configured
- `php artisan dropblockeditor:make` now assumes you want an edit component by default

## 0.1.3 - 2023-06-23

- added `preview_css` config option (thanks @TechTomaz)
- fix path make block command (lowercase `app`) (thanks @Carnicero90)

## 0.1.2 - 2023-05-12

- allow html parser base template to be a string

## 0.1.1 - 2023-05-11

- fix not passing update properties to buttons on initial load

## 0.1.0 - 2023-05-10

- first pre release
