<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Bats;
use App\Enums\Throws;
use App\Models\Concerns\BelongsToOrganization;
use Carbon\CarbonInterface;
use Database\Factories\PlayerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property-read int $id
 * @property-read string $uuid
 * @property-read int $organization_id
 * @property-read string $first_name
 * @property-read string $last_name
 * @property-read CarbonInterface $dob
 * @property-read int|null $graduation_year
 * @property-read Bats|null $bats
 * @property-read Throws|null $throws
 * @property-read string|null $notes
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 * @property-read CarbonInterface|null $deleted_at
 */
final class Player extends Model
{
    /** @use HasFactory<PlayerFactory> */
    use BelongsToOrganization, HasFactory;

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'integer',
            'uuid' => 'string',
            'organization_id' => 'integer',
            'first_name' => 'string',
            'last_name' => 'string',
            'dob' => 'date',
            'graduation_year' => 'integer',
            'bats' => Bats::class,
            'throws' => Throws::class,
            'notes' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return BelongsToMany<Guardian, $this>
     */
    public function guardians(): BelongsToMany
    {
        return $this->belongsToMany(Guardian::class);
    }
}
