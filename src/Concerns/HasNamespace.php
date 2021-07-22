<?php

namespace Log1x\Poet\Concerns;

use Illuminate\Support\Str;

trait HasNamespace
{
    /**
     * Use the current theme's text domain as a namespace.
     *
     * @param string $delimiter
     * @return string
     */
    protected function namespace($delimiter = '/')
    {
        return (Str::slug(
            wp_get_theme()->get('TextDomain')
        ) ?? 'sage') . $delimiter;
    }
}
