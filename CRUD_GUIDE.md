# Laravel 12 + Inertia 2 + Vue 3 CRUD Guide

A comprehensive guide for creating CRUD operations following Laravel 12, Inertia 2, and Vue 3 best practices.

## Table of Contents
- [Overview](#overview)
- [Step 1: Create the Model](#step-1-create-the-model)
- [Step 2: Create Migration](#step-2-create-migration)
- [Step 3: Create Factory and Seeder](#step-3-create-factory-and-seeder)
- [Step 4: Create Controller](#step-4-create-controller)
- [Step 5: Create Form Requests](#step-5-create-form-requests)
- [Step 6: Define Routes](#step-6-define-routes)
- [Step 7: Create Vue Components](#step-7-create-vue-components)
- [Step 8: Generate Wayfinder Types](#step-8-generate-wayfinder-types)
- [Step 9: Create Tests](#step-9-create-tests)
- [Step 10: Run and Verify](#step-10-run-and-verify)

## Overview

This guide assumes you're creating CRUD for a model called `Post`. Replace `Post` with your actual model name throughout.

## Step 1: Create the Model

Use Artisan to create the model with factory and migration:

```bash
php artisan make:model Post --factory --migration --no-interaction
```

**Edit `app/Models/Post.php`:**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    // Define fillable attributes
    protected $fillable = [
        'title',
        'content',
        'user_id',
        'published_at',
    ];

    // Define casts using method (Laravel 12 convention)
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    // Define relationships with return type hints
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

## Step 2: Create Migration

**Edit `database/migrations/YYYY_MM_DD_HHMMSS_create_posts_table.php`:**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('content');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
```

Run the migration:

```bash
php artisan migrate
```

## Step 3: Create Factory and Seeder

**Edit `database/factories/PostFactory.php`:**

```php
<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(),
            'content' => fake()->paragraphs(3, true),
            'published_at' => fake()->optional(0.7)->dateTimeBetween('-1 year', 'now'),
        ];
    }

    // Custom state for published posts
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => now(),
        ]);
    }

    // Custom state for draft posts
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => null,
        ]);
    }
}
```

**Optional - Create seeder:**

```bash
php artisan make:seeder PostSeeder --no-interaction
```

**Edit `database/seeders/PostSeeder.php`:**

```php
<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            Post::factory()
                ->count(5)
                ->for($user)
                ->create();
        }
    }
}
```

## Step 4: Create Controller

```bash
php artisan make:controller PostController --no-interaction
```

**Edit `app/Http/Controllers/PostController.php`:**

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class PostController extends Controller
{
    public function index(): Response
    {
        $posts = Post::query()
            ->with('user') // Eager load to prevent N+1
            ->latest()
            ->paginate(15);

        return Inertia::render('Posts/Index', [
            'posts' => $posts,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Posts/Create');
    }

    public function store(CreatePostRequest $request): RedirectResponse
    {
        $post = Post::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('posts.show', $post)
            ->with('success', 'Post created successfully.');
    }

    public function show(Post $post): Response
    {
        $post->load('user');

        return Inertia::render('Posts/Show', [
            'post' => $post,
        ]);
    }

    public function edit(Post $post): Response
    {
        return Inertia::render('Posts/Edit', [
            'post' => $post,
        ]);
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $post->update($request->validated());

        return redirect()
            ->route('posts.show', $post)
            ->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post deleted successfully.');
    }
}
```

## Step 5: Create Form Requests

**Create request:**

```bash
php artisan make:request CreatePostRequest --no-interaction
php artisan make:request UpdatePostRequest --no-interaction
```

**Edit `app/Http/Requests/CreatePostRequest.php`:**

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Or add authorization logic
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'published_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The post title is required.',
            'title.max' => 'The post title cannot exceed 255 characters.',
            'content.required' => 'The post content is required.',
        ];
    }
}
```

**Edit `app/Http/Requests/UpdatePostRequest.php`:**

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Check if user owns the post
        return $this->user()->id === $this->route('post')->user_id;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'published_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The post title is required.',
            'title.max' => 'The post title cannot exceed 255 characters.',
            'content.required' => 'The post content is required.',
        ];
    }
}
```

## Step 6: Define Routes

**Edit `routes/web.php`:**

```php
use App\Http\Controllers\PostController;

Route::middleware(['auth'])->group(function () {
    Route::resource('posts', PostController::class);
});
```

**Verify routes:**

```bash
php artisan route:list --name=posts
```

## Step 7: Create Vue Components

### Index Page

**Create `resources/js/pages/Posts/Index.vue`:**

```vue
<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { index, create, show, destroy } from '@/actions/App/Http/Controllers/PostController';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import type { Post, PaginatedData } from '@/types';

defineProps<{
    posts: PaginatedData<Post>;
}>();
</script>

<template>
    <Head title="Posts" />

    <div class="container mx-auto py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold">Posts</h1>
            <Link :href="create.url()">
                <Button>Create Post</Button>
            </Link>
        </div>

        <div class="grid gap-4">
            <Card v-for="post in posts.data" :key="post.id">
                <CardHeader>
                    <CardTitle>
                        <Link
                            :href="show.url(post.id)"
                            class="hover:underline"
                        >
                            {{ post.title }}
                        </Link>
                    </CardTitle>
                    <CardDescription>
                        By {{ post.user.name }} •
                        {{ new Date(post.created_at).toLocaleDateString() }}
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <p class="text-muted-foreground line-clamp-2">
                        {{ post.content }}
                    </p>
                </CardContent>
            </Card>
        </div>

        <!-- Pagination component here -->
    </div>
</template>
```

### Create Page

**Create `resources/js/pages/Posts/Create.vue`:**

```vue
<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Form } from '@inertiajs/vue3';
import { store } from '@/actions/App/Http/Controllers/PostController';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
</script>

<template>
    <Head title="Create Post" />

    <div class="container mx-auto py-8 max-w-2xl">
        <Card>
            <CardHeader>
                <CardTitle>Create New Post</CardTitle>
                <CardDescription>
                    Fill in the details to create a new post.
                </CardDescription>
            </CardHeader>
            <CardContent>
                <Form
                    v-bind="store.form()"
                    class="space-y-6"
                    #default="{ errors, processing }"
                >
                    <div class="grid gap-2">
                        <Label for="title">Title</Label>
                        <Input
                            id="title"
                            name="title"
                            type="text"
                            required
                        />
                        <InputError :message="errors.title" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="content">Content</Label>
                        <textarea
                            id="content"
                            name="content"
                            rows="10"
                            class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background placeholder:text-muted-foreground focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                            required
                        />
                        <InputError :message="errors.content" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="published_at">Publish Date (Optional)</Label>
                        <Input
                            id="published_at"
                            name="published_at"
                            type="datetime-local"
                        />
                        <InputError :message="errors.published_at" />
                    </div>

                    <div class="flex gap-4">
                        <Button type="submit" :disabled="processing">
                            {{ processing ? 'Creating...' : 'Create Post' }}
                        </Button>
                        <Button
                            type="button"
                            variant="outline"
                            @click="$inertia.visit('/posts')"
                        >
                            Cancel
                        </Button>
                    </div>
                </Form>
            </CardContent>
        </Card>
    </div>
</template>
```

### Show Page

**Create `resources/js/pages/Posts/Show.vue`:**

```vue
<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { edit, destroy, index } from '@/actions/App/Http/Controllers/PostController';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import type { Post } from '@/types';

defineProps<{
    post: Post;
}>();

const handleDelete = (postId: number) => {
    if (confirm('Are you sure you want to delete this post?')) {
        window.location.href = destroy.url(postId);
        // Or use Inertia: router.delete(destroy.url(postId))
    }
};
</script>

<template>
    <Head :title="post.title" />

    <div class="container mx-auto py-8 max-w-4xl">
        <div class="flex items-center justify-between mb-6">
            <Link :href="index.url()">
                <Button variant="outline">← Back to Posts</Button>
            </Link>
            <div class="flex gap-2">
                <Link :href="edit.url(post.id)">
                    <Button variant="outline">Edit</Button>
                </Link>
                <Button
                    variant="destructive"
                    @click="handleDelete(post.id)"
                >
                    Delete
                </Button>
            </div>
        </div>

        <Card>
            <CardHeader>
                <CardTitle class="text-4xl">{{ post.title }}</CardTitle>
                <CardDescription>
                    By {{ post.user.name }} •
                    {{ new Date(post.created_at).toLocaleDateString() }}
                    <span v-if="post.published_at">
                        • Published {{ new Date(post.published_at).toLocaleDateString() }}
                    </span>
                </CardDescription>
            </CardHeader>
            <CardContent>
                <div class="prose max-w-none">
                    {{ post.content }}
                </div>
            </CardContent>
        </Card>
    </div>
</template>
```

### Edit Page

**Create `resources/js/pages/Posts/Edit.vue`:**

```vue
<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Form } from '@inertiajs/vue3';
import { update, show } from '@/actions/App/Http/Controllers/PostController';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import type { Post } from '@/types';

const props = defineProps<{
    post: Post;
}>();

const formatDateTimeLocal = (date: string | null) => {
    if (!date) return '';
    return new Date(date).toISOString().slice(0, 16);
};
</script>

<template>
    <Head :title="`Edit ${post.title}`" />

    <div class="container mx-auto py-8 max-w-2xl">
        <Card>
            <CardHeader>
                <CardTitle>Edit Post</CardTitle>
                <CardDescription>
                    Update the post details below.
                </CardDescription>
            </CardHeader>
            <CardContent>
                <Form
                    v-bind="update.put(post.id).form()"
                    class="space-y-6"
                    #default="{ errors, processing }"
                >
                    <div class="grid gap-2">
                        <Label for="title">Title</Label>
                        <Input
                            id="title"
                            name="title"
                            type="text"
                            :default-value="post.title"
                            required
                        />
                        <InputError :message="errors.title" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="content">Content</Label>
                        <textarea
                            id="content"
                            name="content"
                            rows="10"
                            class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background placeholder:text-muted-foreground focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                            required
                        >{{ post.content }}</textarea>
                        <InputError :message="errors.content" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="published_at">Publish Date (Optional)</Label>
                        <Input
                            id="published_at"
                            name="published_at"
                            type="datetime-local"
                            :default-value="formatDateTimeLocal(post.published_at)"
                        />
                        <InputError :message="errors.published_at" />
                    </div>

                    <div class="flex gap-4">
                        <Button type="submit" :disabled="processing">
                            {{ processing ? 'Updating...' : 'Update Post' }}
                        </Button>
                        <Button
                            type="button"
                            variant="outline"
                            @click="$inertia.visit(show.url(post.id))"
                        >
                            Cancel
                        </Button>
                    </div>
                </Form>
            </CardContent>
        </Card>
    </div>
</template>
```

### TypeScript Types

**Add to `resources/js/types/index.d.ts`:**

```typescript
export interface Post {
    id: number;
    user_id: number;
    title: string;
    content: string;
    published_at: string | null;
    created_at: string;
    updated_at: string;
    user: User;
}

export interface PaginatedData<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}
```

## Step 8: Generate Wayfinder Types

Generate TypeScript types for your routes:

```bash
php artisan wayfinder:generate
```

This creates type-safe route helpers in `resources/js/actions/` that you can import and use.

## Step 9: Create Tests

### Feature Test

```bash
php artisan make:test PostTest --no-interaction
```

**Edit `tests/Feature/PostTest.php`:**

```php
<?php

use App\Models\Post;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('displays posts index page', function () {
    Post::factory()->count(3)->create();

    actingAs($this->user)
        ->get(route('posts.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page->component('Posts/Index'));
});

it('displays create post page', function () {
    actingAs($this->user)
        ->get(route('posts.create'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page->component('Posts/Create'));
});

it('creates a new post', function () {
    $postData = [
        'title' => 'Test Post',
        'content' => 'This is test content.',
        'published_at' => now()->toDateTimeString(),
    ];

    actingAs($this->user)
        ->post(route('posts.store'), $postData)
        ->assertRedirect();

    assertDatabaseHas('posts', [
        'title' => 'Test Post',
        'user_id' => $this->user->id,
    ]);
});

it('validates required fields when creating post', function () {
    actingAs($this->user)
        ->post(route('posts.store'), [])
        ->assertSessionHasErrors(['title', 'content']);
});

it('displays single post', function () {
    $post = Post::factory()->create();

    actingAs($this->user)
        ->get(route('posts.show', $post))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Posts/Show')
            ->has('post')
        );
});

it('displays edit post page', function () {
    $post = Post::factory()->for($this->user)->create();

    actingAs($this->user)
        ->get(route('posts.edit', $post))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page->component('Posts/Edit'));
});

it('updates a post', function () {
    $post = Post::factory()->for($this->user)->create();

    actingAs($this->user)
        ->put(route('posts.update', $post), [
            'title' => 'Updated Title',
            'content' => 'Updated content.',
        ])
        ->assertRedirect();

    assertDatabaseHas('posts', [
        'id' => $post->id,
        'title' => 'Updated Title',
    ]);
});

it('prevents unauthorized user from updating post', function () {
    $post = Post::factory()->create();
    $otherUser = User::factory()->create();

    actingAs($otherUser)
        ->put(route('posts.update', $post), [
            'title' => 'Hacked Title',
            'content' => 'Hacked content.',
        ])
        ->assertForbidden();
});

it('deletes a post', function () {
    $post = Post::factory()->for($this->user)->create();

    actingAs($this->user)
        ->delete(route('posts.destroy', $post))
        ->assertRedirect();

    expect(Post::find($post->id))->toBeNull();
});
```

### Run Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/PostTest.php

# Run specific test
php artisan test --filter="creates a new post"
```

## Step 10: Run and Verify

1. **Build frontend assets:**
   ```bash
   npm run build
   # or for development
   npm run dev
   ```

2. **Run Pint for code formatting:**
   ```bash
   vendor/bin/pint --dirty
   ```

3. **Verify routes:**
   ```bash
   php artisan route:list --name=posts
   ```

4. **Test in browser:**
   - Visit your app at the configured URL
   - Navigate to `/posts`
   - Test create, read, update, and delete operations

## Best Practices Checklist

- [ ] Model uses `casts()` method (not `$casts` property)
- [ ] Relationships have proper return type hints
- [ ] Migration follows naming conventions
- [ ] Factory includes custom states when appropriate
- [ ] Controller methods have return type hints
- [ ] Form Request classes used for validation (not inline validation)
- [ ] Routes use `Route::resource()` when possible
- [ ] Eager loading prevents N+1 queries
- [ ] Vue components use Wayfinder for type-safe routing
- [ ] Inertia Form component used for forms
- [ ] Tests cover happy paths, failure paths, and authorization
- [ ] Pint run before committing

## Common Issues

### Vite Manifest Error
If you see "Unable to locate file in Vite manifest":
```bash
npm run build
# or run dev server
npm run dev
```

### TypeScript Errors
Regenerate Wayfinder types:
```bash
php artisan wayfinder:generate
```

### Test Failures
Ensure database is migrated for testing:
```bash
php artisan migrate --env=testing
```

## Additional Resources

- Search Laravel docs: Use `search-docs` tool with queries
- List Artisan commands: `php artisan list`
- Check routes: `php artisan route:list`
