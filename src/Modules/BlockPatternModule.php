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
        if (! class_exists('WP_Block_Patterns_Registry')) {
            return;
        }

        return $this->config->each(function ($value, $key) {
            if (empty($key) || is_int($key)) {
                return;
            }

            $value = $this->collect($value);

            if (! Str::contains($key, '/')) {
                $key = Str::start($key, $this->namespace());
            }

            $view = 'block-patterns.' . Str::after($key, '/');

            if (view()->exists($view)) {
                $value['content'] = view($view)->render();
            }

            return register_block_pattern($key, $value->all());
        });
    }
}
