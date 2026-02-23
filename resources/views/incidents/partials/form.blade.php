@php
    $incident = $incident ?? null;
    $useOld = $useOld ?? true;
@endphp

<div class="space-y-5">
    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <x-input-label for="employee_id" value="Selected employee" />
            <div x-data="searchableEmployeeSelect({{ json_encode($employees->map(fn($e) => ['id' => $e->id, 'name' => $e->first_name . ' ' . $e->last_name, 'employee_no' => $e->employee_no])->values()->toArray()) }}, '{{ $useOld ? old('employee_id', $incident?->employee_id) : $incident?->employee_id }}')" class="relative mt-1">
                <input 
                    type="text"
                    x-model="searchQuery"
                    @input="filterEmployees()"
                    @focus="showDropdown = true"
                    @click="showDropdown = true"
                    placeholder="Search or select employee"
                    class="block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500"
                />
                <input type="hidden" id="employee_id" name="employee_id" :value="selectedEmployeeId" />
                
                <div 
                    x-show="showDropdown"
                    @click.outside="showDropdown = false"
                    class="absolute top-full left-0 right-0 z-10 mt-1 max-h-60 overflow-y-auto rounded-md border border-slate-300 bg-white shadow-lg">
                    <template x-if="filteredEmployees.length === 0">
                        <div class="px-4 py-3 text-sm text-slate-500">No employees found</div>
                    </template>
                    <template x-if="filteredEmployees.length > 0">
                        <template x-for="employee in filteredEmployees" :key="employee.id">
                            <button
                                type="button"
                                @click="selectEmployee(employee); showDropdown = false"
                                :class="selectedEmployeeId === employee.id ? 'bg-slate-100' : 'hover:bg-slate-50'"
                                class="w-full px-4 py-3 text-left text-sm transition">
                                <span class="font-medium text-slate-900" x-text="employee.name"></span>
                                <span class="text-slate-500" x-text="'(' + employee.employee_no + ')'"></span>
                            </button>
                        </template>
                    </template>
                </div>
            </div>
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

<script>
    function searchableEmployeeSelect(employees, initialId) {
        return {
            employees: employees,
            filteredEmployees: employees,
            searchQuery: '',
            showDropdown: false,
            selectedEmployeeId: initialId ? parseInt(initialId) : null,
            
            filterEmployees() {
                const query = this.searchQuery.toLowerCase().trim();
                
                if (!query) {
                    this.filteredEmployees = this.employees;
                } else {
                    this.filteredEmployees = this.employees.filter(emp => 
                        emp.name.toLowerCase().includes(query) || 
                        emp.employee_no.toLowerCase().includes(query)
                    );
                }
            },
            
            selectEmployee(employee) {
                this.selectedEmployeeId = employee.id;
                this.searchQuery = employee.name + ' (' + employee.employee_no + ')';
                this.showDropdown = false;
            }
        };
    }
</script>
