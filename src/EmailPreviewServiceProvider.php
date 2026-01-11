<?php

namespace Ghijk\EmailPreview;

use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Ghijk\EmailPreview\Mail\Transport\DatabaseTransport;
use Ghijk\EmailPreview\Console\Commands\CleanupCapturedEmails;

class EmailPreviewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/email-preview.php',
            'email-preview'
        );

        $this->registerMailTransport();
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/email-preview.php' => config_path('email-preview.php'),
        ], 'email-preview-config');

        $this->publishes([
            __DIR__ . '/../database/migrations/create_captured_emails_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_captured_emails_table.php'),
        ], 'email-preview-migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                CleanupCapturedEmails::class,
            ]);
        }

        $this->loadRoutes();
    }

    protected function loadRoutes(): void
    {
        if (! $this->app->routesAreCached()) {
            Route::middleware('web')->group(function () {
                require __DIR__ . '/../routes/web.php';
            });
        }
    }

    protected function registerMailTransport(): void
    {
        $this->app->extend('mail.manager', function (MailManager $manager) {
            $manager->extend('database', function () {
                return new DatabaseTransport;
            });

            return $manager;
        });
    }
}
