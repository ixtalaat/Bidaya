@extends('layouts.app')

@section('title', __('notifications.title'))

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="font-display text-xl font-bold">{{ __('notifications.title') }}</h1>
    <form method="POST" action="{{ route('notifications.read-all') }}">
        @csrf
        <button type="submit" class="clay-btn cursor-pointer bg-white px-3 py-1.5 text-sm font-extrabold text-slate-900">
            {{ __('notifications.mark_all_read') }}
        </button>
    </form>
</div>

<div class="space-y-2">
    @forelse ($notifications as $notification)
        <div class="flex items-center justify-between gap-3 clay-card p-4 {{ $notification->read_at === null ? 'border-s-indigo-500 border-s-4' : '' }}">
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
                <button type="submit" class="rounded clay-btn cursor-pointer bg-secondary px-3 py-1.5 text-sm font-extrabold text-slate-900">
                    {{ __('notifications.mark_read') }}
                </button>
            </form>
        </div>
    @empty
        <p class="clay-card p-6 text-sm text-slate-500">{{ __('notifications.empty') }}</p>
    @endforelse
</div>

<div class="mt-4">{{ $notifications->links() }}</div>
@endsection
