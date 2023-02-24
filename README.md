# Poet

![Latest Stable Version](https://img.shields.io/packagist/v/log1x/poet?style=flat-square)
![Total Downloads](https://img.shields.io/packagist/dt/log1x/poet?style=flat-square)
![Build Status](https://img.shields.io/github/actions/workflow/status/log1x/poet/Main.yml?branch=master&style=flat-square)

Poet provides simple configuration-based post type, taxonomy, editor color palette, block category, block pattern and block registration/modification.

## Features

- Dead simple post type and taxonomy registration, modification, and unregistering powered by [Extended CPTs](https://github.com/johnbillion/extended-cpts).
- Easy editor color palette configuration including built-in support for [webpack-palette-plugin](https://github.com/roots/palette-webpack-plugin).
- Blocks registered are rendered using Laravel Blade on the frontend.
- Block Patterns registered can have their content defined using Laravel Blade too.
- Add additional block categories with nothing more than a slug.
- Move parent admin menu items to the `Tools` submenu using their page slug.

## Requirements

- [Sage](https://github.com/roots/sage) >= 10.0
- [PHP](https://secure.php.net/manual/en/install.php) >= 7.4
- [Composer](https://getcomposer.org/download/)

## Installation

Install via Composer:

```bash
$ composer require log1x/poet
```

## Getting Started

Start with publishing the Poet configuration file using Acorn:

```bash
$ wp acorn vendor:publish --provider="Log1x\Poet\PoetServiceProvider"
```

## Usage

### Registering a Post Type

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

In it's simplest form, a post type can be created by simply passing a string:

```php
'post' => [
    'book',
],
```

To modify an existing post type, simply treat it as if you are creating a new post type passing only the configuration options you wish to change:

```php
'post' => [
    'post' => [
        'labels' => [
            'singular' => 'Article',
            'plural' => 'Articles',
        ],
    ],
],
```

It is also possible to unregister an existing post type by simply passing `false`:

```php
'post' => [
    'book' => false,
],
```

Please note that some built-in post types (e.g. Post) can not be conventionally unregistered.

For additional configuration options for post types, please see:

- [`register_post_type()`](https://developer.wordpress.org/reference/functions/register_post_type/)
- [`register_extended_post_type()`](https://github.com/johnbillion/extended-cpts/wiki/Registering-Post-Types)

> **Note**: Do not nest configuration in a `config` key like shown in the Extended CPTs documentation.

### Registering a Taxonomy

Registering a taxonomy is similar to a post type. Looking in `config/poet.php`, you will see a Genre taxonomy accompanying the default Book post type:

```php
'taxonomy' => [
    'genre' => [
        'links' => ['book'],
        'meta_box' => 'radio',
    ],
],
```

The most relevent configuration option is `links` which defines the post type the taxonomy is connected to. If no link is specified, it will default to `post`.

To view an archive for the Genre taxonomy, copy the Blade template called `archive.blade.php` to a new file called `taxonomy-genre.blade.php`.

In it's simplest form, you can simply pass a string. The example below would create a Topic taxonomy for the Post post type:

```php
'taxonomy' => [
    'topic',
],
```

As with post types, to modify an existing taxonomy, simply pass only the configuration options you wish to change:

```php
'taxonomy' => [
    'category' => [
        'labels' => [
            'singular' => 'Section',
            'plural' => 'Sections',
        ],
    ],
],
```

Also like post types, you can easily unregister an existing taxonomy by simply passing `false`:

```php
'taxonomy' => [
    'post_tag' => false,
    'category' => false,
],
```

For additional configuration options for taxonomies, please see:

- [`register_taxonomy()`](https://developer.wordpress.org/reference/functions/register_taxonomy/)
- [`register_extended_taxonomy()`](https://github.com/johnbillion/extended-cpts/wiki/Registering-taxonomies)

> **Note**: Do not nest configuration in a `config` key like shown in the Extended CPTs documentation.

### Registering a Block

Poet provides an easy way to register a Gutenberg block with the editor using an accompanying Blade view for rendering the block on the frontend.

Blocks are registered using the `namespace/label` defined when [registering the block with the editor](https://developer.wordpress.org/block-editor/developers/block-api/block-registration/#registerblocktype).

If no namespace is provided, the current theme's [text domain](https://developer.wordpress.org/themes/functionality/internationalization/#loading-text-domain) will be used instead.

Registering a block in most cases is as simple as:

```php
'block' => [
    'sage/accordion',
],
```

#### Creating a Block View

Given the block `sage/accordion`, your accompanying Blade view would be located at `views/blocks/accordion.blade.php`.

Block views have the following variables available:

- `$data` – An object containing the block data.
- `$content` – A string containing the InnerBlocks content. Returns `null` when empty.

By default, when checking if `$content` is empty, it is passed through a method to remove all tags and whitespace before evaluating. This assures that editor bloat like `nbsp;` or empty `<p></p>` tags do not cause `$content` to always return `true` when used in a conditional.

If you do not want this behavior on a particular block, simply register it as an array:

```php
'block' => [
    'sage/accordion' => ['strip' => false],
],
```

If you need to register block attributes using PHP on a particular block, simply pass the attributes in an array when registering:

```php
'block' => [
    'sage/accordion' => [
        'attributes' => [
            'title' => [
                'default' => 'Lorem ipsum',
                'type' => 'string',
            ],
        ],
    ],
],
```

Consider an accordion block that is registered with a `title` and `className` attribute. Your view might look something like this:

```php
<div class="wp-block-accordion {{ $data->className ?? '' }}">
  @isset ($data->title)
    <h2>{!! $data->title !!}</h2>
  @endisset

  <div>
    {!! $content ?? 'Please feed me InnerBlocks.' !!}
  </div>
</div>
```

### Registering a Block Category

Poet provides an easy to way register, modify, and unregister Gutenberg block categories. Looking in the config, you will see a commented out example for a Call to Action category:

```php
'block_category' => [
    'cta' => [
        'title' => 'Call to Action',
        'icon' => 'star-filled',
    ],
],
```

This would result in a block category with a slug of `cta`. Once your block category is registered, you must register a block to its slug before the category will appear in the editor.

In it's simplest form, you can simply pass a string:

```php
'block_category' => [
    'my-cool-blocks',
],
```

which would result in a `my-cool-blocks` category automatically converting the slug to title case.

You can also specify the title by passing a value to your slug:

```php
'block_category' => [
    'my-cool-blocks' => 'Best Blocks, World.',
],
```

Like post types and taxonomies, modifying an existing block category is the same as registering one:

```php
'block_category' => [
    'layouts' => 'Sections',
    'common' => ['icon' => 'star-filled'],
],
```

You can unregister an existing block category by simply passing `false`:

```php
'block_category' => [
    'common' => false,
],
```

### Registering a Block Pattern

Poet can also register Block Patterns for you, with an optional Blade view for the content.

Patterns are registered using the `namespace/label` defined when [registering the pattern with the editor](https://developer.wordpress.org/reference/functions/register_block_pattern/).

If no namespace is provided, the current theme's [text domain](https://developer.wordpress.org/themes/functionality/internationalization/#loading-text-domain) will be used instead.

Registering a block in most cases is as simple as:

```php
'block_pattern' => [
    'sage/hero' => [
        'title' => 'Page Hero',
        'description' => 'Draw attention to the main focus of the page, and highlight key CTAs',
        'categories' => ['all'],
    ],
],
```

You can register the actual content for the pattern here as well, using the `content` key. Or leave it blank to use a corresponding blade view.

```php
'block_pattern' => [
    'sage/fake-paragraph' => [
        'title' => 'Fake Paragraph',
        'description' => 'Filler content used instead of actual content for testing purposes',
        'categories' => ['all'],
        'content' => '<!-- wp:paragraph --><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ratione nulla culpa repudiandae nisi nostrum et, labore earum repellendus porro, mollitia voluptas quam? Modi sint tempore deleniti nesciunt ab, perferendis et.</p><!-- /wp:paragraph -->',
    ],
],
```

#### Creating a Pattern View

Given the block `sage/fake-paragraph`, if no `content` key is defined, then your accompanying Blade view would be located at `views/block-patterns/fake-paragraph.blade.php`.

This Block Pattern view may look like this:

```php
<!-- wp:paragraph -->
<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ratione nulla culpa repudiandae nisi nostrum et, labore earum repellendus porro, mollitia voluptas quam? Modi sint tempore deleniti nesciunt ab, perferendis et.</p>
<!-- /wp:paragraph -->
```

### Registering a Block Pattern Category

Block Pattern Categories can be added with the following code in the poet config:

```php
'block_pattern_category' => [
    'all' => [
        'label' => 'All Patterns',
    ],
],
```

You can specify all category properties such as `label`, as per the [block editor handbook](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-patterns/#register_block_pattern_category).

> Note: Currently, if no Block Pattern Categories are available at all, the Block Patterns tab in the editor will crash when clicked on.

### Registering an Editor Color Palette

Poet attempts to simplify registering a color palette with the editor a bit by not requiring such strict, fragile array markup.

While you can of course pass said array directly, you are also able to register colors by simply passing a slug along with a color and letting Poet handle the rest.

```php
'palette' => [
    'red' => '#ff0000',
    'blue' => '#0000ff',
],
```

Alternatively to passing an array, Poet also accepts a `JSON` file containing your color palette. Poet will generally look for this file in `dist/` by default.

```php
'palette' => 'colors.json',
```

If you are using the [Palette Webpack Plugin](https://github.com/roots/palette-webpack-plugin), you may also simply pass `true` to automatically use the generated `palette.json` during build.

```php
'palette' => true,
```

## Bug Reports

If you discover a bug in Poet, please [open an issue](https://github.com/log1x/poet/issues).

## Contributing

Contributing whether it be through PRs, reporting an issue, or suggesting an idea is encouraged and appreciated.

## License

Poet is provided under the [MIT License](LICENSE.md).
