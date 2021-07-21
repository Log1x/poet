<?php

namespace Log1x\Poet\Modules;

use Illuminate\Support\Str;

class BlockPatternCategoryModule extends AbstractModule
{
    /**
     * The module key.
     *
     * @var string
     */
    protected $key = 'block_pattern_category';

    /**
     * Handle the module.
     *
     * @return void
     */
    public function handle()
    {
        /** No need to load block pattern categories when loading the website frontend */
        if (!is_admin() || !class_exists('WP_Block_Patterns_Registry')) {
            return;
        }

        return $this->config->each(function ($categoryData, $categorySlug) {
            if (empty($categorySlug) || is_int($categorySlug)) {
                $categorySlug = $categoryData;
                $categoryData = [];
            }

            register_block_pattern_category(Str::slug($categorySlug), $categoryData);
        });
    }
}
