<?php

namespace Log1x\Poet;

use WP_Post_Type;
use WP_Taxonomy;
use TOC\MarkupFixer;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

use function Roots\asset;

class Poet
{
    /**
     * The Poet configuration.
     *
     * @var array
     */
    protected $config;

    /**
     * The Poet modules.
     *
     * @var array
     */
    protected $modules = [
        \Log1x\Poet\Modules\AdminMenuModule::class,
        \Log1x\Poet\Modules\AnchorModule::class,
        \Log1x\Poet\Modules\BlockCategoryModule::class,
        \Log1x\Poet\Modules\BlockModule::class,
        \Log1x\Poet\Modules\EditorPaletteModule::class,
        \Log1x\Poet\Modules\PostTypeModule::class,
        \Log1x\Poet\Modules\TaxonomyModule::class,
    ];

    /**
     * Create a new Poet instance.
     *
     * @param  array $config
     * @return void
     */
    public function __construct($config = [])
    {
        add_filter('init', function () {
            //
        }, 20);
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
