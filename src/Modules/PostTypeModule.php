<?php

namespace Log1x\Poet\Modules;

use Illuminate\Support\Arr;

class PostTypeModule extends Module
{
    /**
     * The module key.
     *
     * @param string[]
     */
    protected $key = ['post', 'post_type'];

    /**
     * Register the configured post types using Extended CPTs.
     *   ↪ https://github.com/johnbillion/extended-cpts
     *
     * If a post type already exists, the object will be modified instead.
     *   ↪ https://codex.wordpress.org/Function_Reference/get_post_type_object
     *
     * If a post type already exists and is set to `false`, the post type
     * will be unregistered.
     *   ↪ https://developer.wordpress.org/reference/functions/unregister_post_type/
     *
     * @return void
     */
    protected function register()
    {
        $this->config
            ->each(function ($value, $key) {
                if (empty($key) || is_int($key)) {
                    return register_extended_post_type(...Arr::wrap($value));
                }

                if (post_type_exists($key)) {
                    if ($value === false) {
                        return $this->unregisterPostType($key);
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
     * Modifies an existing post type or taxonomy object.
     *
     * @param  string $name
     * @param  array  $config
     * @return void
     */
    protected function modifyPostType($name, $config)
    {
        $object = get_post_type_object($name);

        if (! $object instanceof WP_Post_Type) {
            return;
        }

        foreach ($config as $key => $value) {
            $object->{$key} = $value;
        }
    }

    /**
     * Unregister an existing post type.
     *
     * @param  string $type
     * @return void
     */
    protected function unregisterPostType($type)
    {
        $object = get_post_type_object($type);

        if (! $object instanceof WP_Post_Type) {
            return;
        }

        return unregister_post_type($object);
    }
}
