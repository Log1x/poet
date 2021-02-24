<?php

namespace Log1x\Poet;

use Illuminate\Support\Collection;
use Log1x\Poet\Concerns\HasCollection;
use Log1x\Poet\Modules\AbstractModule;
use Log1x\Poet\Modules\AdminMenuModule;
use Log1x\Poet\Modules\BlockCategoryModule;
use Log1x\Poet\Modules\BlockModule;
use Log1x\Poet\Modules\EditorPaletteModule;
use Log1x\Poet\Modules\PostAnchorModule;
use Log1x\Poet\Modules\PostTypeModule;
use Log1x\Poet\Modules\TaxonomyModule;
use Roots\Acorn\Application;

use function Roots\asset;

class Poet
{
    use HasCollection;

    /**
     * The Application instance.
     *
     * @var \Roots\Acorn\Application
     */
    protected $app;

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
        AdminMenuModule::class,
        BlockCategoryModule::class,
        BlockModule::class,
        EditorPaletteModule::class,
        PostAnchorModule::class,
        PostTypeModule::class,
        TaxonomyModule::class,
    ];

    /**
     * Create a new Poet instance.
     *
     * @param  array $config
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->config = $this->collect($this->app->config->get('poet'))
            ->map(function ($value) {
                return is_array($value) ? $this->collect($value) : $value;
            });

        add_filter('init', function () {
            $this->register([
                AdminMenuModule::class,
                BlockCategoryModule::class,
                BlockModule::class,
                EditorPaletteModule::class,
                PostTypeModule::class,
                TaxonomyModule::class,
            ]);

            if (class_exists('\TOC\MarkupFixer')) {
                $this->register(PostAnchorModule::class);
            }
        }, 20);
    }

    /**
     * Register a Poet module.
     *
     * @param  string[] $modules
     * @return void
     */
    public function register($modules = [])
    {
        if (! is_array($modules)) {
            $modules = [$modules];
        }

        foreach ($modules as $module) {
            if ($module instanceof AbstractModule) {
                continue;
            }

            new $module($this->app, $this->config);
        }
    }
}
