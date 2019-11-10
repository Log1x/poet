# Poet

![Latest Stable Version](https://img.shields.io/packagist/v/log1x/poet?style=flat-square)
![Build Status](https://img.shields.io/circleci/build/github/Log1x/poet?style=flat-square)
![Total Downloads](https://img.shields.io/packagist/dt/log1x/poet?style=flat-square)

Poet provides simple configuration-based post type and taxonomy registration for Sage 10 utilizing [Extended CPTs](https://github.com/johnbillion/extended-cpts).

Poet also has the ability to modify an existing post type or taxonomy if it finds that it already exists.

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

For additional configuration values, see the [Extended CPTs](https://github.com/johnbillion/extended-cpts/wiki) and [`register_post_type()`](https://developer.wordpress.org/reference/functions/register_post_type/) documentation.

**Note**: Do not nest configuration in a `'config'` key like shown on Extended CPTs.

### Example for enabling the built-in `WP_Block` post type.

```php
'post' => [
    'wp_block' => [
        'show_in_menu' => true,
        'menu_icon'    => 'dashicons-layout',
        '_builtin'     => false,
    ],
],
```

## Bug Reports

If you discover a bug in Poet, please [open an issue](https://github.com/log1x/poet/issues).

## Contributing

Contributing whether it be through PRs, reporting an issue, or suggesting an idea is encouraged and appreciated.

## License

Poet is provided under the [MIT License](https://github.com/log1x/poet/blob/master/LICENSE.md).
