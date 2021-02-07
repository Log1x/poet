<?php

namespace Log1x\Poet\Modules;

use Log1x\Poet\Concerns\HasCollection;
use Log1x\Poet\Contracts\Module;

class AbstractModule implements Module
{
    use HasCollection;

    /**
     * The module key.
     *
     * @param string
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
    }
}
