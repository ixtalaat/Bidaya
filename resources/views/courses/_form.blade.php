<div class="space-y-4">
    <div>
        <label for="name" class="mb-1 block text-sm font-medium">{{ __('courses.name') }}</label>
        <input id="name" type="text" name="name" value="{{ old('name', $course->name) }}" required
               class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="description" class="mb-1 block text-sm font-medium">{{ __('courses.description') }}</label>
        <textarea id="description" name="description" rows="3"
                  class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">{{ old('description', $course->description) }}</textarea>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div>
            <label for="category" class="mb-1 block text-sm font-medium">{{ __('courses.category') }}</label>
            <input id="category" type="text" name="category" value="{{ old('category', $course->category) }}"
                   class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
        </div>
        <div>
            <label for="level" class="mb-1 block text-sm font-medium">{{ __('courses.level') }}</label>
            <input id="level" type="text" name="level" value="{{ old('level', $course->level) }}"
                   class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
        </div>
        <div>
            <label for="status" class="mb-1 block text-sm font-medium">{{ __('courses.status') }}</label>
            <select id="status" name="status" required
                    class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
                @foreach (\App\Enums\CourseStatus::cases() as $option)
                    <option value="{{ $option->value }}" @selected(old('status', $course->status?->value) === $option->value)>{{ $option->label() }}</option>
                @endforeach
            </select>
            @error('status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <div>
            <label for="min_age" class="mb-1 block text-sm font-medium">{{ __('courses.min_age') }}</label>
            <input id="min_age" type="number" name="min_age" value="{{ old('min_age', $course->min_age) }}" min="0" max="100"
                   class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
        </div>
        <div>
            <label for="max_age" class="mb-1 block text-sm font-medium">{{ __('courses.max_age') }}</label>
            <input id="max_age" type="number" name="max_age" value="{{ old('max_age', $course->max_age) }}" min="0" max="100"
                   class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
            @error('max_age')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="duration_weeks" class="mb-1 block text-sm font-medium">{{ __('courses.duration_weeks') }}</label>
            <input id="duration_weeks" type="number" name="duration_weeks" value="{{ old('duration_weeks', $course->duration_weeks) }}" min="1"
                   class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
        </div>
        <div>
            <label for="sessions_count" class="mb-1 block text-sm font-medium">{{ __('courses.sessions_count') }}</label>
            <input id="sessions_count" type="number" name="sessions_count" value="{{ old('sessions_count', $course->sessions_count) }}" min="1"
                   class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label for="monthly_fee" class="mb-1 block text-sm font-medium">{{ __('courses.monthly_fee') }}</label>
            <input id="monthly_fee" type="number" name="monthly_fee" value="{{ old('monthly_fee', $course->monthly_fee) }}" min="0" step="0.01" required
                   class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
            @error('monthly_fee')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="prerequisites" class="mb-1 block text-sm font-medium">{{ __('courses.prerequisites') }}</label>
            <input id="prerequisites" type="text" name="prerequisites" value="{{ old('prerequisites', $course->prerequisites) }}"
                   class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
        </div>
    </div>
</div>
