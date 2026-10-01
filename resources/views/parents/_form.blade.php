<div class="space-y-4">
    <div>
        <label for="name" class="mb-1 block text-sm font-medium">{{ __('guardians.name') }}</label>
        <input id="name" type="text" name="name" value="{{ old('name', $guardian->name) }}" required
               class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label for="phone" class="mb-1 block text-sm font-medium">{{ __('guardians.phone') }}</label>
            <input id="phone" type="text" name="phone" value="{{ old('phone', $guardian->phone) }}" required
                   class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
            @error('phone')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="email" class="mb-1 block text-sm font-medium">{{ __('guardians.email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email', $guardian->email) }}"
                   class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="address" class="mb-1 block text-sm font-medium">{{ __('guardians.address') }}</label>
        <input id="address" type="text" name="address" value="{{ old('address', $guardian->address) }}"
               class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
        @error('address')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="emergency_contact" class="mb-1 block text-sm font-medium">{{ __('guardians.emergency_contact') }}</label>
        <input id="emergency_contact" type="text" name="emergency_contact" value="{{ old('emergency_contact', $guardian->emergency_contact) }}"
               class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
        @error('emergency_contact')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="user_id" class="mb-1 block text-sm font-medium">{{ __('guardians.user_account') }}</label>
        <select id="user_id" name="user_id"
                class="w-full rounded border-2 border-slate-900 px-3 py-2 text-sm focus:border-primary focus:outline-none">
            <option value="">{{ __('guardians.no_account') }}</option>
            @foreach ($parentUsers as $parentUser)
                <option value="{{ $parentUser->id }}" @selected((string) old('user_id', $guardian->user_id) === (string) $parentUser->id)>
                    {{ $parentUser->name }} ({{ $parentUser->email }})
                </option>
            @endforeach
        </select>
        @error('user_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
