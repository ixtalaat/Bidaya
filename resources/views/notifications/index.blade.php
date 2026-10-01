@extends('layouts.app')

@section('title', __('notifications.title'))

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-xl font-bold">{{ __('notifications.title') }}</h1>
    <form method="POST" action="{{ route('notifications.read-all') }}">
        @csrf
        <button type="submit" class="rounded bg-slate-200 px-3 py-1.5 text-sm font-semibold text-slate-800 hover:bg-slate-300">
            {{ __('notifications.mark_all_read') }}
        </button>
    </form>
</div>

<div class="space-y-2">
    @forelse ($notifications as $notification)
        <div class="flex items-center justify-between gap-3 rounded-lg bg-white p-4 shadow {{ $notification->read_at === null ? 'border-s-indigo-500 border-s-4' : '' }}">
            <div>
                <p class="text-sm">{{ $notification->data['message_'.app()->getLocale()] ?? $notification->data['message_en'] ?? $notification->type }}</p>
                <p class="mt-1 text-xs text-slate-500">
                    {{ $notification->created_at->diffForHumans() }}
                    @if ($notification->read_at === null)
                        — {{ __('notifications.unread') }}
                    @endif
                </p>
            </div>
            <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="shrink-0">
                @csrf
                <button type="submit" class="rounded bg-indigo-700 px-3 py-1.5 text-sm font-semibold text-white hover:bg-indigo-800">
                    {{ __('notifications.mark_read') }}
                </button>
            </form>
        </div>
    @empty
        <p class="rounded-lg bg-white p-6 text-sm text-slate-500 shadow">{{ __('notifications.empty') }}</p>
    @endforelse
</div>

<div class="mt-4">{{ $notifications->links() }}</div>
@endsection
