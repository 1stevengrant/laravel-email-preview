<?php

namespace Ghijk\EmailPreview;

use Ghijk\EmailPreview\Console\Commands\CleanupCapturedEmails;
use Ghijk\EmailPreview\Mail\Transport\DatabaseTransport;
use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class EmailPreviewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/email-preview.php',
            'email-preview'
        );

        $this->registerMailerConfig();
        $this->registerMailTransport();
    }

    protected function registerMailerConfig(): void
    {
        $this->app['config']->set('mail.mailers.database', [
            'transport' => 'database',
        ]);
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/email-preview.php' => config_path('email-preview.php'),
        ], 'email-preview-config');

        $this->publishes([
            __DIR__.'/../database/migrations/create_captured_emails_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_captured_emails_table.php'),
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
            Route::middleware('web')->group(function (): void {
                require __DIR__.'/../routes/web.php';
            });
        }
    }

    protected function registerMailTransport(): void
    {
        $this->app->afterResolving('mail.manager', function (MailManager $manager): void {
            $manager->extend('database', fn (): \Ghijk\EmailPreview\Mail\Transport\DatabaseTransport => new DatabaseTransport);
        });
    }
}
