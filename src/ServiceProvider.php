<?php

namespace Foorintodev\Seo;

use Foorintodev\Seo\Http\Middleware\RedirectFromStore;
use Foorintodev\Seo\Listeners\CreateRedirectOnSlugChange;
use Foorintodev\Seo\Tags\Seo;
use Illuminate\Support\Facades\Event;
use Statamic\Events\EntrySaved;
use Statamic\Facades\CP\Nav;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $tags = [
        Seo::class,
    ];

    // Bundle JS du panneau d'admin (aperçus Google + réseaux sociaux).
    // Construit par `npm run build` (voir vite.config.js), publié dans public/vendor.
    protected $scripts = [
        __DIR__.'/../resources/dist/js/cp.js',
    ];

    public function register()
    {
        parent::register();

        $this->app->singleton(Redirects::class);
    }

    public function bootAddon()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/seo.php', 'foorintodev-seo');

        // Redirections : écoute les changements de slug + sert les 301 enregistrées.
        Event::listen(EntrySaved::class, [CreateRedirectOnSlugChange::class, 'handle']);
        $this->app['router']->pushMiddlewareToGroup('web', RedirectFromStore::class);

        // Entrée de menu du panneau, section « Outils ».
        Nav::extend(function ($nav) {
            $nav->create(__('Redirections'))
                ->section('Tools')
                ->route('foorintodev-seo.redirects.index')
                ->icon('add-link');
        });

        // `php artisan vendor:publish --tag=foorintodev-seo` publie la config + les fieldsets.
        $this->publishes([
            __DIR__.'/../config/seo.php' => config_path('foorintodev-seo.php'),
            __DIR__.'/../resources/fieldsets/seo.yaml' => resource_path('fieldsets/seo.yaml'),
            __DIR__.'/../resources/fieldsets/seo_social.yaml' => resource_path('fieldsets/seo_social.yaml'),
            __DIR__.'/../resources/fieldsets/redirect.yaml' => resource_path('fieldsets/redirect.yaml'),
            __DIR__.'/../resources/blueprints/globals/errors.yaml' => resource_path('blueprints/globals/errors.yaml'),
            __DIR__.'/../resources/views/errors/404.antlers.html' => resource_path('views/errors/404.antlers.html'),
        ], 'foorintodev-seo');
    }
}
