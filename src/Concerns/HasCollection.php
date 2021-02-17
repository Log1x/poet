<?php

namespace Log1x\Poet\Concerns;

use Illuminate\Support\Collection;

trait HasCollection
{
    /**
     * Initialize a Collection instance.
     *
     * @param  string[] $value
     * @return \Illuminate\Support\Collection
     */
    public function collect($value)
    {
        return Collection::make($value);
    }
}
