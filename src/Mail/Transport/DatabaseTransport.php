<?php

namespace Ghijk\EmailPreview\Mail\Transport;

use Ghijk\EmailPreview\Models\CapturedEmail;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\MessageConverter;
use Symfony\Component\Mime\RawMessage;

class DatabaseTransport implements TransportInterface
{
    public function send(RawMessage $message, ?Envelope $envelope = null): ?SentMessage
    {
        $email = MessageConverter::toEmail($message);

        $this->captureEmail($email);

        return new SentMessage($message, $envelope ?? Envelope::create($message));
    }

    protected function captureEmail(Email $email): void
    {
        CapturedEmail::create([
            'to' => $this->extractAddresses($email->getTo()),
            'cc' => $this->extractAddresses($email->getCc()),
            'bcc' => $this->extractAddresses($email->getBcc()),
            'from' => $this->extractAddresses($email->getFrom())[0] ?? null,
            'reply_to' => $this->extractAddresses($email->getReplyTo())[0] ?? null,
            'subject' => $email->getSubject(),
            'html_body' => $email->getHtmlBody(),
            'text_body' => $email->getTextBody(),
            'headers' => $this->extractHeaders($email),
            'attachments' => $this->extractAttachments($email),
            'mailable_class' => $this->extractMailableClass($email),
            'metadata' => [],
        ]);
    }

    protected function extractAddresses(array $addresses): array
    {
        return array_map(fn (Address $address): string => $address->getAddress(), $addresses);
    }

    protected function extractHeaders(Email $email): array
    {
        $headers = [];

        foreach ($email->getHeaders()->all() as $header) {
            $headers[$header->getName()] = $header->getBodyAsString();
        }

        return $headers;
    }

    protected function extractAttachments(Email $email): array
    {
        $attachments = [];

        foreach ($email->getAttachments() as $attachment) {
            $attachments[] = [
                'name' => $attachment->getName(),
                'content_type' => $attachment->getMediaType().'/'.$attachment->getMediaSubtype(),
                'size' => mb_strlen($attachment->getBody()),
                'body' => base64_encode($attachment->getBody()),
            ];
        }

        return $attachments;
    }

    protected function extractMailableClass(Email $email): ?string
    {
        $headers = $email->getHeaders();

        if ($headers->has('X-Mailer')) {
            $mailer = $headers->get('X-Mailer')->getBodyAsString();
            if (str_contains($mailer, '::')) {
                return explode('::', $mailer)[0];
            }
        }

        return null;
    }

    public function __toString(): string
    {
        return 'database';
    }
}
