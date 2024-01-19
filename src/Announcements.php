<?php

namespace AMGPortal\Announcements;

use Event;
use Route;
use AMGPortal\Announcements\Events\EmailNotificationRequested;
use AMGPortal\Announcements\Hooks\NavbarItemsHook;
use AMGPortal\Announcements\Hooks\ScriptsHook;
use AMGPortal\Announcements\Hooks\StylesHook;
use AMGPortal\Announcements\Listeners\SendEmailNotification;
use AMGPortal\Announcements\Repositories\AnnouncementsRepository;
use AMGPortal\Announcements\Repositories\EloquentAnnouncements;
use AMGPortal\Plugins\Plugin;
use AMGPortal\Support\Sidebar\Item;
use AMGPortal\Announcements\Listeners\ActivityLogSubscriber;
use AMGPortal\Plugins\AMGPortal;

class Announcements extends Plugin
{
    /**
     * A sidebar item for the plugin.
     * @return Item|null
     */
    public function sidebar()
    {
        // return Item::create(__('Announcements'))
        //     ->icon('fas fa-bullhorn')
        //     ->route('announcements.index')
        //     ->permissions('announcements.manage')
        //     ->active('announcements*');
    }

    /**
     * Register plugin services.
     */
    public function register()
    {
        $this->app->singleton(AnnouncementsRepository::class, EloquentAnnouncements::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot()
    {
        $this->registerViews();
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'announcements');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'announcements');
        $this->loadJsonTranslationsFrom(__DIR__.'/../resources/lang');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations')
        ], 'migrations');

        $this->mapRoutes();

        $this->registerHooks();

        $this->registerEventListeners();

        $this->publishAssets();
    }

    /**
     * Register plugin views.
     *
     * @return void
     */
    protected function registerViews()
    {
        $viewsPath = __DIR__.'/../resources/views';

        $this->publishes([
            $viewsPath => resource_path('views/vendor/plugins/announcements')
        ], 'views');

        $this->loadViewsFrom($viewsPath, 'announcements');
    }

    /**
     * Map all plugin related routes.
     */
    protected function mapRoutes()
    {
        $this->mapWebRoutes();

        if ($this->app['config']->get('auth.expose_api')) {
            $this->mapApiRoutes();
        }
    }

    /**
     * Map web plugin related routes.
     */
    protected function mapWebRoutes()
    {
        Route::group([
            'namespace' => 'AMGPortal\Announcements\Http\Controllers\Web',
            'middleware' => 'web',
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }

    /**
     * Map API plugin related routes.
     */
    protected function mapApiRoutes()
    {
        Route::group([
            'namespace' => 'AMGPortal\Announcements\Http\Controllers\Api',
            'middleware' => 'api',
            'prefix' => 'api',
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        });
    }

    /**
     * Register plugin event listeners.
     */
    private function registerEventListeners()
    {
        // Register activity log subscriber only if
        // UserActivity plugin is installed.
        if ($this->app->bound('AMGPortal\UserActivity\Repositories\Activity\ActivityRepository')) {
            Event::subscribe(ActivityLogSubscriber::class);
        }

        Event::listen(EmailNotificationRequested::class, SendEmailNotification::class);
    }

    /**
     * Register all necessary view hooks for the plugin.
     */
    private function registerHooks()
    {
        AMGPortal::hook('navbar:items', NavbarItemsHook::class);
        AMGPortal::hook('app:styles', StylesHook::class);
        AMGPortal::hook('app:scripts', ScriptsHook::class);
    }

    /**
     * Publish public assets.
     *
     * @return void
     */
    protected function publishAssets()
    {
        $this->publishes([
            realpath(__DIR__.'/../dist') => $this->app['path.public'].'/vendor/plugins/announcements',
        ], 'public');
    }
}
