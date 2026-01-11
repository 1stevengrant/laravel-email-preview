<?php

use Ghijk\EmailPreview\Models\CapturedEmail;

it('can create captured email', function (): void {
    $email = CapturedEmail::create([
        'subject' => 'Test Subject',
        'from' => 'sender@example.com',
        'to' => ['recipient@example.com'],
        'html_body' => '<p>Test body</p>',
        'text_body' => 'Test body',
    ]);

    expect($email->uuid)->not->toBeNull();
    expect($email->subject)->toBe('Test Subject');
    expect($email->to)->toBe(['recipient@example.com']);
});

it('generates uuid on creation', function (): void {
    $email = CapturedEmail::create([
        'subject' => 'Test',
        'from' => 'test@example.com',
        'to' => ['to@example.com'],
    ]);

    expect($email->uuid)->not->toBeNull();
    expect(strlen((string) $email->uuid))->toBe(36);
});

it('filters by subject using search scope', function (): void {
    CapturedEmail::create([
        'subject' => 'Welcome Email',
        'from' => 'test@example.com',
        'to' => ['user@example.com'],
    ]);

    CapturedEmail::create([
        'subject' => 'Password Reset',
        'from' => 'test@example.com',
        'to' => ['user@example.com'],
    ]);

    $results = CapturedEmail::search('Welcome')->get();

    expect($results)->toHaveCount(1);
    expect($results->first()->subject)->toBe('Welcome Email');
});

it('filters by to address using recipient scope', function (): void {
    CapturedEmail::create([
        'subject' => 'Test 1',
        'from' => 'test@example.com',
        'to' => ['john@example.com'],
    ]);

    CapturedEmail::create([
        'subject' => 'Test 2',
        'from' => 'test@example.com',
        'to' => ['jane@example.com'],
    ]);

    $results = CapturedEmail::recipient('john@example.com')->get();

    expect($results)->toHaveCount(1);
    expect($results->first()->subject)->toBe('Test 1');
});
