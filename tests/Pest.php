<?php

use Ghijk\EmailPreview\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

pest()->extends(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature', 'Unit');
