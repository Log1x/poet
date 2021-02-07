<?php

namespace Log1x\Poet\Contracts;

interface Module
{
    /**
     * Initialize the Module handler.
     *
     * @return array
     */
    public function handle();
}
