<?php

namespace Marshmallow\NovaSpotlight;

use LivewireUI\Spotlight\Spotlight;
use Illuminate\Support\ServiceProvider;
use Marshmallow\NovaSpotlight\Spotlight\NewNova;
use Marshmallow\NovaSpotlight\Spotlight\EditNova;
use Marshmallow\NovaSpotlight\Spotlight\ViewNova;

class NovaSpotlightServiceProvider extends ServiceProvider
{

    const NOVA_VIEWS_PATH = __DIR__ . '/../resources/views';

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Views
        $this->publishes([
            self::NOVA_VIEWS_PATH => resource_path('views/vendor/nova'),
        ], 'views');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Spotlight::registerCommand(EditNova::class);
        Spotlight::registerCommand(NewNova::class);
        Spotlight::registerCommand(ViewNova::class);
    }
}
