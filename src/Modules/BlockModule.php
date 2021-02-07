<?php

namespace Log1x\Poet\Modules;

use Illuminate\Support\Str;

use function Roots\view;

class BlockModule extends Module
{
    /**
     * The module key.
     *
     * @param string[]
     */
    protected $key = ['block', 'blocks'];

    /**
     * Register the configured block types with the editor using Blade
     * for rendering the registered block.
     *
     * If no namespace is provided on the block, Poet will default to
     * the current theme text domain.
     *
     * Optionally, you may pass a block as an array containing:
     *   ↪ attributes – An array of custom block attributes.
     *   ↪ strip – When set to false, `$content` will always return true.
     *
     * Given the Block `sage/accordion`, the Block view would be located at:
     *   ↪ `views/blocks/accordion.blade.php`
     *
     * Block views have the following variables available:
     *   ↪ $data    – An object containing the block data.
     *   ↪ $content – A string containing the InnerBlocks content.
     *                Returns `false` when empty.
     *
     * @return void
     */
    protected function registerBlocks()
    {
        return $this->config->get('block')->each(function ($value, $key) {
            if (empty($key) || is_int($key)) {
                $key = $value;
            }

            $value = collect($value);

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
}
