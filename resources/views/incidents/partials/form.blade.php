@php
    $incident = $incident ?? null;
    $useOld = $useOld ?? true;
@endphp

<div class="space-y-5">
    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <x-input-label for="employee_id" value="Selected employee" />
            <select id="employee_id" name="employee_id" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500" required>
                <option value="">Select employee</option>
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}" @selected(($useOld ? old('employee_id', $incident?->employee_id) : $incident?->employee_id) == $employee->id)>
                        {{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->employee_no }})
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('employee_id')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="incident_date" value="Date of incident" />
            <x-text-input id="incident_date" name="incident_date" type="date" class="mt-1 block w-full" value="{{ $useOld ? old('incident_date', $incident?->incident_date?->format('Y-m-d')) : $incident?->incident_date?->format('Y-m-d') }}" required />
            <x-input-error :messages="$errors->get('incident_date')" class="mt-2" />
        </div>
    </div>

    <div>
        <x-input-label for="description" value="Description of the incident" />
        <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500" required>{{ $useOld ? old('description', $incident?->description) : ($incident?->description ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="attachment" value="Pictures or video (up to 100 MB)" />
        <input id="attachment" name="attachment" type="file" data-max-bytes="104857600" data-max-size-label="100 MB" class="mt-1 block w-full rounded-lg border border-slate-300 text-sm text-slate-700 file:mr-4 file:rounded-md file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-slate-800" />
        @if ($incident?->attachment_path)
            <p class="mt-2 text-xs text-slate-500">Current file: <a href="{{ asset('storage/' . $incident->attachment_path) }}" class="text-slate-700 underline" target="_blank" rel="noreferrer">Download</a></p>
        @endif
        <p class="mt-1 text-xs text-slate-500">Accepted: JPG, PNG, WEBP, MP4, MOV, AVI, WMV. Max 100 MB.</p>
        <p class="mt-2 hidden text-xs text-rose-600" data-upload-size-error></p>
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
        <x-input-error :messages="$errors->get('attachment')" class="mt-2" />
    </div>
</div>
