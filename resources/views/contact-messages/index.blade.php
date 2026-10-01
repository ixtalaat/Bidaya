@extends('layouts.app')

@section('title', __('pages.inbox_title'))

@section('content')
<div class="mb-6">
    <h1 class="font-display text-xl font-bold">{{ __('pages.inbox_title') }}</h1>
</div>

<div class="overflow-hidden clay-card">
    <table class="w-full text-sm">
        <thead class="bg-background">
            <tr>
                <th class="px-4 py-2 text-start font-medium">{{ __('pages.message_from') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('pages.form_subject') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('pages.form_phone') }}</th>
                <th class="px-4 py-2 text-start font-medium">{{ __('reports.date') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($messages as $message)
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-2">
                        <a href="{{ route('contact-messages.show', $message) }}" class="font-semibold text-primary hover:underline">{{ $message->name }}</a>
                    </td>
                    <td class="px-4 py-2">{{ $message->subject ?? '—' }}</td>
                    <td class="px-4 py-2" dir="ltr">{{ $message->phone }}</td>
                    <td class="px-4 py-2">{{ $message->created_at->toDateTimeString() }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-slate-500">{{ __('pages.inbox_empty') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $messages->links() }}</div>
@endsection
