<?php

namespace Ghijk\EmailPreview\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\RedirectResponse;
use Ghijk\EmailPreview\Models\CapturedEmail;

class CapturedEmailsController extends Controller
{
    public function index(Request $request): Response
    {
        $emails = CapturedEmail::query()
            ->when($request->search, fn ($query) => $query->search($request->search))
            ->when($request->mailable_class, fn ($query) => $query->mailableClass($request->mailable_class))
            ->when($request->from || $request->to, fn ($query) => $query->dateRange($request->from, $request->to))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $mailableClasses = CapturedEmail::query()
            ->whereNotNull('mailable_class')
            ->distinct()
            ->pluck('mailable_class')
            ->sort()
            ->values();

        return Inertia::render('Admin/CapturedEmails/Index', [
            'emails' => $emails,
            'mailableClasses' => $mailableClasses,
            'filters' => $request->only(['search', 'mailable_class', 'from', 'to']),
        ]);
    }

    public function show(string $uuid): Response
    {
        $email = CapturedEmail::where('uuid', $uuid)->firstOrFail();

        return Inertia::render('Admin/CapturedEmails/Show', [
            'email' => $email,
        ]);
    }

    public function destroy(string $uuid): RedirectResponse
    {
        $email = CapturedEmail::where('uuid', $uuid)->firstOrFail();
        $email->delete();

        $routeName = config('email-preview.routes.name') . '.index';

        return to_route($routeName)->with('success', 'Email deleted successfully.');
    }

    public function clear(): RedirectResponse
    {
        CapturedEmail::truncate();

        $routeName = config('email-preview.routes.name') . '.index';

        return to_route($routeName)->with('success', 'All emails cleared successfully.');
    }
}
