<?php

namespace Log1x\Poet\Modules;

use Illuminate\Support\Str;

use function Roots\view;

class BlockModule extends AbstractModule
{
    /**
     * The module key.
     *
     * @var string
     */
    protected $key = 'block';

    /**
     * Handle the module.
     *
     * @return void
     */
    public function handle()
    {
        return $this->config->each(function ($value, $key) {
            if (empty($key) || is_int($key)) {
                $key = $value;
            }

            $value = $this->collect($value);

            if (! Str::contains($key, '/')) {
                $key = Str::start($key, $this->namespace());
            }

            return register_block_type($key, [
                'attributes' => $value->get('attributes', []),
                'render_callback' => function ($data, $content) use ($key, $value) {
                    return view($value->get('view', 'blocks.' . Str::after($key, '/')), [
                        'data' => (object) $data,
                        'content' => $value->get('strip', true) && $this->isEmpty($content) ? false : $content
                    ]);
                },
            ]);
        });
    }

    /**
     * Use the current theme's text domain as a namespace.
     *
     * @param  string $delimiter
     * @return string
     */
    protected function namespace($delimiter = '/')
    {
        return (Str::slug(
            wp_get_theme()->get('TextDomain')
        ) ?? 'sage') . $delimiter;
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
