@php
    $employee = $employee ?? null;
@endphp

<div class="grid gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <x-input-label for="photo" :value="__('Employee photo')" />
        <div class="mt-2 flex items-center gap-4">
            <div class="h-16 w-16 overflow-hidden rounded-full border border-slate-200 bg-slate-100">
                @if ($employee?->photo_path)
                    <img src="{{ asset('storage/' . $employee->photo_path) }}" alt="{{ $employee->first_name }} {{ $employee->last_name }}" class="h-full w-full object-cover">
                @else
                    <div class="flex h-full w-full items-center justify-center text-xs font-semibold text-slate-500">N/A</div>
                @endif
            </div>
            <div class="flex-1">
                <input id="photo" name="photo" type="file" accept="image/*" class="block w-full rounded-lg border border-slate-300 text-sm text-slate-700 file:mr-4 file:rounded-md file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-slate-800" />
                <p class="mt-1 text-xs text-slate-500">PNG or JPG up to 2 MB.</p>
                <div class="mt-3 hidden" data-upload-progress aria-hidden="true">
                    <div class="flex items-center justify-between text-xs text-slate-500">
                        <span>Uploading...</span>
                        <span data-upload-progress-text>0%</span>
                    </div>
                    <div class="mt-2 h-5 w-full overflow-hidden rounded-full bg-slate-200">
                        <div class="flex h-full w-0 items-center justify-center bg-emerald-600 text-[10px] font-semibold text-white" data-upload-progress-bar aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" role="progressbar">
                            <span data-upload-progress-bar-text>0%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <x-input-error :messages="$errors->get('photo')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="employee_no" :value="__('Employee ID')" />
        <x-text-input id="employee_no" name="employee_no" type="text" class="mt-1 block w-full" value="{{ old('employee_no', $employee?->employee_no) }}" required />
        <x-input-error :messages="$errors->get('employee_no')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="status" :value="__('Status')" />
        <select id="status" name="status" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            @foreach (['active' => 'Active', 'inactive' => 'Inactive'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $employee?->status ?? 'active') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('status')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="first_name" :value="__('First name')" />
        <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" value="{{ old('first_name', $employee?->first_name) }}" required />
        <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="last_name" :value="__('Last name')" />
        <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" value="{{ old('last_name', $employee?->last_name) }}" required />
        <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="department" :value="__('Department')" />
        <x-text-input id="department" name="department" type="text" class="mt-1 block w-full" value="{{ old('department', $employee?->department) }}" required />
        <x-input-error :messages="$errors->get('department')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="section" :value="__('Section')" />
        <x-text-input id="section" name="section" type="text" class="mt-1 block w-full" value="{{ old('section', $employee?->section) }}" required />
        <x-input-error :messages="$errors->get('section')" class="mt-2" />
    </div>
</div>
