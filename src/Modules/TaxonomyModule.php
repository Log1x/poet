<?php

namespace Log1x\Poet\Modules;

use WP_Taxonomy;
use Illuminate\Support\Arr;

class TaxonomyModule extends AbstractModule
{
    /**
     * The module key.
     *
     * @var string
     */
    protected $key = 'taxonomy';

    /**
     * Handle the module.
     *
     * @return void
     */
    public function handle()
    {
        $this->config->each(function ($value, $key) {
            if (empty($key) || is_int($key)) {
                return register_extended_taxonomy($value, 'post');
            }

            if ($this->hasTaxonomy($key)) {
                if ($value === false) {
                    return $this->unregisterTaxonomy($key);
                }

                return $this->modifyTaxonomy($key, $value);
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
     * Determine if the object is a taxonomy.
     *
     * @param  mixed $object
     * @return bool
     */
    protected function isTaxonomy($object)
    {
        return $object instanceof WP_Taxonomy;
    }

    /**
     * Determine if the taxonomy exists.
     *
     * @param  string $name
     * @return bool
     */
    protected function hasTaxonomy($name)
    {
        return taxonomy_exists($name);
    }

    /**
     * Modify an existing taxonomy.
     *
     * @param  string $name
     * @param  array  $config
     * @return void
     */
    protected function modifyTaxonomy($name, $config)
    {
        $object = get_taxonomy($name);

        if (! $this->isTaxonomy($object)) {
            return;
        }

        foreach ($config as $key => $value) {
            $object->{$key} = $value;
        }
    }

    /**
     * Unregister an existing taxonomy.
     *
     * @link https://developer.wordpress.org/reference/functions/unregister_taxonomy_for_object_type/
     * @link https://developer.wordpress.org/reference/functions/unregister_taxonomy/
     *
     * @param  string $type
     * @return void
     */
    protected function unregisterTaxonomy($type)
    {
        $object = get_taxonomy($type);

        if (! $this->isTaxonomy($object)) {
            return;
        }

        foreach ($object->object_type as $key) {
            unregister_taxonomy_for_object_type($object->name, $key) ?? unregister_taxonomy($object);
        }
    }
}
