<x-app-layout>
    @php($pageTitle = 'Employees')
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900">Employees</h2>
                <p class="text-sm text-slate-500">Create, update, and manage employee profiles.</p>
            </div>
            <button x-data @click="$store.employeeManager.openCreateModal()" class="inline-flex items-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-slate-800">
                Add employee
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

            <form method="GET" action="{{ route('employees.index') }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                <div class="flex flex-wrap items-end gap-3">
                    <div class="min-w-[220px] flex-1">
                        <x-input-label for="search" value="Search" class="sr-only" />
                        <x-text-input id="search" name="search" type="text" class="block w-full" placeholder="Search name, ID, department, section" value="{{ $search }}" />
                    </div>
                    <div class="min-w-[180px]">
                        <x-input-label for="department_filter" value="Department" class="sr-only" />
                        <select id="department_filter" name="department" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                            <option value="">All departments</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept }}" @selected($department === $dept)>{{ $dept }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-[140px]">
                        <x-input-label for="status_filter" value="Status" class="sr-only" />
                        <select id="status_filter" name="status" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                            <option value="">All status</option>
                            <option value="active" @selected($status === 'active')>Active</option>
                            <option value="inactive" @selected($status === 'inactive')>Inactive</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-primary-button class="px-4">Filter</x-primary-button>
                        @if ($search || $department || $status)
                            <a href="{{ route('employees.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-800">Clear</a>
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
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">Department</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">Section</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-widest text-slate-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse ($employees as $employee)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 overflow-hidden rounded-full border border-slate-200 bg-slate-100">
                                                @if ($employee->photo_path)
                                                    <img src="{{ asset('storage/' . $employee->photo_path) }}" alt="{{ $employee->first_name }} {{ $employee->last_name }}" class="h-full w-full object-cover">
                                                @else
                                                    <div class="flex h-full w-full items-center justify-center text-xs font-semibold text-slate-500">
                                                        {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold text-slate-900">
                                                    {{ $employee->first_name }} {{ $employee->last_name }}
                                                </div>
                                                <div class="text-xs text-slate-500">{{ $employee->employee_no }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600">{{ $employee->department }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-600">{{ $employee->section }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium {{ $employee->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                            {{ ucfirst($employee->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm">
                                        <div class="inline-flex items-center gap-3">
                                            <a href="{{ route('employees.show', $employee) }}" class="text-indigo-600 hover:text-indigo-500">View</a>
                                            @if (auth()->user()->name === 'Allen Tamang')
                                                <button @click="$store.employeeManager.openEditModal({
                                                    id: {{ $employee->id }},
                                                    employee_no: '{{ $employee->employee_no }}',
                                                    first_name: '{{ $employee->first_name }}',
                                                    last_name: '{{ $employee->last_name }}',
                                                    department: '{{ $employee->department }}',
                                                    section: '{{ $employee->section }}',
                                                    status: '{{ $employee->status }}',
                                                    photo_url: '{{ $employee->photo_path ? asset('storage/' . $employee->photo_path) : '' }}'
                                                })" class="text-slate-600 hover:text-slate-800">Edit</button>
                                                <button @click="$store.employeeManager.openDeleteModal({
                                                    id: {{ $employee->id }},
                                                    first_name: '{{ $employee->first_name }}',
                                                    last_name: '{{ $employee->last_name }}'
                                                })" class="text-rose-600 hover:text-rose-500">Delete</button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-500">
                                        No employees found. Add your first employee record.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-200 px-6 py-4">
                    {{ $employees->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div x-data x-show="$store.employeeManager.showCreateModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex min-h-screen items-center justify-center bg-slate-900/50 px-4 py-6">
            <div @click.outside="$store.employeeManager.closeCreateModal()" class="w-full max-w-2xl rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Add New Employee</h3>
                        <p class="text-xs text-slate-500">Create a complete employee profile.</p>
                    </div>
                    <button @click="$store.employeeManager.closeCreateModal()" class="rounded-full p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <form method="POST" action="{{ route('employees.store') }}" enctype="multipart/form-data" class="space-y-5 px-6 py-5" data-upload-form>
                    @csrf
                    <div>
                        <x-input-label for="employee_no" value="Employee No" />
                        <x-text-input id="employee_no" type="text" name="employee_no" class="mt-1 block w-full" required />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="first_name" value="First Name" />
                            <x-text-input id="first_name" type="text" name="first_name" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <x-input-label for="last_name" value="Last Name" />
                            <x-text-input id="last_name" type="text" name="last_name" class="mt-1 block w-full" required />
                        </div>
                    </div>
                    <div>
                        <x-input-label for="department" value="Department" />
                        <x-text-input id="department" type="text" name="department" class="mt-1 block w-full" required />
                    </div>
                    <div>
                        <x-input-label for="section" value="Section" />
                        <x-text-input id="section" type="text" name="section" class="mt-1 block w-full" required />
                    </div>
                    <div>
                        <x-input-label for="status" value="Status" />
                        <select name="status" id="status" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="photo" value="Employee Photo" />
                        <input id="photo" name="photo" type="file" accept="image/*" class="mt-1 block w-full rounded-lg border border-slate-300 text-sm text-slate-700 file:mr-4 file:rounded-md file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-slate-800" />
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
                    <div class="flex justify-end gap-3 border-t border-slate-200 pt-5">
                        <button type="button" @click="$store.employeeManager.closeCreateModal()" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Cancel</button>
                        <x-primary-button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Save Employee</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-data x-show="$store.employeeManager.showEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex min-h-screen items-center justify-center bg-slate-900/50 px-4 py-6">
            <div @click.outside="$store.employeeManager.closeEditModal()" class="relative w-full max-w-2xl rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200">
                <button @click="$store.employeeManager.closeEditModal()" class="absolute right-4 top-4 rounded-full p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <form method="POST" :action="`{{ route('employees.update', '') }}/${$store.employeeManager.currentEmployee.id}`" enctype="multipart/form-data" class="space-y-4 px-5 py-5" data-upload-form>
                    @csrf
                    @method('PUT')
                    <div class="sm:col-span-2">
                        <x-input-label for="edit_photo" value="Employee photo" />
                        <div class="mt-2 flex items-center gap-3">
                            <div class="h-12 w-12 overflow-hidden rounded-full border border-slate-200 bg-slate-100">
                                <img x-show="$store.employeeManager.currentEmployee.photo_url" :src="$store.employeeManager.currentEmployee.photo_url" alt="Employee photo" class="h-full w-full object-cover" />
                                <div x-show="!$store.employeeManager.currentEmployee.photo_url" class="flex h-full w-full items-center justify-center text-xs font-semibold text-slate-500">N/A</div>
                            </div>
                            <div class="flex-1">
                                <input id="edit_photo" name="photo" type="file" accept="image/*" class="block w-full rounded-lg border border-slate-300 text-sm text-slate-700 file:mr-4 file:rounded-md file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-slate-800" />
                                <p class="mt-1 text-xs text-slate-500">Upload a new photo to replace the current one.</p>
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
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="edit_employee_no" value="Employee ID" />
                            <x-text-input id="edit_employee_no" type="text" name="employee_no" x-model="$store.employeeManager.currentEmployee.employee_no" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <x-input-label for="edit_status" value="Status" />
                            <select name="status" id="edit_status" x-model="$store.employeeManager.currentEmployee.status" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="edit_first_name" value="First Name" />
                            <x-text-input id="edit_first_name" type="text" name="first_name" x-model="$store.employeeManager.currentEmployee.first_name" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <x-input-label for="edit_last_name" value="Last Name" />
                            <x-text-input id="edit_last_name" type="text" name="last_name" x-model="$store.employeeManager.currentEmployee.last_name" class="mt-1 block w-full" required />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="edit_department" value="Department" />
                            <x-text-input id="edit_department" type="text" name="department" x-model="$store.employeeManager.currentEmployee.department" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <x-input-label for="edit_section" value="Section" />
                            <x-text-input id="edit_section" type="text" name="section" x-model="$store.employeeManager.currentEmployee.section" class="mt-1 block w-full" required />
                        </div>
                    </div>
                    <div>
                        <x-input-label for="edit_admin_password" value="Your password (required to update)" />
                        <x-text-input id="edit_admin_password" name="admin_password" type="password" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('admin_password')" class="mt-2" />
                    </div>
                    <div class="flex justify-end gap-3 border-t border-slate-200 pt-4">
                        <button type="button" @click="$store.employeeManager.closeEditModal()" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50" data-upload-cancel>Cancel</button>
                        <x-primary-button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800" data-upload-submit>
                            <span data-upload-submit-text>Update Employee</span>
                            <span data-upload-spinner class="ml-2 hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white" aria-hidden="true"></span>
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div x-data x-show="$store.employeeManager.showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex min-h-screen items-center justify-center bg-slate-900/50 px-4 py-6">
            <div @click.outside="$store.employeeManager.closeDeleteModal()" class="w-full max-w-lg rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200">
                <div class="flex items-center gap-4 border-b border-slate-200 px-6 py-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-rose-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 0v2m0-6h.01M7.21 15.89A10 10 0 1 1 15.89 7.21" />
                    </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Delete Employee</h3>
                        <p class="text-xs text-slate-500">This action cannot be undone.</p>
                    </div>
                </div>
                <div class="px-6 py-5">
                    <p class="text-sm text-slate-600">Are you sure you want to delete <span x-text="$store.employeeManager.currentEmployee.first_name + ' ' + $store.employeeManager.currentEmployee.last_name" class="font-medium"></span>?</p>
                </div>
                
                <form method="POST" :action="`{{ route('employees.destroy', '') }}/${$store.employeeManager.currentEmployee.id}`" class="border-t border-slate-200 px-6 py-4">
                    @csrf
                    @method('DELETE')
                    <div class="mb-4">
                        <x-input-label for="delete_admin_password" value="Your password (required to delete)" />
                        <x-text-input id="delete_admin_password" name="admin_password" type="password" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('admin_password')" class="mt-2" />
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="$store.employeeManager.closeDeleteModal()" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-700">Delete Employee</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('employeeManager', {
                showCreateModal: false,
                showEditModal: false,
                showDeleteModal: false,
                currentEmployee: {},

                openCreateModal() {
                    this.showCreateModal = true;
                },
                closeCreateModal() {
                    this.showCreateModal = false;
                },
                openEditModal(employee) {
                    this.currentEmployee = employee;
                    this.showEditModal = true;
                },
                closeEditModal() {
                    this.showEditModal = false;
                },
                openDeleteModal(employee) {
                    this.currentEmployee = employee;
                    this.showDeleteModal = true;
                },
                closeDeleteModal() {
                    this.showDeleteModal = false;
                }
            });
        });
    </script>
</x-app-layout>
