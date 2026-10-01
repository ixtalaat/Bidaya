<div class="space-y-4">
    <div>
        <label for="name" class="mb-1 block text-sm font-medium">{{ __('assessments.name') }}</label>
        <input id="name" type="text" name="name" value="{{ old('name', $assessment->name) }}" required
               class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div>
            <label for="type" class="mb-1 block text-sm font-medium">{{ __('assessments.type') }}</label>
            <select id="type" name="type" required
                    class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                @foreach (\App\Enums\AssessmentType::cases() as $option)
                    <option value="{{ $option->value }}" @selected(old('type', $assessment->type?->value) === $option->value)>{{ $option->label() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="max_score" class="mb-1 block text-sm font-medium">{{ __('assessments.max_score') }}</label>
            <input id="max_score" type="number" name="max_score" value="{{ old('max_score', $assessment->max_score) }}" min="0.01" step="0.01" required
                   class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
            @error('max_score')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="assessment_date" class="mb-1 block text-sm font-medium">{{ __('assessments.assessment_date') }}</label>
            <input id="assessment_date" type="date" name="assessment_date" value="{{ old('assessment_date', $assessment->assessment_date?->format('Y-m-d')) }}" required
                   class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
        </div>
    </div>

    <div>
        <label for="description" class="mb-1 block text-sm font-medium">{{ __('assessments.description') }}</label>
        <textarea id="description" name="description" rows="3"
                  class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">{{ old('description', $assessment->description) }}</textarea>
    </div>
</div>
