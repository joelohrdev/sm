<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped('organization', function (): ?Organization {
            return auth()->user()?->organization();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->bootModelsDefaults();
    }

    private function bootModelsDefaults(): void
    {
        Model::unguard();
    }
}
