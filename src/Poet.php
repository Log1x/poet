<?php

namespace Log1x\Poet;

use Illuminate\Support\Collection;
use Log1x\Poet\Concerns\HasCollection;
use Log1x\Poet\Modules\AbstractModule;
use Log1x\Poet\Modules\AdminMenuModule;
use Log1x\Poet\Modules\AnchorModule;
use Log1x\Poet\Modules\BlockCategoryModule;
use Log1x\Poet\Modules\BlockModule;
use Log1x\Poet\Modules\EditorPaletteModule;
use Log1x\Poet\Modules\PostTypeModule;
use Log1x\Poet\Modules\TaxonomyModule;

use function Roots\asset;

class Poet
{
    use HasCollection;

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
        // AdminMenuModule::class,
        // AnchorModule::class,
        // BlockCategoryModule::class,
        // BlockModule::class,
        // EditorPaletteModule::class,
        PostTypeModule::class,
        TaxonomyModule::class,
    ];

    /**
     * Create a new Poet instance.
     *
     * @param  array $config
     * @return void
     */
    public function __construct($config = [])
    {
        $this->config = $this->collect($config)->map(function ($value) {
            return is_array($value) ? $this->collect($value) : $value;
        });

        add_filter('init', function () {
            foreach ($this->modules as $module) {
                if ($module instanceof AbstractModule) {
                    continue;
                }

                (new $module($this->config))->handle();
            }
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

        return $this->collect($config)->map(function ($value, $key) use ($object) {
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
            return $this->collect($object->object_type)->each(function ($key) use ($object) {
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
