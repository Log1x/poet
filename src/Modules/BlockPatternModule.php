<?php

namespace Log1x\Poet\Modules;

use Illuminate\Support\Str;
use Log1x\Poet\Concerns\HasNamespace;
use function Roots\view;

class BlockPatternModule extends AbstractModule
{
    use HasNamespace;

    /**
     * The module key.
     *
     * @var string
     */
    protected $key = 'block_pattern';

    /**
     * Handle the module.
     *
     * @return void
     */
    public function handle()
    {
        /** No need to load block patterns when loading the website frontend */
        if (!is_admin() || !class_exists('WP_Block_Patterns_Registry')) {
            return;
        }

        return $this->config->each(function ($patternData, $patternSlug) {
            if (empty($patternSlug) || is_int($patternSlug)) {
                return;
            }

            $patternData = $this->collect($patternData);

            if (!Str::contains($patternSlug, '/')) {
                $patternSlug = Str::start($patternSlug, $this->namespace());
            }

            $viewSlug = 'block-patterns.' . Str::after($patternSlug, '/');

            if (!view()->exists($viewSlug)) {
                return;
            }

            if (!$patternData->has('content')) {
                $patternData['content'] = view($viewSlug)->render();
            }

            return register_block_pattern($patternSlug, $patternData->all());
        });
    }
}
