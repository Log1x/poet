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
     * Moves configured admin menu parent items into the Tools.php submenu.
     * Items are configured by simply passing the `page` slug of each plugin.
     *
     * If an item is explicitly set to `false`, the menu item will be
     * removed entirely instead.
     *
     * @return void
     */
    public function handle()
    {
        add_filter('admin_menu', function () {
            $menu = $this->config->get('menu');

            if ($menu->isEmpty()) {
                return;
            }

            $GLOBALS['menu'] = $this->collect($GLOBALS['menu'])->map(function ($item) use ($menu) {
                if (! $menu->contains(Str::afterLast($item[2], '='))) {
                    return $item;
                }

                if ($menu->get($item[2]) === false) {
                    return;
                }

                array_push(
                    $GLOBALS['submenu']['tools.php'],
                    $this->collect($item)->slice(0, 2)->push(
                        admin_url(
                            (is_string($menu->get($item[2])) ? $item[2] : Str::contains($item[2], '.php'))
                                ? $item[2] : Str::start($item[2], 'admin.php?page=')
                        )
                    )->all()
                );
            })->filter()->all();
        }, 20);
    }
}
