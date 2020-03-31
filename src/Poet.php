<?php

namespace Log1x\Poet;

use WP_Post_Type;
use WP_Taxonomy;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class Poet
{
    /**
     * The Poet configuration.
     *
     * @var array
     */
    protected $config;

    /**
     * Create a new Poet instance.
     *
     * @param  array $config
     * @return void
     */
    public function __construct($config = [])
    {
        $this->config = collect($config);

        add_filter('init', function () {
            $this->registerPosts();
            $this->registerTaxonomies();
            $this->registerBlocks();
        }, 20);
    }

    /**
     * Register the configured post types using Extended CPTs.
     *   ↪ https://github.com/johnbillion/extended-cpts
     *
     * If a post type already exists, the object will be modified instead.
     *   ↪ https://codex.wordpress.org/Function_Reference/get_post_type_object
     *
     * @return void
     */
    protected function registerPosts()
    {
        $this->config->only('post')->each(function ($post) {
            foreach ($post as $key => $value) {
                if (empty($key)) {
                    return register_extended_post_type(...Arr::wrap($value));
                }

                if ($this->exists($key)) {
                    return $this->modify($key, $value);
                }

                return register_extended_post_type(
                    $key,
                    Arr::get($value, 'links', 'post'),
                    Arr::get($value, 'labels', [])
                );
            }
        });
    }

    /**
     * Register the configured taxomonies using Extended CPTs.
     *   ↪ https://github.com/johnbillion/extended-cpts
     *
     * If a taxonomy already exists, the object will be modified instead.
     *   ↪ https://developer.wordpress.org/reference/functions/get_taxonomy/
     *
     * @return void
     */
    protected function registerTaxonomies()
    {
        $this->config->only('taxonomy')->each(function ($taxonomy) {
            foreach ($taxonomy as $key => $value) {
                if (empty($key)) {
                    return register_extended_taxonomy($value, 'post');
                }

                if ($this->exists($key)) {
                    return $this->modify($key, $value);
                }

                return register_extended_taxonomy(
                    $key,
                    Arr::get($value, 'links', 'post'),
                    $value,
                    Arr::get($value, 'labels', [])
                );
            }
        });
    }

    /**
     * Register the configured block types with the editor using Blade
     * for rendering the registered block.
     *
     * If no namespace is provided on the block, Poet will default to
     * the current theme text domain.
     *
     * Optionally, you may pass a block as an array containing:
     *   ↪ attributes – An array of custom block attributes.
     *   ↪ strip – When set to false, `$content` will always return true.
     *
     * Given the Block `sage/accordion`, the Block view would be located at:
     *   ↪ `views/blocks/accordion.blade.php`
     *
     * Block views have the following variables available:
     *   ↪ $data    – An object containing the block data.
     *   ↪ $content – A string containing the InnerBlocks content.
     *                Returns `false` when empty.
     *
     * @return void
     */
    protected function registerBlocks()
    {
        return $this->config->only('block')->each(function ($block) {
            foreach ($block as $key => $value) {
                if (empty($key)) {
                    $key = $value;
                }

                $value = collect($value);

                if (! Str::contains($key, '/')) {
                    $key = Str::start($key, $this->namespace());
                }

                 return register_block_type($key, [
                    'attributes' => $value->get('attributes', []),
                    'render_callback' => function ($data, $content) use ($key, $value) {
                        return view($value->get('view', 'blocks.' . Str::after($key, '/')), [
                            'data' => (object) $data,
                            'content' => $value->get('strip', true) && $this->isEmpty($content) ? false : $content
                        ]);
                    },
                ]);
            }
        });
    }

    /**
     * Checks if a post type or taxonomy already exists.
     *
     * @param  string $name
     * @return bool
     */
    protected function exists($name)
    {
        return post_type_exists($name) || taxonomy_exists($name);
    }

    /**
     * Modifies an existing post type or taxonomy object.
     *
     * @param  string $name
     * @param  array  $config
     * @return void
     */
    protected function modify($name, $config)
    {
        $object = get_post_type_object($name) ?: get_taxonomy($name);

        if (! $this->verify($object)) {
            return;
        }

        return collect($config)->map(function ($value, $key) use ($object) {
            $object->{$key} = $value;
        });
    }

    /**
     * Checks if the passed object is a valid WP_Post_Type or WP_Taxonomy instance.
     *
     * @param  object $object
     * @return bool
     */
    protected function verify($object)
    {
        return $object instanceof WP_Post_Type || $object instanceof WP_Taxonomy;
    }

    /**
     * Use the current theme's text domain as a namespace.
     *
     * @param  string $delimiter
     * @return string
     */
    protected function namespace($delimiter = '/')
    {
        return (Str::slug(
            wp_get_theme()->get('TextDomain')
        ) ?? 'sage') . $delimiter;
    }

    /**
     * Check if a string is empty after stripping tags and whitespace.
     *
     * @param  string $value
     * @return bool
     */
    protected function isEmpty($value)
    {
        return empty(
            wp_strip_all_tags($value, true)
        );
    }
}
