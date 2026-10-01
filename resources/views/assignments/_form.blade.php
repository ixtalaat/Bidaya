<div class="space-y-4">
    <div>
        <label for="title" class="mb-1 block text-sm font-medium">{{ __('assignments.name_title') }}</label>
        <input id="title" type="text" name="title" value="{{ old('title', $assignment->title) }}" required
               class="w-full rounded-xl border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
        @error('title')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="description" class="mb-1 block text-sm font-medium">{{ __('assignments.description') }}</label>
        <textarea id="description" name="description" rows="4"
                  class="w-full rounded-xl border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">{{ old('description', $assignment->description) }}</textarea>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div>
            <label for="due_date" class="mb-1 block text-sm font-medium">{{ __('assignments.due_date') }}</label>
            <input id="due_date" type="date" name="due_date" value="{{ old('due_date', $assignment->due_date?->format('Y-m-d')) }}" required
                   class="w-full rounded-xl border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
            @error('due_date')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="max_score" class="mb-1 block text-sm font-medium">{{ __('assignments.max_score') }}</label>
            <input id="max_score" type="number" name="max_score" value="{{ old('max_score', $assignment->max_score) }}" min="0.01" step="0.01" required
                   class="w-full rounded-xl border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
            @error('max_score')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="status" class="mb-1 block text-sm font-medium">{{ __('assignments.status') }}</label>
            <select id="status" name="status" required
                    class="w-full rounded-xl border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
                @foreach (\App\Enums\AssignmentStatus::cases() as $option)
                    <option value="{{ $option->value }}" @selected(old('status', $assignment->status?->value ?? \App\Enums\AssignmentStatus::Draft->value) === $option->value)>{{ $option->label() }}</option>
                @endforeach
            </select>
            @error('status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="attachment" class="mb-1 block text-sm font-medium">{{ __('assignments.attachment') }}</label>
        @if ($assignment->attachment_path)
            <p class="mb-2 text-sm text-slate-500">
                <a href="{{ Storage::url($assignment->attachment_path) }}" target="_blank" class="font-semibold text-primary hover:underline">{{ basename($assignment->attachment_path) }}</a>
            </p>
        @endif
        <input id="attachment" type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx"
               class="w-full rounded-xl border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
        @error('attachment')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
