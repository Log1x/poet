<?php

namespace Log1x\Poet\Modules;

use Illuminate\Support\Str;

use function Roots\asset;

class EditorPaletteModule extends Module
{
    /**
     * The module key.
     *
     * @param string[]
     */
    protected $key = ['palette'];

     /**
     * Register the configured color palette with the editor.
     *
     * @link https://developer.wordpress.org/block-editor/developers/themes/theme-support/#block-color-palettes
     * @return void
     */
    protected function register()
    {
        if (
            (is_bool($palette = $this->config->get('palette')->pop()) && $palette === true) ||
            is_string($palette)
        ) {
            $palette = json_decode(
                asset(Str::finish(is_string($palette) ? $palette : 'palette', '.json'))->contents(),
                true
            );

            if (empty($palette)) {
                return;
            }

            return add_theme_support('editor-color-palette', $palette);
        }

        $palette = $this->config->get('palette')->map(function ($value, $key) {
            if (! is_array($value)) {
                return [
                    'name' => Str::title($key),
                    'slug' => Str::slug($key),
                    'color' => $value,
                ];
            }

            return array_merge([
                'name' => Str::title($key),
                'slug' => Str::slug($key),
            ], $value ?? []);
        })
        ->values()
        ->filter();

        if ($palette->isEmpty()) {
            return;
        }

        return add_theme_support('editor-color-palette', $palette->all());
    }
}
