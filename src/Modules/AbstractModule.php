<?php

namespace Log1x\Poet\Modules;

use Illuminate\Support\Collection;
use Log1x\Poet\Concerns\HasCollection;
use Log1x\Poet\Contracts\Module;
use Roots\Acorn\Application;

abstract class AbstractModule implements Module
{
    use HasCollection;

    /**
     * The Application instance.
     *
     * @var \Roots\Acorn\Application
     */
    protected $app;

    /**
     * The module key.
     *
     * @var string
     */
    protected $key;

    /**
     * The module config.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $config;

    /**
     * Initialize the Module instance.
     *
     * @param  \Roots\Acorn\Application $app
     * @return void
     */
    public function __construct(Application $app, Collection $config)
    {
        if (empty($this->key)) {
            throw new LifecycleException(
                sprintf('Module %s is missing the key property.', get_class($this))
            );
        }

        $this->app = $app;
        $this->config = $config->get($this->key);

        $this->boot();
    }

    /**
     * Boot the module.
     *
     * @return void
     */
    protected function boot()
    {
        if (empty($this->config)) {
            return;
        }

        $method = method_exists($this, 'handle') ? 'handle' : '__invoke';

        $this->app->call([$this, $method]);
    }
}
