<?php

namespace Log1x\Poet\Modules;

use Illuminate\Support\Str;

class AdminMenuModule extends AbstractModule
{
    /**
     * The module key.
     *
     * @param string
     */
    protected $key = 'adminMenu';

    /**
     * Handle the module.
     *
     * @return void
     */
    public function handle()
    {
        add_filter('admin_menu', function () {
            if ($this->config->isEmpty()) {
                return;
            }

            $GLOBALS['menu'] = $this->collect($GLOBALS['menu'])->map(function ($item) {
                if (! $this->config->contains(Str::afterLast($item[2], '='))) {
                    return $item;
                }

                if ($this->config->get($item[2]) === false) {
                    return;
                }

                array_push(
                    $GLOBALS['submenu']['tools.php'],
                    $this->collect($item)->slice(0, 2)->push(
                        admin_url(
                            (is_string($this->config->get($item[2])) ? $item[2] : Str::contains($item[2], '.php'))
                                ? $item[2] : Str::start($item[2], 'admin.php?page=')
                        )
                    )->all()
                );
            })->filter()->all();
        }, 20);
    }
}
