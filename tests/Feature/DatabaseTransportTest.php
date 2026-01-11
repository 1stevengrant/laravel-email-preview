<?php

namespace Ghijk\EmailPreview\Tests\Feature;

use Ghijk\EmailPreview\Models\CapturedEmail;
use Ghijk\EmailPreview\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

class DatabaseTransportTest extends TestCase
{
    use RefreshDatabase;

    public function test_emails_are_captured_to_database(): void
    {
        Mail::raw('Test email content', function ($message) {
            $message->to('recipient@example.com')
                ->subject('Test Email');
        });

        $this->assertDatabaseHas('captured_emails', [
            'subject' => 'Test Email',
        ]);

        $email = CapturedEmail::first();
        $this->assertStringContainsString('recipient@example.com', json_encode($email->to));
    }

    public function test_html_emails_are_captured(): void
    {
        Mail::html('<h1>Hello World</h1>', function ($message) {
            $message->to('test@example.com')
                ->subject('HTML Email');
        });

        $email = CapturedEmail::first();

        $this->assertEquals('HTML Email', $email->subject);
        $this->assertStringContainsString('Hello World', $email->html_body);
    }

    public function test_cc_and_bcc_are_captured(): void
    {
        Mail::raw('Test', function ($message) {
            $message->to('to@example.com')
                ->cc('cc@example.com')
                ->bcc('bcc@example.com')
                ->subject('CC BCC Test');
        });

        $email = CapturedEmail::first();

        $this->assertNotNull($email->cc);
        $this->assertNotNull($email->bcc);
    }
}
