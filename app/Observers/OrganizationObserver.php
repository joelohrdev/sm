<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Organization;
use Illuminate\Support\Str;

final class OrganizationObserver
{
    public function creating(Organization $organization): void
    {
        $organization->uuid = (string) Str::uuid();
    }
}
