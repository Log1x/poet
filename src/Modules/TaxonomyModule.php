<?php

namespace Log1x\Poet\Modules;

use Illuminate\Support\Arr;

class TaxonomyModule extends Module
{
    /**
     * The module key.
     *
     * @param string[]
     */
    protected $key = ['taxonomy'];

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
    protected function register()
    {
        $this->config->each(function ($value, $key) {
            if (empty($key) || is_int($key)) {
                return register_extended_taxonomy($value, 'post');
            }

            if ($this->exists($key)) {
                if ($value === false) {
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
}
