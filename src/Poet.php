<?php

namespace Log1x\Poet;

use WP_Post_Type;
use WP_Taxonomy;

class Poet
{
    /**
     * Returns the configured post types for the application.
     *
     * @var array
     */
    protected $post;

    /**
     * Returns the configured taxonomies for the application.
     *
     * @var array
     */
    protected $taxonomy;

    /**
     * Create a new Poet instance.
     *
     * @param  array $post
     * @param  array $taxonomy
     * @return void
     */
    public function __construct($post = [], $taxonomy = [])
    {
        $this->post = $post;
        $this->taxonomy = $taxonomy;

        add_action('init', function () {
            $this->register();
        });
    }

    /**
     * Register post types and taxonomies.
     *
     * @return void
     */
    protected function register()
    {
        // phpcs:disable
        collect($this->post)
            ->each(function ($config = [], $key) {
                if ($this->exists($key)) {
                    return $this->modify($key, $config);
                }

                register_extended_post_type($key, $config, $config['labels'] ?? []);
            });

        collect($this->taxonomy)
            ->each(function ($config = [], $key) {
                if ($this->exists($key)) {
                    return $this->modify($key, $config);
                }

                register_extended_taxonomy($key, $config['links'] ?? 'post', $config, $config['labels'] ?? []);
            });
        // phpcs:enable
    }

    /**
     * Checks if a post type or taxonomy already exists.
     *
     * @return bool
     */
    protected function exists($name)
    {
        return post_type_exists($name) || taxonomy_exists($name);
    }

    /**
     * Modifies an existing post type or taxonomy object.
     *
     * @param string $name
     * @param array  $config
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
}
