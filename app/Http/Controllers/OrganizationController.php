<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrganizationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

final class OrganizationController
{
    public function store(CreateOrganizationRequest $request): RedirectResponse
    {
        $user = auth()->user();

        if ($request->hasFile('logo_path')) {
            $logoPath = $request->file('logo_path')->store('logos', 'public');
        }

        $user->organizations()->create([
            'name' => $request->string('name'),
            'slug' => Str::slug($request->string('name')),
            'owner_id' => $user->getKey(),
            'logo_path' => $logoPath ?? null,
            'primary_color' => $request->string('primary_color'),
        ]);

        return to_route('dashboard')
            ->with('success', 'Organization created successfully.');
    }
}
