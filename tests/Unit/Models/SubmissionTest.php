<?php

declare(strict_types=1);

use App\Models\Form;
use App\Models\Submission;

test('to array', function (): void {
    $submission = Submission::factory()->create()->refresh();

    expect(array_keys($submission->toArray()))
        ->toBe([
            'id',
            'uuid',
            'form_id',
            'user_id',
            'player_id',
            'data',
            'payment_status',
            'created_at',
            'updated_at',
        ]);
});

test('belongs to form', function (): void {
    $form = Form::factory()->create();
    $submission = Submission::factory()->for($form)->create();

    expect($submission->form->is($form))->toBeTrue();
});
