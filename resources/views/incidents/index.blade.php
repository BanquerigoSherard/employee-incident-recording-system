<x-app-layout>
    @php($pageTitle = 'Incident Reports')
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900">Incident reports</h2>
                <p class="text-sm text-slate-500">Submit, update, and review incident reports.</p>
            </div>
            <button x-data @click="$store.incidentManager.openCreateModal()" class="inline-flex items-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-slate-800">
                Add report
            </button>
        </div>
    </x-slot>

    <div class="py-8" x-data>
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <form method="GET" action="{{ route('incidents.index') }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                <div class="flex flex-wrap items-end gap-3">
                    <div class="min-w-[220px] flex-1">
                        <x-input-label for="search" value="Search" class="sr-only" />
                        <x-text-input id="search" name="search" type="text" class="block w-full" placeholder="Search description, name, ID, department" value="{{ $search }}" />
                    </div>
                    <div class="min-w-[200px]">
                        <x-input-label for="employee_filter" value="Employee" class="sr-only" />
                        <select id="employee_filter" name="employee_id" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                            <option value="">All employees</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" @selected($employeeFilter == $employee->id)>
                                    {{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->employee_no }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-[160px]">
                        <x-input-label for="date_from" value="From" class="sr-only" />
                        <x-text-input id="date_from" name="date_from" type="date" class="block w-full" value="{{ $dateFrom }}" />
                    </div>
                    <div class="min-w-[160px]">
                        <x-input-label for="date_to" value="To" class="sr-only" />
                        <x-text-input id="date_to" name="date_to" type="date" class="block w-full" value="{{ $dateTo }}" />
                    </div>
                    <div class="flex items-center gap-2">
                        <x-primary-button class="px-4">Filter</x-primary-button>
                        @if ($search || $employeeFilter || $dateFrom || $dateTo)
                            <a href="{{ route('incidents.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-800">Clear</a>
                        @endif
                    </div>
                </div>
            </form>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">Employee</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">Incident</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">Date</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-widest text-slate-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse ($incidents as $incident)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-semibold text-slate-900">
                                            {{ $incident->employee?->first_name }} {{ $incident->employee?->last_name }}
                                        </div>
                                        <div class="text-xs text-slate-500">{{ $incident->employee?->employee_no }} • {{ $incident->employee?->department }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600">
                                        <div class="font-medium text-slate-900 line-clamp-2">{{ $incident->description }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600">
                                        <div class="text-sm text-slate-700">{{ $incident->incident_date->format('M d, Y') }}</div>
                                        <div class="text-xs text-slate-500">Logged {{ $incident->created_at->format('M d, Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm">
                                        <div class="inline-flex items-center gap-3">
                                            <a href="{{ route('incidents.show', $incident) }}" class="text-indigo-600 hover:text-indigo-500">View</a>
                                            @if (auth()->user()->id === 1)
                                                <button @click="$store.incidentManager.openEditModal({
                                                    id: {{ $incident->id }},
                                                    employee_id: {{ $incident->employee_id }},
                                                    incident_date: '{{ $incident->incident_date->format('Y-m-d') }}',
                                                    description: @js($incident->description),
                                                    attachment_url: '{{ $incident->attachment_path ? asset('storage/' . $incident->attachment_path) : '' }}'
                                                })" class="text-slate-600 hover:text-slate-800">Edit</button>
                                                <button @click="$store.incidentManager.openDeleteModal({
                                                    id: {{ $incident->id }},
                                                    incident_summary: @js(\Illuminate\Support\Str::limit($incident->description, 60)),
                                                    employee_name: '{{ $incident->employee?->first_name }} {{ $incident->employee?->last_name }}'
                                                })" class="text-rose-600 hover:text-rose-500">Delete</button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-500">
                                        No incident reports yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-200 px-6 py-4">
                    {{ $incidents->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div x-data x-show="$store.incidentManager.showCreateModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" data-upload-modal>
        <div class="flex min-h-screen items-center justify-center bg-slate-900/50 px-4 py-6">
            <div class="w-full max-w-4xl rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">New incident report</h3>
                        <p class="text-xs text-slate-500">Complete all required details before submitting.</p>
                    </div>
                    <button @click="$store.incidentManager.closeCreateModal()" class="rounded-full p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600" data-upload-close>
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="incident-create-form" method="POST" action="{{ route('incidents.store') }}" enctype="multipart/form-data" class="space-y-6 px-6 py-5" data-upload-form @if (!($openCreateOnError ?? false)) data-reset-on-load @endif>
                    @csrf
                    <input type="hidden" name="form_context" value="create" />

                    @include('incidents.partials.form', ['useOld' => ($openCreateOnError ?? false), 'incident' => null])

                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-6">
                        <button type="button" @click="$store.incidentManager.closeCreateModal()" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50" data-upload-cancel>Cancel</button>
                        <x-primary-button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800" data-upload-submit>
                            <span data-upload-submit-text>Submit report</span>
                            <span data-upload-spinner class="ml-2 hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white" aria-hidden="true"></span>
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-data x-show="$store.incidentManager.showEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" data-upload-modal>
        <div class="flex min-h-screen items-center justify-center bg-slate-900/50 px-4 py-6">
            <div class="relative w-full max-w-4xl rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200">
                <button @click="$store.incidentManager.closeEditModal()" class="absolute right-4 top-4 rounded-full p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600" data-upload-close>
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <form method="POST" :action="`{{ route('incidents.update', '') }}/${$store.incidentManager.currentIncident.id}`" enctype="multipart/form-data" class="space-y-6 px-6 py-6" data-upload-form>
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="form_context" value="edit" />
                    <input type="hidden" name="incident_id" :value="$store.incidentManager.currentIncident.id" />

                    <div class="space-y-5">
                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <x-input-label for="edit_employee_id" value="Selected employee" />
                                <div x-data="editSearchableEmployeeSelect({{ json_encode($employees->map(fn($e) => ['id' => $e->id, 'name' => $e->first_name . ' ' . $e->last_name, 'employee_no' => $e->employee_no])->values()->toArray()) }})" class="relative mt-1">
                                    <input 
                                        type="text"
                                        x-model="searchQuery"
                                        @input="filterEmployees()"
                                        @focus="showDropdown = true"
                                        @click="showDropdown = true"
                                        placeholder="Search or select employee"
                                        class="block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500"
                                    />
                                    
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
                                                    :class="$store.incidentManager.currentIncident.employee_id == employee.id ? 'bg-slate-100' : 'hover:bg-slate-50'"
                                                    class="w-full px-4 py-3 text-left text-sm transition">
                                                    <span class="font-medium text-slate-900" x-text="employee.name"></span>
                                                    <span class="text-slate-500" x-text="'(' + employee.employee_no + ')'"></span>
                                                </button>
                                            </template>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <x-input-label for="edit_incident_date" value="Date of incident" />
                                <x-text-input id="edit_incident_date" name="incident_date" type="date" x-model="$store.incidentManager.currentIncident.incident_date" class="mt-1 block w-full" required />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="edit_description" value="Description of the incident" />
                            <textarea id="edit_description" name="description" rows="4" x-model="$store.incidentManager.currentIncident.description" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500" required></textarea>
                        </div>

                        <div>
                            <x-input-label for="edit_attachment" value="Pictures or video (up to 100 MB)" />
                            <input id="edit_attachment" name="attachment" type="file" data-max-bytes="104857600" data-max-size-label="100 MB" class="mt-1 block w-full rounded-lg border border-slate-300 text-sm text-slate-700 file:mr-4 file:rounded-md file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-slate-800" />
                            <template x-if="$store.incidentManager.currentIncident.attachment_url">
                                <p class="mt-2 text-xs text-slate-500">
                                    Current file: <a :href="$store.incidentManager.currentIncident.attachment_url" class="text-slate-700 underline" target="_blank" rel="noreferrer">Download</a>
                                </p>
                            </template>
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
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="admin_password" value="Your password (required to update)" />
                            <x-text-input id="admin_password" name="admin_password" type="password" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('admin_password')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-6">
                        <button type="button" @click="$store.incidentManager.closeEditModal()" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50" data-upload-cancel>Cancel</button>
                        <x-primary-button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800" data-upload-submit>
                            <span data-upload-submit-text>Save changes</span>
                            <span data-upload-spinner class="ml-2 hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white" aria-hidden="true"></span>
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div x-data x-show="$store.incidentManager.showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex min-h-screen items-center justify-center bg-slate-900/50 px-4 py-6">
            <div @click.outside="$store.incidentManager.closeDeleteModal()" class="w-full max-w-lg rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200">
                <div class="flex items-center gap-4 border-b border-slate-200 px-6 py-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-rose-100">
                        <svg class="h-6 w-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 0v2m0-6h.01M7.21 15.89A10 10 0 1 1 15.89 7.21" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Delete incident report</h3>
                        <p class="text-xs text-slate-500">This action cannot be undone.</p>
                    </div>
                </div>
                <div class="px-6 py-5 space-y-3">
                    <p class="text-sm text-slate-600">Are you sure you want to delete <span class="font-medium" x-text="$store.incidentManager.currentIncident.incident_summary"></span> for <span class="font-medium" x-text="$store.incidentManager.currentIncident.employee_name"></span>?</p>
                    <div>
                        <x-input-label for="delete_admin_password" value="Your password" />
                        <x-text-input id="delete_admin_password" name="admin_password" type="password" form="delete-incident-form" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('admin_password')" class="mt-2" />
                    </div>
                </div>

                <form id="delete-incident-form" method="POST" :action="`{{ route('incidents.destroy', '') }}/${$store.incidentManager.currentIncident.id}`" class="border-t border-slate-200 px-6 py-4">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="form_context" value="delete" />
                    <input type="hidden" name="incident_id" :value="$store.incidentManager.currentIncident.id" />
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="$store.incidentManager.closeDeleteModal()" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-700">Delete report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('incidentManager', {
                showCreateModal: @json($openCreateOnError ?? false),
                showEditModal: @json($openEditOnError ?? false),
                showDeleteModal: @json($openDeleteOnError ?? false),
                currentIncident: @json($currentIncidentPayload ?? []),

                openCreateModal() {
                    if (window.resetFormById && !@json($openCreateOnError ?? false)) {
                        window.resetFormById('incident-create-form');
                    }
                    this.showCreateModal = true;
                },
                closeCreateModal() {
                    if (window.resetFormById) {
                        window.resetFormById('incident-create-form');
                    }
                    this.showCreateModal = false;
                },
                openEditModal(incident) {
                    this.currentIncident = incident;
                    this.showEditModal = true;
                },
                closeEditModal() {
                    this.showEditModal = false;
                },
                openDeleteModal(incident) {
                    this.currentIncident = incident;
                    this.showDeleteModal = true;
                },
                closeDeleteModal() {
                    this.showDeleteModal = false;
                }
            });
        });

        function editSearchableEmployeeSelect(employees) {
            return {
                employees: employees,
                filteredEmployees: employees,
                searchQuery: '',
                showDropdown: false,
                
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
                    this.$store.incidentManager.currentIncident.employee_id = employee.id;
                    this.searchQuery = employee.name + ' (' + employee.employee_no + ')';
                    this.showDropdown = false;
                }
            };
        }
    </script>
</x-app-layout>
