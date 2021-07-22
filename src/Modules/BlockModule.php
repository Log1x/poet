<?php

namespace Log1x\Poet\Modules;

use Illuminate\Support\Str;
use Log1x\Poet\Concerns\HasNamespace;

use function Roots\view;

class BlockModule extends AbstractModule
{
    use HasNamespace;

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
