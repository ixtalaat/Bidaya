<div class="space-y-4">
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label for="name" class="mb-1 block text-sm font-medium">{{ __('teachers.name') }}</label>
            <input id="name" type="text" name="name" value="{{ old('name', $teacher->user->name ?? null) }}" required
                   class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="email" class="mb-1 block text-sm font-medium">{{ __('teachers.email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email', $teacher->user->email ?? null) }}" required
                   class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label for="password" class="mb-1 block text-sm font-medium">{{ __('teachers.password') }}</label>
            <input id="password" type="password" name="password" @if (!$teacher->exists) required @endif
                   class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
            @if ($teacher->exists)
                <p class="mt-1 text-xs text-slate-500">{{ __('teachers.password_hint') }}</p>
            @endif
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="password_confirmation" class="mb-1 block text-sm font-medium">{{ __('teachers.password_confirmation') }}</label>
            <input id="password_confirmation" type="password" name="password_confirmation" @if (!$teacher->exists) required @endif
                   class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div>
            <label for="specialization" class="mb-1 block text-sm font-medium">{{ __('teachers.specialization') }}</label>
            <input id="specialization" type="text" name="specialization" value="{{ old('specialization', $teacher->specialization) }}"
                   class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
        </div>
        <div>
            <label for="hire_date" class="mb-1 block text-sm font-medium">{{ __('teachers.hire_date') }}</label>
            <input id="hire_date" type="date" name="hire_date" value="{{ old('hire_date', $teacher->hire_date?->format('Y-m-d')) }}"
                   class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
            @error('hire_date')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="status" class="mb-1 block text-sm font-medium">{{ __('teachers.status') }}</label>
            <select id="status" name="status" required
                    class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                @foreach (\App\Enums\TeacherStatus::cases() as $option)
                    <option value="{{ $option->value }}" @selected(old('status', $teacher->status?->value) === $option->value)>{{ $option->label() }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div>
        <label for="qualification" class="mb-1 block text-sm font-medium">{{ __('teachers.qualification') }}</label>
        <input id="qualification" type="text" name="qualification" value="{{ old('qualification', $teacher->qualification) }}"
               class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
    </div>

    <div>
        <label for="notes" class="mb-1 block text-sm font-medium">{{ __('teachers.notes') }}</label>
        <textarea id="notes" name="notes" rows="3"
                  class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">{{ old('notes', $teacher->notes) }}</textarea>
    </div>
</div>
