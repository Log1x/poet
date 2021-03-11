<?php

namespace Log1x\Poet\Modules;

use Illuminate\Support\Str;

class AdminMenuModule extends AbstractModule
{
    /**
     * The module key.
     *
     * @var string
     */
    protected $key = 'admin_menu';

    /**
     * Handle the module.
     *
     * @return void
     */
    public function handle()
    {
        add_filter('admin_menu', function () {
            $this->config = $this->config->mapWithKeys(function ($value, $key) {
                $page = admin_url("admin.php?page={$value}");

                return is_int($key) ? [$value => $page] : [$key => $page];
            });

            if ($this->config->isEmpty()) {
                return;
            }

            $menus = $this->collect($GLOBALS['menu'])
                ->flatMap(function ($value) {
                    $key = Str::afterLast($value[2], '=');

                    if (empty($this->config->get($key))) {
                        return;
                    }

                    return [$value[2] => [$value[0], $value[3], $value[1], $this->config->get($key)]];
                });

            $menus->each(function ($value, $key) {
                remove_menu_page($key);
                add_submenu_page('tools.php', ...array_values($value));
            });
        }, 20);
    }
}
