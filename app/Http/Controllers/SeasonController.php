<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

final class SeasonController
{
    public function index(): Response
    {
        return Inertia::render('season/Index');
    }
}
