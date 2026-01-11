<?php

namespace Ghijk\EmailPreview\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CapturedEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'to',
        'cc',
        'bcc',
        'from',
        'reply_to',
        'subject',
        'html_body',
        'text_body',
        'headers',
        'attachments',
        'mailable_class',
        'metadata',
    ];

    protected $casts = [
        'to' => 'array',
        'cc' => 'array',
        'bcc' => 'array',
        'headers' => 'array',
        'attachments' => 'array',
        'metadata' => 'array',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('email-preview.table', 'captured_emails');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (! $model->uuid) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function scopeRecipient($query, string $email)
    {
        return $query->where(function ($q) use ($email) {
            $q->whereJsonContains('to', $email)
                ->orWhereJsonContains('cc', $email)
                ->orWhereJsonContains('bcc', $email);
        });
    }

    public function scopeMailableClass($query, string $class)
    {
        return $query->where('mailable_class', $class);
    }

    public function scopeDateRange($query, ?string $from = null, ?string $to = null)
    {
        if ($from) {
            $query->where('created_at', '>=', $from);
        }

        if ($to) {
            $query->where('created_at', '<=', $to);
        }

        return $query;
    }

    public function scopeSearch($query, ?string $term = null)
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('subject', 'like', "%{$term}%")
                ->orWhere('from', 'like', "%{$term}%")
                ->orWhere('mailable_class', 'like', "%{$term}%")
                ->orWhereJsonContains('to', $term);
        });
    }
}
