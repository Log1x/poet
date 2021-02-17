<?php

namespace Log1x\Poet\Contracts;

interface Module
{
    /**
     * Handle the module.
     *
     * @return array
     */
    public function handle();
}
