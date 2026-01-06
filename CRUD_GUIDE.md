# Laravel 12 + Inertia 2 + Vue 3 CRUD Guide

A comprehensive guide for creating CRUD operations following Laravel 12, Inertia 2, and Vue 3 best practices.

## Table of Contents
- [Overview](#overview)
- [Step 1: Create the Model](#step-1-create-the-model)
- [Step 2: Create Migration](#step-2-create-migration)
- [Step 3: Create Factory and Seeder](#step-3-create-factory-and-seeder)
- [Step 4: Create Policy](#step-4-create-policy)
- [Step 5: Create Controller](#step-5-create-controller)
- [Step 6: Create API Resource](#step-6-create-api-resource)
- [Step 7: Create Form Requests](#step-7-create-form-requests)
- [Step 8: Define Routes](#step-8-define-routes)
- [Step 9: Create Vue Components](#step-9-create-vue-components)
- [Step 10: Generate Wayfinder Types](#step-10-generate-wayfinder-types)
- [Step 11: Create Tests](#step-11-create-tests)
- [Step 12: Run and Verify](#step-12-run-and-verify)

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

            // Indexes for performance
            $table->index('published_at');
            $table->index('created_at');
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

## Step 4: Create Policy

Create a policy to handle authorization logic for posts:

```bash
php artisan make:policy PostPolicy --model=Post --no-interaction
```

**Edit `app/Policies/PostPolicy.php`:**

```php
<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Post $post): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    public function restore(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    public function forceDelete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }
}
```

## Step 5: Create Controller

```bash
php artisan make:controller PostController --no-interaction
```

**Edit `app/Http/Controllers/PostController.php`:**

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function index(): Response
    {
        $posts = Post::query()
            ->with('user:id,name,email') // Eager load to prevent N+1, select only needed columns
            ->latest('created_at')
            ->paginate(15);

        return Inertia::render('Posts/Index', [
            'posts' => PostResource::collection($posts),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Post::class);

        return Inertia::render('Posts/Create');
    }

    public function store(CreatePostRequest $request): RedirectResponse
    {
        $this->authorize('create', Post::class);

        try {
            $post = Post::create([
                ...$request->validated(),
                'user_id' => $request->user()->id,
            ]);

            return redirect()
                ->route('posts.show', $post)
                ->with('flash', [
                    'type' => 'success',
                    'message' => 'Post created successfully!',
                ]);
        } catch (\Exception $e) {
            \Log::error('Failed to create post', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id,
            ]);

            return back()
                ->withInput()
                ->with('flash', [
                    'type' => 'error',
                    'message' => 'Failed to create post. Please try again.',
                ]);
        }
    }

    public function show(Post $post): Response
    {
        $post->load('user:id,name,email');

        return Inertia::render('Posts/Show', [
            'post' => PostResource::make($post),
            'canUpdate' => auth()->user()?->can('update', $post) ?? false,
            'canDelete' => auth()->user()?->can('delete', $post) ?? false,
        ]);
    }

    public function edit(Post $post): Response
    {
        $this->authorize('update', $post);

        return Inertia::render('Posts/Edit', [
            'post' => PostResource::make($post),
        ]);
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $this->authorize('update', $post);

        try {
            $post->update($request->validated());

            return redirect()
                ->route('posts.show', $post)
                ->with('flash', [
                    'type' => 'success',
                    'message' => 'Post updated successfully!',
                ]);
        } catch (\Exception $e) {
            \Log::error('Failed to update post', [
                'error' => $e->getMessage(),
                'post_id' => $post->id,
                'user_id' => $request->user()->id,
            ]);

            return back()
                ->withInput()
                ->with('flash', [
                    'type' => 'error',
                    'message' => 'Failed to update post. Please try again.',
                ]);
        }
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);

        try {
            $post->delete();

            return redirect()
                ->route('posts.index')
                ->with('flash', [
                    'type' => 'success',
                    'message' => 'Post deleted successfully!',
                ]);
        } catch (\Exception $e) {
            \Log::error('Failed to delete post', [
                'error' => $e->getMessage(),
                'post_id' => $post->id,
            ]);

            return back()
                ->with('flash', [
                    'type' => 'error',
                    'message' => 'Failed to delete post. Please try again.',
                ]);
        }
    }
}
```

## Step 6: Create API Resource

Create an API resource to transform the model data before sending it to the frontend:

```bash
php artisan make:resource PostResource --no-interaction
```

**Edit `app/Http/Resources/PostResource.php`:**

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'content' => $this->content,
            'published_at' => $this->published_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            'user' => $this->whenLoaded('user', [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]),
        ];
    }
}
```

## Step 7: Create Form Requests

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
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'content' => ['required', 'string', 'min:10', 'max:100000'],
            'published_at' => ['nullable', 'date', 'after_or_equal:now'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The post title is required.',
            'title.min' => 'The post title must be at least 3 characters.',
            'title.max' => 'The post title cannot exceed 255 characters.',
            'content.required' => 'The post content is required.',
            'content.min' => 'The post content must be at least 10 characters.',
            'content.max' => 'The post content cannot exceed 100,000 characters.',
            'published_at.after_or_equal' => 'The publish date cannot be in the past.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Sanitize input
        $this->merge([
            'title' => $this->title ? strip_tags($this->title) : null,
        ]);
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
        // Authorization is handled by PostPolicy in the controller
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'content' => ['required', 'string', 'min:10', 'max:100000'],
            'published_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The post title is required.',
            'title.min' => 'The post title must be at least 3 characters.',
            'title.max' => 'The post title cannot exceed 255 characters.',
            'content.required' => 'The post content is required.',
            'content.min' => 'The post content must be at least 10 characters.',
            'content.max' => 'The post content cannot exceed 100,000 characters.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Sanitize input
        $this->merge([
            'title' => $this->title ? strip_tags($this->title) : null,
        ]);
    }
}
```

## Step 8: Define Routes

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

## Step 9: Create Vue Components

### Index Page

**Create `resources/js/pages/Posts/Index.vue`:**

```vue
<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { index, create, show } from '@/actions/App/Http/Controllers/PostController';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Pagination,
    PaginationEllipsis,
    PaginationFirst,
    PaginationLast,
    PaginationList,
    PaginationListItem,
    PaginationNext,
    PaginationPrev,
} from '@/components/ui/pagination';
import type { Post, PaginatedData } from '@/types';

const props = defineProps<{
    posts: PaginatedData<Post>;
}>();

const getPageNumbers = () => {
    const pages: (number | 'ellipsis')[] = [];
    const { current_page, last_page } = props.posts;
    const delta = 2;

    for (let i = 1; i <= last_page; i++) {
        if (
            i === 1 ||
            i === last_page ||
            (i >= current_page - delta && i <= current_page + delta)
        ) {
            pages.push(i);
        } else if (pages[pages.length - 1] !== 'ellipsis') {
            pages.push('ellipsis');
        }
    }

    return pages;
};
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

        <div class="grid gap-4 mb-8">
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

        <Pagination
            v-if="posts.last_page > 1"
            v-slot="{ page }"
            :total="posts.total"
            :items-per-page="posts.per_page"
            :sibling-count="1"
            show-edges
            :default-page="posts.current_page"
        >
            <PaginationList class="flex items-center gap-1">
                <PaginationFirst as-child>
                    <Link :href="index.url({ query: { page: 1 } })">
                        <Button variant="outline" size="icon">
                            <span class="sr-only">First page</span>
                        </Button>
                    </Link>
                </PaginationFirst>
                <PaginationPrev as-child>
                    <Link
                        v-if="posts.current_page > 1"
                        :href="index.url({ query: { page: posts.current_page - 1 } })"
                    >
                        <Button variant="outline" size="icon">
                            <span class="sr-only">Previous page</span>
                        </Button>
                    </Link>
                </PaginationPrev>

                <template v-for="(item, index) in getPageNumbers()" :key="index">
                    <PaginationListItem v-if="item === 'ellipsis'">
                        <PaginationEllipsis />
                    </PaginationListItem>
                    <PaginationListItem v-else>
                        <Link :href="index.url({ query: { page: item } })">
                            <Button
                                variant="outline"
                                size="icon"
                                :class="{ 'bg-accent': item === posts.current_page }"
                            >
                                {{ item }}
                            </Button>
                        </Link>
                    </PaginationListItem>
                </template>

                <PaginationNext as-child>
                    <Link
                        v-if="posts.current_page < posts.last_page"
                        :href="index.url({ query: { page: posts.current_page + 1 } })"
                    >
                        <Button variant="outline" size="icon">
                            <span class="sr-only">Next page</span>
                        </Button>
                    </Link>
                </PaginationNext>
                <PaginationLast as-child>
                    <Link :href="index.url({ query: { page: posts.last_page } })">
                        <Button variant="outline" size="icon">
                            <span class="sr-only">Last page</span>
                        </Button>
                    </Link>
                </PaginationLast>
            </PaginationList>
        </Pagination>
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
import { Head, Link, router } from '@inertiajs/vue3';
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
import { ref } from 'vue';

defineProps<{
    post: Post;
    canUpdate: boolean;
    canDelete: boolean;
}>();

const isDeleting = ref(false);

const handleDelete = (postId: number) => {
    if (confirm('Are you sure you want to delete this post? This action cannot be undone.')) {
        isDeleting.value = true;
        router.delete(destroy.url(postId), {
            onFinish: () => {
                isDeleting.value = false;
            },
        });
    }
};
</script>

<template>
    <Head :title="post.title" />

    <div class="container mx-auto py-8 max-w-4xl">
        <div class="flex items-center justify-between mb-6">
            <Link :href="index.url()" aria-label="Back to posts list">
                <Button variant="outline">← Back to Posts</Button>
            </Link>
            <div v-if="canUpdate || canDelete" class="flex gap-2">
                <Link v-if="canUpdate" :href="edit.url(post.id)" aria-label="Edit post">
                    <Button variant="outline">Edit</Button>
                </Link>
                <Button
                    v-if="canDelete"
                    variant="destructive"
                    :disabled="isDeleting"
                    @click="handleDelete(post.id)"
                    aria-label="Delete post"
                >
                    {{ isDeleting ? 'Deleting...' : 'Delete' }}
                </Button>
            </div>
        </div>

        <Card>
            <CardHeader>
                <CardTitle class="text-4xl">{{ post.title }}</CardTitle>
                <CardDescription>
                    By {{ post.user.name }} •
                    <time :datetime="post.created_at">
                        {{ new Date(post.created_at).toLocaleDateString() }}
                    </time>
                    <span v-if="post.published_at">
                        • Published <time :datetime="post.published_at">
                            {{ new Date(post.published_at).toLocaleDateString() }}
                        </time>
                    </span>
                </CardDescription>
            </CardHeader>
            <CardContent>
                <div class="prose prose-slate dark:prose-invert max-w-none">
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
export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string | null;
    created_at: string;
    updated_at: string;
}

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
    links: {
        first: string | null;
        last: string | null;
        prev: string | null;
        next: string | null;
    };
    meta: {
        current_page: number;
        from: number | null;
        last_page: number;
        path: string;
        per_page: number;
        to: number | null;
        total: number;
    };
    // Convenience properties (also available on meta)
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

export interface FlashMessage {
    type: 'success' | 'error' | 'warning' | 'info';
    message: string;
}

export interface PageProps {
    auth: {
        user: User | null;
    };
    flash?: FlashMessage | null;
    errors?: Record<string, string>;
}
```

### Flash Message Component

**Create `resources/js/components/FlashMessage.vue`:**

```vue
<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed, watch, ref } from 'vue';
import type { PageProps } from '@/types';

const page = usePage<PageProps>();
const show = ref(false);

const flash = computed(() => page.props.flash);

watch(
    flash,
    (newFlash) => {
        if (newFlash) {
            show.value = true;
            setTimeout(() => {
                show.value = false;
            }, 5000);
        }
    },
    { immediate: true }
);

const bgClass = computed(() => {
    switch (flash.value?.type) {
        case 'success':
            return 'bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-200 border-green-200 dark:border-green-800';
        case 'error':
            return 'bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-200 border-red-200 dark:border-red-800';
        case 'warning':
            return 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-200 border-yellow-200 dark:border-yellow-800';
        case 'info':
            return 'bg-blue-50 dark:bg-blue-900/20 text-blue-800 dark:text-blue-200 border-blue-200 dark:border-blue-800';
        default:
            return 'bg-gray-50 dark:bg-gray-900/20 text-gray-800 dark:text-gray-200 border-gray-200 dark:border-gray-800';
    }
});
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 translate-y-4"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-4"
    >
        <div
            v-if="show && flash"
            :class="bgClass"
            class="fixed top-4 right-4 max-w-md p-4 rounded-lg border shadow-lg z-50"
            role="alert"
            aria-live="polite"
        >
            <div class="flex items-start gap-3">
                <div class="flex-1">
                    <p class="font-medium">{{ flash.message }}</p>
                </div>
                <button
                    type="button"
                    @click="show = false"
                    class="text-current opacity-70 hover:opacity-100 transition-opacity"
                    aria-label="Close notification"
                >
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </button>
            </div>
        </div>
    </Transition>
</template>
```

**Add FlashMessage to your main layout** (e.g., `resources/js/layouts/AppLayout.vue`):

```vue
<script setup lang="ts">
import FlashMessage from '@/components/FlashMessage.vue';
</script>

<template>
    <div>
        <FlashMessage />
        <!-- Your layout content -->
        <slot />
    </div>
</template>
```

## Step 10: Generate Wayfinder Types

Generate TypeScript types for your routes:

```bash
php artisan wayfinder:generate
```

This creates type-safe route helpers in `resources/js/actions/` that you can import and use.

## Step 11: Create Tests

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

it('validates post creation rules', function (string $field, mixed $value, string $error) {
    $validData = [
        'title' => 'Valid Post Title',
        'content' => 'This is valid content that meets the minimum length requirement.',
        'published_at' => now()->addDay()->toDateTimeString(),
    ];

    $validData[$field] = $value;

    actingAs($this->user)
        ->post(route('posts.store'), $validData)
        ->assertSessionHasErrors([$field => $error]);
})->with([
    'title too short' => ['title', 'ab', 'min'],
    'title too long' => ['title', str_repeat('a', 256), 'max'],
    'title missing' => ['title', '', 'required'],
    'content too short' => ['content', 'short', 'min'],
    'content missing' => ['content', '', 'required'],
    'published_at in past' => ['published_at', now()->subDay()->toDateTimeString(), 'after_or_equal'],
]);

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

it('prevents unauthorized user from accessing edit page', function () {
    $post = Post::factory()->create();
    $otherUser = User::factory()->create();

    actingAs($otherUser)
        ->get(route('posts.edit', $post))
        ->assertForbidden();
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

it('prevents unauthorized user from deleting post', function () {
    $post = Post::factory()->create();
    $otherUser = User::factory()->create();

    actingAs($otherUser)
        ->delete(route('posts.destroy', $post))
        ->assertForbidden();

    expect(Post::find($post->id))->not->toBeNull();
});
```

### Policy Test

```bash
php artisan make:test PostPolicyTest --unit --no-interaction
```

**Edit `tests/Unit/PostPolicyTest.php`:**

```php
<?php

use App\Models\Post;
use App\Models\User;
use App\Policies\PostPolicy;

beforeEach(function () {
    $this->policy = new PostPolicy;
    $this->user = User::factory()->create();
});

it('allows any authenticated user to view posts', function () {
    $post = Post::factory()->create();

    expect($this->policy->view($this->user, $post))->toBeTrue();
});

it('allows any authenticated user to create posts', function () {
    expect($this->policy->create($this->user))->toBeTrue();
});

it('allows post owner to update their post', function () {
    $post = Post::factory()->for($this->user)->create();

    expect($this->policy->update($this->user, $post))->toBeTrue();
});

it('prevents non-owner from updating post', function () {
    $post = Post::factory()->create();

    expect($this->policy->update($this->user, $post))->toBeFalse();
});

it('allows post owner to delete their post', function () {
    $post = Post::factory()->for($this->user)->create();

    expect($this->policy->delete($this->user, $post))->toBeTrue();
});

it('prevents non-owner from deleting post', function () {
    $post = Post::factory()->create();

    expect($this->policy->delete($this->user, $post))->toBeFalse();
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

## Step 12: Run and Verify

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

## Production Security & Performance Considerations

### Security

1. **Rate Limiting**
   Add rate limiting to prevent abuse:
   ```php
   // In routes/web.php
   Route::middleware(['auth', 'throttle:60,1'])->group(function () {
       Route::resource('posts', PostController::class);
   });
   ```

2. **CSRF Protection**
   - Laravel automatically protects against CSRF attacks via middleware
   - Ensure all forms use POST/PUT/PATCH/DELETE methods
   - Inertia handles CSRF tokens automatically

3. **XSS Protection**
   - Never use `v-html` with user-generated content
   - Vue automatically escapes content in templates
   - Backend sanitization via `strip_tags()` in Form Requests

4. **SQL Injection Protection**
   - Always use Eloquent or Query Builder (never raw queries with user input)
   - Use parameter binding if raw queries are necessary
   - Validate all input via Form Requests

5. **Mass Assignment Protection**
   - Define `$fillable` or `$guarded` on all models
   - Never use `Post::create($request->all())` directly

6. **Authorization**
   - Use Policies for all authorization logic
   - Always check permissions before sensitive operations
   - Pass authorization state to frontend (`canUpdate`, `canDelete`)

### Performance

1. **Database Optimization**
   - Add indexes on frequently queried columns (`user_id`, `created_at`, `published_at`)
   - Use eager loading to prevent N+1 queries: `->with('user:id,name,email')`
   - Select only needed columns when possible

2. **Caching** (Optional for high-traffic apps)
   ```php
   // Cache post index for 5 minutes
   $posts = Cache::remember('posts.index', 300, function () {
       return Post::with('user:id,name,email')->latest()->paginate(15);
   });
   ```

3. **API Resources**
   - Use `whenLoaded()` to conditionally include relationships
   - Avoid exposing sensitive data (passwords, tokens)
   - Transform dates to consistent format

4. **Frontend Optimization**
   - Implement pagination for large datasets
   - Use loading states to provide user feedback
   - Debounce search/filter inputs if implementing search

### Accessibility

1. **Semantic HTML**
   - Use `<time>` elements for dates
   - Proper heading hierarchy
   - Form labels associated with inputs

2. **ARIA Attributes**
   - Add `aria-label` to icon-only buttons
   - Use `role="alert"` for flash messages
   - Add `aria-live="polite"` for dynamic content updates

3. **Keyboard Navigation**
   - Ensure all interactive elements are keyboard accessible
   - Proper focus management in modals/dialogs
   - Visible focus indicators

4. **Screen Reader Support**
   - Descriptive button labels ("Delete post" not just "Delete")
   - Loading states announced ("Deleting..." instead of just disabled)
   - Error messages associated with form fields

### Error Handling

1. **Backend**
   - Try-catch blocks in controller methods
   - Meaningful error messages for users
   - Log errors for debugging: `Log::error($e->getMessage())`

2. **Frontend**
   - Display validation errors from backend
   - Handle network failures gracefully
   - Provide retry mechanisms for failed operations

### Environment-Specific Configuration

1. **Production Settings**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   LOG_LEVEL=error
   ```

2. **Queue Long-Running Tasks**
   - Consider queueing email notifications
   - Background processing for heavy operations

3. **Asset Optimization**
   ```bash
   npm run build  # Minified production assets
   ```

## Best Practices Checklist

### Backend
- [ ] Model uses `casts()` method (not `$casts` property)
- [ ] Relationships have proper return type hints
- [ ] Migration follows naming conventions with proper indexes
- [ ] Indexes added for frequently queried columns (`created_at`, `published_at`, etc.)
- [ ] Factory includes custom states when appropriate
- [ ] Policy created for centralized authorization logic
- [ ] Controller methods have return type hints
- [ ] Controller uses `$this->authorize()` to check policy permissions
- [ ] Error handling with try-catch blocks in controller
- [ ] API Resources used with `whenLoaded()` for relationships
- [ ] Dates formatted with `toIso8601String()` not `toISOString()`
- [ ] Form Request classes used for validation (not inline validation)
- [ ] Input sanitization in `prepareForValidation()` method
- [ ] Validation rules include min/max constraints
- [ ] Custom error messages for all validation rules
- [ ] Eager loading with specific columns: `with('user:id,name,email')`
- [ ] Flash messages use structured format with type and message

### Frontend
- [ ] TypeScript types defined for all data structures
- [ ] Complete type definitions including `User`, `PageProps`, `FlashMessage`
- [ ] Authorization abilities passed to frontend (`canUpdate`, `canDelete`)
- [ ] Vue components use Wayfinder for type-safe routing
- [ ] Inertia router used (not `window.location`) for navigation
- [ ] Inertia Form component used for forms
- [ ] Loading states implemented for async operations
- [ ] Flash message component created and added to layout
- [ ] Accessibility: ARIA labels on all buttons
- [ ] Accessibility: Semantic HTML (`<time>` for dates)
- [ ] Accessibility: Proper focus management
- [ ] Dark mode support with Tailwind `dark:` classes
- [ ] Pagination implemented for index pages

### Security
- [ ] Rate limiting applied to routes
- [ ] CSRF protection enabled (Laravel default)
- [ ] XSS protection: No `v-html` with user content
- [ ] SQL injection prevention: Eloquent/Query Builder only
- [ ] Mass assignment protection: `$fillable` defined
- [ ] Input sanitization with `strip_tags()` or similar

### Testing
- [ ] Tests cover happy paths, failure paths, and edge cases
- [ ] Authorization tests for policies
- [ ] Tests for update/delete preventing unauthorized access
- [ ] Validation rule tests with datasets
- [ ] Tests run successfully before deployment
- [ ] Pint run before committing

### Performance
- [ ] Database indexes on foreign keys and commonly queried columns
- [ ] N+1 queries prevented with eager loading
- [ ] Only necessary columns selected in queries
- [ ] Pagination for large datasets
- [ ] Assets built for production (`npm run build`)

### Production Readiness
- [ ] Environment variables properly configured
- [ ] Debug mode disabled in production
- [ ] Error logging configured
- [ ] All sensitive data excluded from API responses
- [ ] Proper HTTP status codes returned (403, 404, 422, etc.)

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
