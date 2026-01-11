<?php

namespace Ghijk\EmailPreview\Tests\Unit;

use Ghijk\EmailPreview\Models\CapturedEmail;
use Ghijk\EmailPreview\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CapturedEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_captured_email(): void
    {
        $email = CapturedEmail::create([
            'subject' => 'Test Subject',
            'from' => 'sender@example.com',
            'to' => ['recipient@example.com'],
            'html_body' => '<p>Test body</p>',
            'text_body' => 'Test body',
        ]);

        $this->assertNotNull($email->uuid);
        $this->assertEquals('Test Subject', $email->subject);
        $this->assertEquals(['recipient@example.com'], $email->to);
    }

    public function test_it_generates_uuid_on_creation(): void
    {
        $email = CapturedEmail::create([
            'subject' => 'Test',
            'from' => 'test@example.com',
            'to' => ['to@example.com'],
        ]);

        $this->assertNotNull($email->uuid);
        $this->assertEquals(36, strlen($email->uuid));
    }

    public function test_search_scope_filters_by_subject(): void
    {
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

        $this->assertCount(1, $results);
        $this->assertEquals('Welcome Email', $results->first()->subject);
    }

    public function test_recipient_scope_filters_by_to_address(): void
    {
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

        $this->assertCount(1, $results);
        $this->assertEquals('Test 1', $results->first()->subject);
    }
}
