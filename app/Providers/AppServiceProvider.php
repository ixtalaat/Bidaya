<?php

namespace App\Providers;

use App\Contracts\WhatsAppGateway;
use App\Enums\Role;
use App\Models\User;
use App\Services\MetaWhatsAppGateway;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(WhatsAppGateway::class, fn (): MetaWhatsAppGateway => new MetaWhatsAppGateway(
            (string) config('services.whatsapp.phone_number_id'),
            (string) config('services.whatsapp.token'),
            (bool) config('services.whatsapp.enabled'),
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! app()->isProduction());

        Gate::define('manage-center', fn (User $user): bool => $user->hasRole(Role::SuperAdmin, Role::Manager));
        Gate::define('record-payments', fn (User $user): bool => $user->hasRole(Role::SuperAdmin, Role::Manager, Role::Accountant));

        View::composer('layouts.app', function ($view): void {
            $view->with('unreadNotificationsCount', auth()->user()?->unreadNotifications()->count() ?? 0);
        });
    }
}
