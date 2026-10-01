@props(['href' => '#', 'active' => false, 'dropdownItem' => false])

<a href="{{ $href }}" @class([
    'transition-colors',
    'block w-full rounded-lg px-3 py-2 text-start font-semibold' => $dropdownItem,
    'rounded-lg px-2.5 py-2' => ! $dropdownItem,
    'bg-muted font-extrabold text-primary underline decoration-secondary decoration-2 underline-offset-4' => $active,
    'font-semibold text-slate-600 hover:bg-muted hover:text-primary' => ! $active,
])>{{ $slot }}</a>
