<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToOrganization
{
    /**
     * Boot the trait and add global scope.
     */
    protected static function bootBelongsToOrganization(): void
    {
        static::addGlobalScope('organization', function (Builder $query): void {
            if ($organization = app('organization')) {
                $query->where('organization_id', $organization->id);
            }
        });
    }
}
