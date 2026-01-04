<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Carbon\CarbonInterface;
use Database\Factories\GuardianFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property-read int $id
 * @property-read string $uuid
 * @property-read int $organization_id
 * @property-read int $user_id
 * @property-read string $first_name
 * @property-read string $last_name
 * @property-read string $email
 * @property-read string $phone
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
final class Guardian extends Model
{
    /** @use HasFactory<GuardianFactory> */
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
            'user_id' => 'integer',
            'first_name' => 'string',
            'last_name' => 'string',
            'email' => 'string',
            'phone' => 'string',
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
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsToMany<Player, $this>
     */
    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class);
    }
}
