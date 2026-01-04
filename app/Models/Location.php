<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Carbon\CarbonInterface;
use Database\Factories\LocationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property-read string $uuid
 * @property-read int $organization_id
 * @property-read string $name
 * @property-read string $address
 * @property-read string $maps_link
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
final class Location extends Model
{
    /** @use HasFactory<LocationFactory> */
    use BelongsToOrganization, HasFactory;

    public function casts(): array
    {
        return [
            'id' => 'integer',
            'uuid' => 'string',
            'organization_id' => 'integer',
            'name' => 'string',
            'address' => 'string',
            'maps_link' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
