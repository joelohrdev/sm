# Organization Setup Notes

## Overview

This application uses a many-to-many relationship between Users and Organizations via a pivot table (`organization_user`). While the schema supports multiple organizations per user, the application currently enforces one organization per user at the application level.

## Database Structure

### Pivot Table: `organization_user`
- `organization_id` (foreign key)
- `user_id` (foreign key)
- `role` (string) - For future role-based access control
- `created_at` / `updated_at`
- Composite primary key: `(organization_id, user_id)`

## Model Relationships

### User Model
```php
// app/Models/User.php

// Many-to-many relationship
public function organizations(): BelongsToMany
{
    return $this->belongsToMany(Organization::class)
        ->withPivot('role')
        ->withTimestamps();
}

// Helper method to get the single organization
public function organization(): ?Organization
{
    return $this->organizations()->first();
}
```

### Organization Model
```php
// app/Models/Organization.php

public function users(): BelongsToMany
{
    return $this->belongsToMany(User::class)
        ->withPivot('role')
        ->withTimestamps();
}
```

## Global Access Setup

### 1. Backend Access

#### Service Container Binding
```php
// app/Providers/AppServiceProvider.php

public function register(): void
{
    $this->app->scoped('organization', function (): ?Organization {
        return auth()->user()?->organization();
    });
}
```

#### Usage Examples
```php
// Anywhere in controllers, services, jobs, etc.
$organization = app('organization');

// Or directly from the user
$organization = auth()->user()->organization();

// Access organization properties
$name = app('organization')->name;
$id = app('organization')->id;
```

### 2. Frontend Access (Inertia/Vue)

#### Sharing via HandleInertiaRequests
```php
// app/Http/Middleware/HandleInertiaRequests.php

public function share(Request $request): array
{
    return [
        ...parent::share($request),
        'organization' => fn () => app('organization'),
        // ... other shared data
    ];
}
```

#### Usage in Vue Components
```vue
<script setup>
import { usePage } from '@inertiajs/vue3'

const organization = usePage().props.organization
</script>

<template>
  <div>
    <h1>{{ organization.name }}</h1>
    <p>Slug: {{ organization.slug }}</p>
  </div>
</template>
```

#### Accessing in Composition API
```vue
<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const organization = computed(() => page.props.organization)
const organizationName = computed(() => page.props.organization?.name)
</script>
```

### 3. Auto-Filter Models by Organization

#### The BelongsToOrganization Trait
```php
// app/Models/Concerns/BelongsToOrganization.php

trait BelongsToOrganization
{
    protected static function bootBelongsToOrganization(): void
    {
        static::addGlobalScope('organization', function (Builder $query): void {
            if ($organization = app('organization')) {
                $query->where('organization_id', $organization->id);
            }
        });
    }
}
```

#### Using the Trait
Add this trait to any model that belongs to an organization:

```php
// Example: app/Models/Season.php
use App\Models\Concerns\BelongsToOrganization;

class Season extends Model
{
    use BelongsToOrganization;

    // All queries will automatically filter by the current user's organization
}
```

#### Models That Should Use This Trait
Based on the current schema, these models should use `BelongsToOrganization`:
- Season
- Division
- Location
- Player
- Guardian
- Team
- Form

#### Bypassing the Global Scope
When you need to query across all organizations:

```php
// Get all seasons regardless of organization
Season::withoutGlobalScope('organization')->get();

// Or for a specific query
Season::withoutGlobalScope('organization')
    ->where('active', true)
    ->get();
```

## Common Patterns

### Creating Resources for Current Organization
```php
// In a controller
public function store(Request $request)
{
    $validated = $request->validate([...]);

    $season = app('organization')->seasons()->create($validated);

    return redirect()->route('seasons.show', $season);
}
```

### Authorizing Access
```php
// In a policy
public function view(User $user, Season $season): bool
{
    return $season->organization_id === $user->organization()?->id;
}
```

### Eager Loading
```php
// Load organization with user
$user = User::with('organizations')->find($id);

// Or load users with their organizations
$users = User::with('organizations')->get();
```

## Future Considerations

### Supporting Multiple Organizations Per User

If you need to support multiple organizations in the future:

1. Add a `current_organization_id` column to the `users` table
2. Create methods to switch organizations:
```php
public function switchOrganization(Organization $organization): void
{
    if ($this->organizations->contains($organization)) {
        $this->update(['current_organization_id' => $organization->id]);
    }
}

public function currentOrganization(): ?Organization
{
    return $this->organizations()
        ->where('organizations.id', $this->current_organization_id)
        ->first();
}
```

3. Update the service binding:
```php
$this->app->scoped('organization', function (): ?Organization {
    return auth()->user()?->currentOrganization();
});
```

### Role-Based Permissions

The pivot table includes a `role` column. To use it:

```php
// Get user's role in their organization
$role = auth()->user()->organizations()->first()?->pivot->role;

// Check if user has a specific role
$isAdmin = auth()->user()
    ->organizations()
    ->wherePivot('role', Role::ADMIN)
    ->exists();
```

## Troubleshooting

### Organization is null
- Ensure the user is authenticated: `auth()->check()`
- Verify the user has an organization in the `organization_user` pivot table
- Check that the pivot table entry has correct foreign keys

### Global Scope Not Working
- Ensure the trait is added to the model: `use BelongsToOrganization;`
- Verify the model has an `organization_id` column
- Check that a user is authenticated when querying

### Inertia Props Not Updating
- Clear cache: `php artisan cache:clear`
- Rebuild frontend: `npm run build` or restart `npm run dev`
- Check browser console for errors
