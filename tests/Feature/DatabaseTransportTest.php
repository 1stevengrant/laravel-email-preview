<?php

use Ghijk\EmailPreview\Models\CapturedEmail;
use Illuminate\Support\Facades\Mail;

it('captures emails to database', function (): void {
    Mail::raw('Test email content', function ($message): void {
        $message->to('recipient@example.com')
            ->subject('Test Email');
    });

    $this->assertDatabaseHas('captured_emails', [
        'subject' => 'Test Email',
    ]);

    $email = CapturedEmail::first();
    expect(json_encode($email->to))->toContain('recipient@example.com');
});

it('captures html emails', function (): void {
    Mail::html('<h1>Hello World</h1>', function ($message): void {
        $message->to('test@example.com')
            ->subject('HTML Email');
    });

    $email = CapturedEmail::first();

    expect($email->subject)->toBe('HTML Email');
    expect($email->html_body)->toContain('Hello World');
});

it('captures cc and bcc', function (): void {
    Mail::raw('Test', function ($message): void {
        $message->to('to@example.com')
            ->cc('cc@example.com')
            ->bcc('bcc@example.com')
            ->subject('CC BCC Test');
    });

    $email = CapturedEmail::first();

    expect($email->cc)->not->toBeNull();
    expect($email->bcc)->not->toBeNull();
});
