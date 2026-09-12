<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormSubmitted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'type' => ['required', 'string', 'max:60'],
            'message' => ['required', 'string', 'max:5000'],
            // Honeypot — puste pole niewidoczne dla ludzi (ukryte w CSS).
            // Boty wypełniające każde pole formularza się w nie złapią.
            'website' => ['prohibited'],
        ]);

        Mail::to(config('mail.contact_recipient'))
            ->send(new ContactFormSubmitted($validated));

        return redirect()
            ->to(url('/').'#kontakt')
            ->with('contact_status', 'success');
    }
}
