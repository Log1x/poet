<?php

namespace Log1x\Poet\Modules;

use Illuminate\Support\Collection;

class Module
{
    /**
     * The module key.
     *
     * @param string[]
     */
    protected $key;

    /**
     * The module config.
     *
     * @param \Illuminate\Support\Collection
     */
    protected $config;

    /**
     * Initialize the Module instance.
     *
     * @return void
     */
    public function __construct(Collection $config)
    {
        if (empty($this->key)) {
            throw new LifecycleException(
                sprintf('Module %s is missing the key property.', get_class($this))
            );
        }

        $this->config = $config->get($this->key);
        $this->register();
    }
}
