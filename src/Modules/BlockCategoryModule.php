<?php

namespace Log1x\Poet\Modules;

use Illuminate\Support\Str;

class BlockCategoryModule extends AbstractModule
{
    /**
     * The module key.
     *
     * @param string[]
     */
    protected $key = 'blockCategory';

    /**
     * Register the configured block categories with the editor.
     *
     * If a category already exists, it will be modified instead.
     *
     * If a category already exists and is set to `false`, the category
     * will be unregistered.
     *
     * @return void
     */
    protected function registerCategories()
    {
        add_filter('block_categories', function ($categories) {
            $categories = $this->collect($categories)->keyBy('slug');

            return $this->config->get('categories')->map(function ($value, $key) use ($categories) {
                if (empty($key) || is_int($key)) {
                    $key = $value;
                }

                if ($categories->has($key)) {
                    if ($value === false) {
                        return $categories->forget($key);
                    }

                    if (is_string($value)) {
                        $value = ['title' => Str::title($value)];
                    }

                    return $categories->put(
                        $key,
                        array_merge($categories->get($key), $value)
                    );
                }

                if (! is_array($value)) {
                    return [
                        'slug' => Str::slug($key),
                        'title' => Str::title($value ?? $key),
                        'icon' => null,
                    ];
                }

                return array_merge([
                    'slug' => Str::slug($key),
                    'title' => Str::title($key),
                    'icon' => null,
                ], $value ?? []);
            })
            ->merge($categories->all())
            ->filter()
            ->sort()
            ->values()
            ->all();
        });
    }
}
