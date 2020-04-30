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
            $this->registerCategories();
        }, 20);
    }

    /**
     * Register the configured post types using Extended CPTs.
     *   ↪ https://github.com/johnbillion/extended-cpts
     *
     * If a post type already exists, the object will be modified instead.
     *   ↪ https://codex.wordpress.org/Function_Reference/get_post_type_object
     *
     * If a post type already exists and is set to `false`, the post type
     * will be unregistered.
     *  ↪ https://developer.wordpress.org/reference/functions/unregister_post_type/
     *
     * @return void
     */
    protected function registerPosts()
    {
        $this->config
            ->only('post')
            ->collapse()
            ->each(function ($value, $key) {
                if (empty($key) || is_int($key)) {
                    return register_extended_post_type(...Arr::wrap($value));
                }

                if ($this->exists($key)) {
                    if (is_bool($value) && $value === false) {
                        return $this->remove($key);
                    }

                    return $this->modify($key, $value);
                }

                return register_extended_post_type(
                    $key,
                    $value,
                    Arr::get($value, 'labels', [])
                );
            });
    }

    /**
     * Register the configured taxomonies using Extended CPTs.
     *   ↪ https://github.com/johnbillion/extended-cpts
     *
     * If a taxonomy already exists, the object will be modified instead.
     *   ↪ https://developer.wordpress.org/reference/functions/get_taxonomy/
     *
     * If a taxonomy already exists and is set to `false`, the taxonomy
     * will be unregistered.
     *   ↪ https://developer.wordpress.org/reference/functions/unregister_taxonomy_for_object_type/
     *     https://developer.wordpress.org/reference/functions/unregister_taxonomy/
     *
     * @return void
     */
    protected function registerTaxonomies()
    {
        $this->config
            ->only('taxonomy')
            ->collapse()
            ->each(function ($value, $key) {
                if (empty($key) || is_int($key)) {
                    return register_extended_taxonomy($value, 'post');
                }

                if ($this->exists($key)) {
                    if (is_bool($value) && $value === false) {
                        return $this->remove($key);
                    }

                    return $this->modify($key, $value);
                }

                return register_extended_taxonomy(
                    $key,
                    Arr::get($value, 'links', 'post'),
                    $value,
                    Arr::get($value, 'labels', [])
                );
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
        return $this->config
            ->only('block')
            ->collapse()
            ->each(function ($value, $key) {
                if (empty($key) || is_int($key)) {
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
            });
    }

    /**
     * Register the configured block categories with the editor.
     *
     * If a category already exists, it will be modified instead.
     *
     * If a category already exists and is set to `false`, the taxonomy
     * will be unregistered.
     *
     * @return void
     */
    protected function registerCategories()
    {
        add_filter('block_categories', function ($categories) {
            $categories = collect($categories)->keyBy('slug');

            return $this->config
                ->only('categories')
                ->collapse()
                ->map(function ($value, $key) use ($categories) {
                    if (empty($key) || is_int($key)) {
                        $key = $value;
                    }

                    if ($categories->has($key)) {
                        if (is_bool($value) && $value === false) {
                            return $categories->forget($key);
                        }

                        if (is_string($value)) {
                            $value = ['title' => Str::title($value)];
                        }

                        return $categories->put(
                            $key,
                            array_merge($categories->get($key), $value)
                        );
                    }

                    if (! is_array($value)) {
                        return [
                            'slug' => Str::slug($key),
                            'title' => Str::title($value ?? $key),
                            'icon' => null,
                        ];
                    }

                    return array_merge([
                        'slug' => Str::slug($key),
                        'title' => Str::title($key),
                        'icon' => null,
                    ], $value ?? []);
                })
                ->merge($categories->all())
                ->filter()
                ->sort()
                ->values()
                ->all();
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
     * Removes an existing post type or taxonomy object.
     *
     * @param  string $name
     * @return void
     */
    protected function remove($name)
    {
        $object = get_post_type_object($name) ?: get_taxonomy($name);

        if (! $this->verify($object)) {
            return;
        }

        if ($this->isTaxonomy($object)) {
            return collect($object->object_type)->each(function ($key) use ($object) {
                return unregister_taxonomy_for_object_type($object->name, $key) ?? unregister_taxonomy($object);
            });
        }

        return unregister_post_type($object);
    }

    /**
     * Checks if the passed object is a valid WP_Post_Type or WP_Taxonomy instance.
     *
     * @param  object $object
     * @return bool
     */
    protected function verify($object)
    {
        return $this->isPostType($object) || $this->isTaxonomy($object);
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
     * Check if an object is an instance of WP_Post_Type.
     *
     * @param  mixed $object
     * @return bool
     */
    protected function isPostType($object)
    {
        return $object instanceof WP_Post_Type;
    }

    /**
     * Check if an object is an instance of WP_Taxonomy.
     *
     * @param  mixed $object
     * @return bool
     */
    protected function isTaxonomy($object)
    {
        return $object instanceof WP_Taxonomy;
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
