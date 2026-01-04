<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Status;
use App\Models\Concerns\BelongsToOrganization;
use Carbon\CarbonInterface;
use Database\Factories\FormFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int $id
 * @property-read string $uuid
 * @property-read int $organization_id
 * @property-read string $name
 * @property-read Status $status
 * @property-read array<string, mixed> $schema
 * @property-read int $price
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
final class Form extends Model
{
    /** @use HasFactory<FormFactory> */
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
            'name' => 'string',
            'status' => Status::class,
            'schema' => 'json',
            'price' => 'integer',
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

    /**
     * @return HasMany<Submission, $this>
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }
}
