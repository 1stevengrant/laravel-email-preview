<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('captured_emails', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->json('to');
            $table->json('cc')->nullable();
            $table->json('bcc')->nullable();
            $table->string('from');
            $table->string('reply_to')->nullable();
            $table->string('subject');
            $table->longText('html_body')->nullable();
            $table->longText('text_body')->nullable();
            $table->json('headers')->nullable();
            $table->json('attachments')->nullable();
            $table->string('mailable_class')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('created_at');
            $table->index('mailable_class');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('captured_emails');
    }
};
