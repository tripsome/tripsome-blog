<?php

namespace Tripsome\Blog;

use App\Providers\TenancyServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Tripsome\Blog\Http\Controllers\ForgotPasswordController;
use Tripsome\Blog\Http\Controllers\LoginController;
use Tripsome\Blog\Http\Middleware\Authenticate;

class BlogServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerRoutes();
        $this->registerAuthGuard();
        $this->registerPublishing();

        $this->loadViewsFrom(
            __DIR__ . '/../resources/views',
            'blog'
        );
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    private function registerRoutes()
    {  
        Route::middleware([InitializeTenancyByDomain::class])->group(function () {
            $middlewareGroup = config('blog.middleware_group');
            Route::middleware($middlewareGroup)
                ->as('blog.')
                ->domain(config('blog.domain'))
                ->prefix(config('blog.path'))
                ->group(function () {
                    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('auth.login');
                    Route::post('/login', [LoginController::class, 'login'])->name('auth.attempt');

                    Route::get('/password/forgot', [ForgotPasswordController::class, 'showResetRequestForm'])->name('password.forgot');
                    Route::post('/password/forgot', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
                    Route::get('/password/reset/{token}', [ForgotPasswordController::class, 'showNewPassword'])->name('password.reset');
                });

            Route::middleware([$middlewareGroup, Authenticate::class])
                ->as('blog.')
                ->domain(config('blog.domain'))
                ->prefix(config('blog.path'))
                ->group(function () {
                    $this->loadRoutesFrom(__DIR__ . '/Http/routes.php');
                });
        });
    }

    /**
     * Register the package's authentication guard.
     *
     * @return void
     */
    private function registerAuthGuard()
    {
        $this->app['config']->set('auth.providers.blog_authors', [
            'driver' => 'eloquent',
            'model' => BlogAuthor::class,
        ]);

        $this->app['config']->set('auth.guards.blog', [
            'driver' => 'session',
            'provider' => 'blog_authors',
        ]);
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    private function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../public' => public_path('vendor/blog'),
            ], 'blog-assets');

            $this->publishes([
                __DIR__ . '/../config/blog.php' => config_path('blog.php'),
            ], 'blog-config');
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/blog.php',
            'blog'
        );
        $this->commands([
            Console\InstallCommand::class,
            Console\MigrateCommand::class,
        ]);
    }
}
