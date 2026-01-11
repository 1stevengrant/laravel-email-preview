# Laravel Email Preview

A Laravel package for capturing and previewing emails in a web UI, perfect for staging and testing environments.

## Features

- 📧 Capture all outgoing emails instead of sending them
- 🎨 View HTML and plain text versions of emails
- 🔍 Search and filter captured emails
- 📎 Download email attachments
- 🗄️ Store emails in database with auto-cleanup
- 🔐 Secure admin-only access
- ⚙️ Fully configurable routes and middleware
- 🎯 Zero configuration required for basic usage

## Installation

### 1. Install via Composer

```bash
composer require ghijk/laravel-email-preview
```

### 2. Publish and Run Migrations

```bash
php artisan vendor:publish --tag=email-preview-migrations
php artisan migrate
```

### 3. Configure Mail Driver

Set your mail driver to `database` in `.env`:

```env
MAIL_MAILER=database
```

That's it! You're ready to start capturing emails.

## Usage

### Viewing Captured Emails

Navigate to `/internal/emails` in your application (default route). The package provides:

- **Email List**: Paginated view of all captured emails with search and filters
- **Email Detail**: Full email preview with HTML/text toggle, headers, and attachments
- **Delete Options**: Delete individual emails or clear all

### Cleanup Old Emails

```bash
# Clean up emails older than 7 days (default)
php artisan email-preview:cleanup

# Custom retention period
php artisan email-preview:cleanup --days=30
```

### Scheduled Cleanup

Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('email-preview:cleanup')->daily();
}
```

## Configuration

### Publish Config (Optional)

```bash
php artisan vendor:publish --tag=email-preview-config
```

### Available Options

Configure via `.env`:

```env
# Table name for storing emails
EMAIL_PREVIEW_TABLE=captured_emails

# Number of days to retain emails
EMAIL_PREVIEW_RETENTION_DAYS=7

# Route configuration
EMAIL_PREVIEW_ROUTE_PREFIX=internal/emails
EMAIL_PREVIEW_ROUTE_NAME=internal.captured-emails
EMAIL_PREVIEW_MIDDLEWARE=auth,super_admin

# Enable/disable routes
EMAIL_PREVIEW_ROUTES_ENABLED=true

# Auto cleanup (still requires scheduler setup)
EMAIL_PREVIEW_AUTO_CLEANUP=false
```

### Custom Middleware

Update `config/email-preview.php`:

```php
'routes' => [
    'middleware' => ['auth', 'admin'], // Your custom middleware
],
```

## Frontend Views

The package uses Inertia.js and expects React components at:

- `resources/js/Pages/Admin/CapturedEmails/Index.tsx`
- `resources/js/Pages/Admin/CapturedEmails/Show.tsx`

These components should be provided by your application for full UI customization.

## API

### Model

```php
use Ghijk\EmailPreview\Models\CapturedEmail;

// Query captured emails
$emails = CapturedEmail::query()
    ->search('test@example.com')
    ->mailableClass('App\\Mail\\WelcomeMail')
    ->dateRange('2024-01-01', '2024-12-31')
    ->get();

// Access email data
$email->to; // Array of recipients
$email->subject;
$email->html_body;
$email->text_body;
$email->attachments; // Array of attachment data
$email->mailable_class; // Original mailable class name
```

### Routes

All routes use the configured prefix and middleware:

- `GET /internal/emails` - Email list
- `GET /internal/emails/{uuid}` - Email details
- `DELETE /internal/emails/{uuid}` - Delete email
- `DELETE /internal/emails` - Clear all emails

## How It Works

1. **Custom Mail Transport**: Intercepts outgoing emails via custom `database` mail driver
2. **Database Storage**: Stores complete email data including HTML, text, headers, and attachments
3. **Web UI**: Provides admin interface for viewing captured emails
4. **Auto-Cleanup**: Optional command to remove old emails based on retention policy

## Requirements

- PHP 8.1 or higher
- Laravel 10.0 or 11.0
- MySQL/PostgreSQL/SQLite (any Laravel-supported database)

## Security

- Always use appropriate middleware to restrict access
- Never use in production with real customer data
- Regularly clean up captured emails
- Review captured emails before deploying to production

## License

MIT License. See [LICENSE](LICENSE) for more information.

## Credits

Created by [Wild at Heart](https://wildatheart.org)

## Support

For issues and feature requests, please use the GitHub issue tracker.
