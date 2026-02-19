<x-app-layout>
    @php($pageTitle = 'Employee Details')
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900">Employee profile</h2>
                <p class="text-sm text-slate-500">{{ $employee->first_name }} {{ $employee->last_name }}</p>
            </div>
            <div class="flex items-center gap-3" x-data>
                @if (auth()->user()->name === 'Allen Tamang')
                    <a href="{{ route('employees.edit', $employee) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium bg-white border rounded-md shadow-sm border-slate-300 text-slate-700 hover:bg-slate-50">Edit</a>
                    <button @click="$store.employeeShowManager.openDeleteModal({
                        id: {{ $employee->id }},
                        first_name: '{{ $employee->first_name }}',
                        last_name: '{{ $employee->last_name }}'
                    })" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white rounded-md shadow-sm bg-rose-600 hover:bg-rose-500">Delete</button>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl px-4 mx-auto space-y-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="px-4 py-3 text-sm border rounded-lg border-emerald-200 bg-emerald-50 text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="p-6 bg-white border shadow-sm rounded-2xl border-slate-200 sm:p-8">
                <div class="flex flex-wrap items-center gap-4 pb-6 border-b border-slate-200">
                    <div class="w-20 h-20 overflow-hidden border rounded-full border-slate-200 bg-slate-100">
                        @if ($employee->photo_path)
                            <img src="{{ asset('storage/' . $employee->photo_path) }}" alt="{{ $employee->first_name }} {{ $employee->last_name }}" class="object-cover w-full h-full">
                        @else
                            <div class="flex items-center justify-center w-full h-full text-lg font-semibold text-slate-500">
                                {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Employee profile</p>
                        <h3 class="text-xl font-semibold text-slate-900">{{ $employee->first_name }} {{ $employee->last_name }}</h3>
                        <p class="text-sm text-slate-600">{{ $employee->employee_no }}</p>
                    </div>
                </div>
                <dl class="grid gap-6 mt-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-semibold tracking-widest uppercase text-slate-500">Employee ID</dt>
                        <dd class="mt-1 text-sm font-medium text-slate-900">{{ $employee->employee_no }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold tracking-widest uppercase text-slate-500">Status</dt>
                        <dd class="mt-1">
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium {{ $employee->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ ucfirst($employee->status) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold tracking-widest uppercase text-slate-500">Department</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ $employee->department }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold tracking-widest uppercase text-slate-500">Section</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ $employee->section }}</dd>
                    </div>
                </dl>
                <div class="mt-8">
                    <a href="{{ route('employees.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold transition bg-white border rounded-md shadow-sm border-slate-300 text-slate-700 hover:bg-slate-50">Back to employees</a>
                </div>
            </div>

            <div class="p-6 bg-white border shadow-sm rounded-2xl border-slate-200 sm:p-8">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-900">Incident history</h3>
                    <a href="{{ route('incidents.index', ['employee_id' => $employee->id]) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">View all incidents</a>
                </div>
                <div class="mt-4 overflow-hidden border rounded-xl border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-xs font-semibold tracking-widest text-left uppercase text-slate-500">Incident date</th>
                                <th class="px-4 py-3 text-xs font-semibold tracking-widest text-left uppercase text-slate-500">Description</th>
                                <th class="px-4 py-3 text-xs font-semibold tracking-widest text-left uppercase text-slate-500">Logged</th>
                                <th class="px-4 py-3 text-xs font-semibold tracking-widest text-right uppercase text-slate-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse ($incidents as $incident)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 text-sm text-slate-700">
                                        {{ $incident->incident_date?->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-slate-600">
                                        {{ \Illuminate\Support\Str::limit($incident->description, 80) }}
                                    </td>
                                    <td class="px-4 py-3 text-xs text-slate-500">
                                        {{ $incident->created_at?->timezone(config('app.timezone'))->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right">
                                        <a href="{{ route('incidents.show', $incident) }}" class="text-indigo-600 hover:text-indigo-500">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-sm text-center text-slate-500">
                                        No incidents recorded for this employee yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div x-data x-show="$store.employeeShowManager.showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 py-6 bg-slate-900/50">
            <div @click.outside="$store.employeeShowManager.closeDeleteModal()" class="w-full max-w-lg bg-white shadow-2xl rounded-2xl ring-1 ring-slate-200">
                <div class="flex items-center gap-4 px-6 py-4 border-b border-slate-200">
                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-rose-100">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 0v2m0-6h.01M7.21 15.89A10 10 0 1 1 15.89 7.21" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Delete Employee</h3>
                        <p class="text-xs text-slate-500">This action cannot be undone.</p>
                    </div>
                </div>
                <div class="px-6 py-5">
                    <p class="text-sm text-slate-600">Are you sure you want to delete <span x-text="$store.employeeShowManager.currentEmployee.first_name + ' ' + $store.employeeShowManager.currentEmployee.last_name" class="font-medium"></span>?</p>
                </div>
                
                <form method="POST" :action="`{{ route('employees.destroy', '') }}/${$store.employeeShowManager.currentEmployee.id}`" class="px-6 py-4 border-t border-slate-200">
                    @csrf
                    @method('DELETE')
                    <div class="mb-4">
                        <x-input-label for="show_delete_admin_password" value="Your password (required to delete)" />
                        <x-text-input id="show_delete_admin_password" name="admin_password" type="password" class="block w-full mt-1" required />
                        <x-input-error :messages="$errors->get('admin_password')" class="mt-2" />
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="$store.employeeShowManager.closeDeleteModal()" class="px-4 py-2 text-sm font-medium transition border rounded-lg border-slate-300 text-slate-700 hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-sm font-semibold text-white transition rounded-lg bg-rose-600 hover:bg-rose-700">Delete Employee</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('employeeShowManager', {
                showDeleteModal: false,
                currentEmployee: {},

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
