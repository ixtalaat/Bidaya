@props(['tagline' => false, 'markSize' => 'h-11 w-11', 'textSize' => 'text-2xl'])

<a href="{{ route('home') }}" class="flex items-center gap-2.5" aria-label="Bidaya — {{ __('layout.tagline') }}">
    <svg viewBox="0 0 64 64" class="{{ $markSize }} shrink-0 drop-shadow-[3px_3px_0_#0f172a]" role="img" aria-hidden="true">
        <defs>
            <linearGradient id="bidaya-mark" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0" stop-color="#2563eb" />
                <stop offset="1" stop-color="#7c3aed" />
            </linearGradient>
        </defs>
        <rect x="3" y="3" width="58" height="58" rx="18" fill="url(#bidaya-mark)" stroke="#0f172a" stroke-width="3" />
        <circle cx="45" cy="17" r="6" fill="#fbbf24" stroke="#0f172a" stroke-width="2.5" />
        <path d="M32 49 V31" stroke="#ffffff" stroke-width="4.5" stroke-linecap="round" />
        <path d="M32 39 C24 39 18 34 16.5 26 C25 27 31 31 32 39 Z" fill="#4ade80" stroke="#0f172a" stroke-width="2.5" stroke-linejoin="round" />
        <path d="M32 35 C40 35 46 30 47.5 22 C39 23 33 27 32 35 Z" fill="#4ade80" stroke="#0f172a" stroke-width="2.5" stroke-linejoin="round" />
        <ellipse cx="32" cy="51" rx="11" ry="3" fill="#fbbf24" stroke="#0f172a" stroke-width="2" />
    </svg>
    <span class="leading-tight">
        <span class="block font-display font-bold text-slate-900 {{ $textSize }}">بداية</span>
        @if ($tagline)
            <span class="block text-xs font-semibold tracking-wide text-slate-500">BIDAYA • {{ __('layout.tagline') }}</span>
        @endif
    </span>
</a>
