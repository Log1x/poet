# Poet

![Latest Stable Version](https://img.shields.io/packagist/v/log1x/poet?style=flat-square)
![Build Status](https://img.shields.io/circleci/build/github/Log1x/poet?style=flat-square)
![Total Downloads](https://img.shields.io/packagist/dt/log1x/poet?style=flat-square)

Poet provides simple configuration-based post type and taxonomy registration as well as the ability to register Gutenberg blocks to be rendered with Laravel Blade.

Post types and taxonomies are registered utilizing [Extended CPTs](https://github.com/johnbillion/extended-cpts).

If the passed post type or taxonomy already exists, Poet will automatically modify their objects instead allowing easy manipulation of built-in post types/taxonomies.

## Requirements

- [Sage](https://github.com/roots/sage) >= 10.0
- [PHP](https://secure.php.net/manual/en/install.php) >= 7.2
- [Composer](https://getcomposer.org/download/)

## Installation

Install via Composer:

```bash
$ composer require log1x/poet
```

## Usage

Publish the example configuration using:

```bash
$ wp acorn vendor:publish --provider="Log1x\Poet\PoetServiceProvider"
```

### Creating a Post Type

All configuration related to Poet is located in `config/poet.php`. Here you will find an example Book post type pre-configured with a few common settings:

```php
'post' => [
    'book' => [
        'enter_title_here' => 'Enter book title',
        'menu_icon' => 'dashicons-book-alt',
        'supports' => ['title', 'editor', 'author', 'revisions', 'thumbnail'],
        'show_in_rest' => true,
        'has_archive' => false,
        'labels' => [
            'singular' => 'Book',
            'plural' => 'Books',
        ],
    ],
],
```

In it's simplest form, a post type can be created by simply passing a string.

```php
'post' => [
    'book',
],
```

To see additional configuration options for post types, take a look at [`register_post_type()`](https://developer.wordpress.org/reference/functions/register_post_type/) and [`register_extended_post_type()`](https://github.com/johnbillion/extended-cpts/wiki/Registering-Post-Types).

> **Note**: Do not nest configuration in a `'config'` key like shown in the Extended CPTs documentation.

## Creating a Taxonomy

Creating a taxonomy is similar to a post type. Looking in `config/poet.php`, you will see a genre taxonomy accompanying the default book post type.

```php
'taxonomy' => [
    'genre' => [
        'links' => ['book'],
        'meta_box' => 'radio',
    ],
],
```

The most relevent configuration value is `links` which defines the post-type the taxonomy is connected to. If no link is specified, it will default to `post`.

In it's simplest form, you can simply pass a string to create a taxonomy for a post.

```php
'taxonomy' => [
    'genre',
],
```

To see additional configuration options for taxonomies, take a look at [`register_taxonomy()`](https://codex.wordpress.org/Function_Reference/register_taxonomy) and [`register_extended_taxonomy()`](https://github.com/johnbillion/extended-cpts/wiki/Registering-taxonomies).

> **Note**: Do not nest configuration in a `'config'` key like shown in the Extended CPTs documentation.

### Modifying an existing Post Type or Taxonomy

Modifying an existing post type or taxonomy is similar to creating a new one. Simply pass the arguments you would otherwise use while registering and Poet will do the rest.

Below is an example for enabling the built-in `wp_block` post type in the menu as well as assigning it a more fitting icon.

```php
'post' => [
    'wp_block' => [
        '_builtin'     => false,
        'show_in_menu' => true,
        'menu_icon'    => 'dashicons-layout',
    ],
],
```

### Registering a Block

Poet provides an easy way to register a Gutenberg block with the editor using an accompanying blade view for rendering the block on the frontend.

Blocks are registered using the `namespace/label` defined when [registering the block with the editor](https://developer.wordpress.org/block-editor/developers/block-api/block-registration/#registerblocktype). If no namespace is provided, the current theme text domain will be used instead.

## Bug Reports

If you discover a bug in Poet, please [open an issue](https://github.com/log1x/poet/issues).

## Contributing

Contributing whether it be through PRs, reporting an issue, or suggesting an idea is encouraged and appreciated.

## License

Poet is provided under the [MIT License](https://github.com/log1x/poet/blob/master/LICENSE.md).
