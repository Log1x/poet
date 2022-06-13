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
        if (! class_exists('WP_Block_Patterns_Registry')) {
            return;
        }

        return $this->config->each(function ($value, $key) {
            if (empty($key) || is_int($key)) {
                $key = $value;
                $value = [];
            }

            register_block_pattern_category(Str::slug($key), $value);
        });
    }
}
