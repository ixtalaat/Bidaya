<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactMessagesController extends Controller
{
    public function index(): View
    {
        $messages = ContactMessage::orderByDesc('id')->paginate(15);

        return view('contact-messages.index', ['messages' => $messages]);
    }

    public function show(ContactMessage $contactMessage): View
    {
        return view('contact-messages.show', ['message' => $contactMessage]);
    }

    public function destroy(ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->delete();

        return redirect()->route('contact-messages.index')->with('status', __('pages.message_deleted'));
    }
}
