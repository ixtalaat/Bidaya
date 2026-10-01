@props(['tagline' => false, 'markSize' => 'h-10 w-10', 'textSize' => 'text-xl'])

<a href="{{ route('home') }}" class="flex items-center gap-2.5">
    <svg viewBox="0 0 48 48" class="{{ $markSize }} shrink-0" role="img" aria-label="Bidaya">
        <defs>
            <linearGradient id="bidaya-mark" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0" stop-color="#4f46e5" />
                <stop offset="1" stop-color="#7c3aed" />
            </linearGradient>
        </defs>
        <rect width="48" height="48" rx="12" fill="url(#bidaya-mark)" />
        <text x="22" y="34" text-anchor="middle" font-size="27" font-weight="800" fill="#ffffff" font-family="Cairo, sans-serif">ب</text>
        <circle cx="35" cy="12" r="3.5" fill="#fbbf24" />
        <path d="M35 18.5 L35 24" stroke="#fbbf24" stroke-width="2.5" stroke-linecap="round" />
    </svg>
    <span class="leading-tight">
        <span class="block font-extrabold text-slate-900 {{ $textSize }}">بداية</span>
        @if ($tagline)
            <span class="block text-xs font-normal text-slate-500">{{ __('layout.tagline') }}</span>
        @endif
    </span>
</a>
