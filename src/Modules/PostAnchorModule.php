<?php

namespace Log1x\Poet\Modules;

use Illuminate\Support\Arr;
use TOC\MarkupFixer;

class PostAnchorModule extends AbstractModule
{
    /**
     * The module key.
     *
     * @var string
     */
    protected $key = 'post';

    /**
     * The post types.
     *
     * @var array
     */
    protected $types = [];

    /**
     * Handle the module.
     *
     * @return void
     */
    public function handle()
    {
        $this->types = $this->config->filter(function ($value) {
            return ! empty($value['anchor']);
        })->mapWithKeys(function ($key, $value) {
            return [$key => $value['anchor']];
        });

        if ($this->types->isEmpty()) {
            return;
        }

        add_filter('the_content', function ($content) {
            if (
                ! $type = $this->types->get(get_post_type()) ||
                is_singular()
            ) {
                return;
            }

            return (new MarkupFixer())->fix(
                $content,
                is_array($type) ? ...$type : null
            );
        }, 20);
    }
}
