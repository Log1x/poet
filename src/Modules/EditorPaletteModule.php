<?php

namespace Log1x\Poet\Modules;

use Illuminate\Support\Str;

use function Roots\asset;

class EditorPaletteModule extends AbstractModule
{
    /**
     * The module key.
     *
     * @var string
     */
    protected $key = 'palette';

    /**
     * Handle the module.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->config === true || is_string($this->config)) {
            $palette = json_decode(
                asset(Str::finish(is_string($this->config) ? $this->config : 'palette', '.json'))->contents(),
                true
            );

            if (empty($palette)) {
                return;
            }

            return add_theme_support('editor-color-palette', $palette);
        }

        $palette = $this->config->map(function ($value, $key) {
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
