# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Package Overview

Laravel Email Preview is a package that captures outgoing emails and stores them in a database for preview in a web UI. Designed for staging/testing environments.

## Development Commands

```bash
# No test suite currently configured
# Package is installed into a Laravel application for testing

# Publish config to test application
php artisan vendor:publish --tag=email-preview-config

# Publish migrations to test application
php artisan vendor:publish --tag=email-preview-migrations

# Cleanup command
php artisan email-preview:cleanup --days=7
```

## Architecture

### Mail Transport Layer
The package registers a custom `database` mail transport via `EmailPreviewServiceProvider::registerMailTransport()`. When `MAIL_MAILER=database`, all outgoing emails are intercepted by `DatabaseTransport` which:
1. Converts Symfony messages to Email objects
2. Extracts all email data (recipients, body, headers, attachments)
3. Stores to database via `CapturedEmail` model
4. Returns a `SentMessage` without actually sending

### Key Components

**DatabaseTransport** (`src/Mail/Transport/DatabaseTransport.php`)
- Implements `Symfony\Component\Mailer\Transport\TransportInterface`
- Extracts mailable class from `X-Mailer` header if available
- Base64 encodes attachment bodies for JSON storage

**CapturedEmail Model** (`src/Models/CapturedEmail.php`)
- Dynamic table name from config
- Auto-generates UUID on creation
- Query scopes: `search()`, `recipient()`, `mailableClass()`, `dateRange()`
- JSON casts for: `to`, `cc`, `bcc`, `headers`, `attachments`, `metadata`

**Routes**
- Uses Inertia.js for rendering (expects React components in consuming app)
- Configurable prefix, middleware, and route names via config
- Routes can be completely disabled via `EMAIL_PREVIEW_ROUTES_ENABLED=false`

### Service Provider Flow
1. `register()`: Merges config, extends mail manager with database transport
2. `boot()`: Publishes config/migrations, registers cleanup command, loads routes

## Configuration

All settings in `config/email-preview.php` are env-driven:
- `EMAIL_PREVIEW_TABLE` - Database table name
- `EMAIL_PREVIEW_RETENTION_DAYS` - Auto-cleanup retention
- `EMAIL_PREVIEW_ROUTE_PREFIX` - URL prefix
- `EMAIL_PREVIEW_MIDDLEWARE` - Comma-separated middleware list

## Frontend Integration

The controller renders Inertia pages expecting these components in the consuming application:
- `Admin/CapturedEmails/Index`
- `Admin/CapturedEmails/Show`

The package does not ship frontend components - they must be created in the host application.
