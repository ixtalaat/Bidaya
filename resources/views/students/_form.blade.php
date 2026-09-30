<div class="space-y-4">
    <div>
        <label for="name" class="mb-1 block text-sm font-medium">{{ __('students.name') }}</label>
        <input id="name" type="text" name="name" value="{{ old('name', $student->name) }}" required
               class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div>
            <label for="date_of_birth" class="mb-1 block text-sm font-medium">{{ __('students.date_of_birth') }}</label>
            <input id="date_of_birth" type="date" name="date_of_birth"
                   value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}" required
                   class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
            @error('date_of_birth')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="gender" class="mb-1 block text-sm font-medium">{{ __('students.gender') }}</label>
            <select id="gender" name="gender" required
                    class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                @foreach (\App\Enums\Gender::cases() as $option)
                    <option value="{{ $option->value }}" @selected(old('gender', $student->gender?->value) === $option->value)>{{ $option->label() }}</option>
                @endforeach
            </select>
            @error('gender')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="status" class="mb-1 block text-sm font-medium">{{ __('students.status') }}</label>
            <select id="status" name="status" required
                    class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                @foreach (\App\Enums\StudentStatus::cases() as $option)
                    <option value="{{ $option->value }}" @selected(old('status', $student->status?->value) === $option->value)>{{ $option->label() }}</option>
                @endforeach
            </select>
            @error('status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="photo" class="mb-1 block text-sm font-medium">{{ __('students.photo') }}</label>
        @if ($student->photo_path)
            <p class="mb-2 text-sm text-slate-500">{{ __('students.current_photo') }}:
                <a href="{{ Storage::url($student->photo_path) }}" target="_blank" class="text-indigo-700 hover:underline">{{ basename($student->photo_path) }}</a>
            </p>
        @endif
        <input id="photo" type="file" name="photo" accept="image/*"
               class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
        @error('photo')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label for="school" class="mb-1 block text-sm font-medium">{{ __('students.school') }}</label>
            <input id="school" type="text" name="school" value="{{ old('school', $student->school) }}"
                   class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
        </div>
        <div>
            <label for="grade_level" class="mb-1 block text-sm font-medium">{{ __('students.grade_level') }}</label>
            <input id="grade_level" type="text" name="grade_level" value="{{ old('grade_level', $student->grade_level) }}"
                   class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
        </div>
    </div>

    <div>
        <label for="address" class="mb-1 block text-sm font-medium">{{ __('students.address') }}</label>
        <input id="address" type="text" name="address" value="{{ old('address', $student->address) }}"
               class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
    </div>

    <div>
        <label for="notes" class="mb-1 block text-sm font-medium">{{ __('students.notes') }}</label>
        <textarea id="notes" name="notes" rows="3"
                  class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">{{ old('notes', $student->notes) }}</textarea>
    </div>

    <div>
        <div class="mb-2 flex items-center justify-between">
            <span class="text-sm font-medium">{{ __('students.guardians') }}</span>
            <button type="button" id="add-guardian" class="rounded bg-slate-200 px-3 py-1 text-sm hover:bg-slate-300">
                {{ __('students.add_guardian') }}
            </button>
        </div>
        <div id="guardian-links" class="space-y-2">
            @foreach (old('parents', $linkedParents) as $index => $link)
                <div class="guardian-row flex gap-2">
                    <select name="parents[{{ $index }}][id]" required
                            class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                        <option value="">{{ __('students.select_guardian') }}</option>
                        @foreach ($allGuardians as $guardian)
                            <option value="{{ $guardian->id }}" @selected((string) ($link['id'] ?? '') === (string) $guardian->id)>
                                {{ $guardian->name }} ({{ $guardian->phone }})
                            </option>
                        @endforeach
                    </select>
                    <select name="parents[{{ $index }}][relationship]" required
                            class="w-48 shrink-0 rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                        @foreach (\App\Enums\RelationshipType::cases() as $option)
                            <option value="{{ $option->value }}" @selected(($link['relationship'] ?? '') === $option->value)>{{ $option->label() }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="remove-guardian shrink-0 rounded bg-red-100 px-3 py-2 text-sm text-red-700 hover:bg-red-200">×</button>
                </div>
            @endforeach
        </div>
        @error('parents')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<template id="guardian-row-template">
    <div class="guardian-row flex gap-2">
        <select name="__NAME__[id]" required
                class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
            <option value="">{{ __('students.select_guardian') }}</option>
            @foreach ($allGuardians as $guardian)
                <option value="{{ $guardian->id }}">{{ $guardian->name }} ({{ $guardian->phone }})</option>
            @endforeach
        </select>
        <select name="__NAME__[relationship]" required
                class="w-48 shrink-0 rounded border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
            @foreach (\App\Enums\RelationshipType::cases() as $option)
                <option value="{{ $option->value }}">{{ $option->label() }}</option>
            @endforeach
        </select>
        <button type="button" class="remove-guardian shrink-0 rounded bg-red-100 px-3 py-2 text-sm text-red-700 hover:bg-red-200">×</button>
    </div>
</template>

<script>
document.getElementById('add-guardian').addEventListener('click', () => {
    const template = document.getElementById('guardian-row-template').innerHTML;
    const wrapper = document.createElement('div');
    wrapper.innerHTML = template.replaceAll('__NAME__', `parents[${Date.now()}]`);
    document.getElementById('guardian-links').appendChild(wrapper.firstElementChild);
});
document.getElementById('guardian-links').addEventListener('click', (event) => {
    if (event.target.classList.contains('remove-guardian')) {
        event.target.closest('.guardian-row').remove();
    }
});
</script>
