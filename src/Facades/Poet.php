<?php

namespace Log1x\Poet\Facades;

use Roots\Acorn\Facade;

class Poet extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'poet';
    }
}
