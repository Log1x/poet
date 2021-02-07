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
    protected $key = 'post';

    /**
     * Add anchor ID attributes to post content heading selectors
     * when enabled on created or modified post types.
     *
     * This is done by simply passing `true` to `anchors` when
     * registering or modifying post types with Poet.
     *
     * If a heading already has a valid anchor ID present in the
     * form of a slug, it will be skipped.
     *
     * You may also optionally pass an array to `anchors` setting
     * a heading limit range. In this case, passing `4` would only
     * add anchor ID's to tags h1–h4.
     *   ↪ https://github.com/caseyamcl/toc
     *
     * @return void
     */
    public function handle()
    {
        add_filter('the_post', function () {
            $this->config
                ->only('post')
                ->collapse()
                ->each(function ($value, $key) {
                    if (
                        ! $anchors = Arr::get($value, 'anchors') ||
                        ! (Str::is($key, get_post_type()) && is_singular())
                    ) {
                        return;
                    }

                    return add_filter('the_content', function ($content) use ($anchors) {
                        $anchors = $this->collect($anchors)->filter(function ($value) {
                            return is_int($value);
                        });

                        return (new MarkupFixer())->fix(
                            $content,
                            ...$anchors->toArray()
                        );
                    });
                });
        }, 20);
    }
}
