<div class="space-y-4">
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label for="course_id" class="mb-1 block text-sm font-medium">{{ __('groups.course') }}</label>
            <select id="course_id" name="course_id" required
                    class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
                @foreach ($allCourses as $course)
                    <option value="{{ $course->id }}" @selected((string) old('course_id', $group->course_id) === (string) $course->id)>
                        {{ $course->name }}
                    </option>
                @endforeach
            </select>
            @error('course_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="teacher_id" class="mb-1 block text-sm font-medium">{{ __('groups.teacher') }}</label>
            <select id="teacher_id" name="teacher_id" required
                    class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
                @foreach ($allTeachers as $teacher)
                    <option value="{{ $teacher->id }}" @selected((string) old('teacher_id', $group->teacher_id) === (string) $teacher->id)>
                        {{ $teacher->user->name }}{{ $teacher->specialization ? ' — '.$teacher->specialization : '' }}
                    </option>
                @endforeach
            </select>
            @error('teacher_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div>
            <label for="name" class="mb-1 block text-sm font-medium">{{ __('groups.name') }}</label>
            <input id="name" type="text" name="name" value="{{ old('name', $group->name) }}" required
                   class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="capacity" class="mb-1 block text-sm font-medium">{{ __('groups.capacity') }}</label>
            <input id="capacity" type="number" name="capacity" value="{{ old('capacity', $group->capacity) }}" min="1" max="200" required
                   class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
            @error('capacity')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="classroom" class="mb-1 block text-sm font-medium">{{ __('groups.classroom') }}</label>
            <input id="classroom" type="text" name="classroom" value="{{ old('classroom', $group->classroom) }}"
                   class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div>
            <label for="start_date" class="mb-1 block text-sm font-medium">{{ __('groups.start_date') }}</label>
            <input id="start_date" type="date" name="start_date" value="{{ old('start_date', $group->start_date?->format('Y-m-d')) }}" required
                   class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
            @error('start_date')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="end_date" class="mb-1 block text-sm font-medium">{{ __('groups.end_date') }}</label>
            <input id="end_date" type="date" name="end_date" value="{{ old('end_date', $group->end_date?->format('Y-m-d')) }}"
                   class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
            @error('end_date')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="status" class="mb-1 block text-sm font-medium">{{ __('groups.status') }}</label>
            <select id="status" name="status" required
                    class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
                @foreach (\App\Enums\GroupStatus::cases() as $option)
                    <option value="{{ $option->value }}" @selected(old('status', $group->status?->value) === $option->value)>{{ $option->label() }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
