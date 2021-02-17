<?php

namespace Log1x\Poet\Modules;

use WP_Post_Type;
use Illuminate\Support\Arr;

class PostTypeModule extends AbstractModule
{
    /**
     * The module key.
     *
     * @var string
     */
    protected $key = 'post';

    /**
     * Handle the module.
     *
     * @return void
     */
    public function handle()
    {
        $this->config->each(function ($value, $key) {
            if (empty($key) || is_int($key)) {
                return register_extended_post_type(...Arr::wrap($value));
            }

            if ($this->hasPostType($key)) {
                if ($value === false) {
                    return $this->unregisterPostType($key);
                }

                return $this->modifyPostType($key, $value);
            }

            return register_extended_post_type(
                $key,
                $value,
                Arr::get($value, 'labels', [])
            );
        });
    }

    /**
     * Determine if the object is a post type.
     *
     * @param  mixed $object
     * @return bool
     */
    protected function isPostType($object)
    {
        return $object instanceof WP_Post_Type;
    }

    /**
     * Determine if the post type exists.
     *
     * @param  string $name
     * @return bool
     */
    protected function hasPostType($name)
    {
        return post_type_exists($name);
    }

    /**
     * Modifiy an existing post type.
     *
     * @param  string $name
     * @param  array  $config
     * @return void
     */
    protected function modifyPostType($name, $config)
    {
        $object = get_post_type_object($name);

        if (! $this->isPostType($object)) {
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

        if (! $this->isPostType($object)) {
            return;
        }

        return unregister_post_type($object);
    }
}
