<x-app-layout>
    @php($pageTitle = 'Employee Details')
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900">Employee profile</h2>
                <p class="text-sm text-slate-500">{{ $employee->first_name }} {{ $employee->last_name }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('employees.edit', $employee) }}" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">Edit</a>
                <form method="POST" action="{{ route('employees.destroy', $employee) }}" onsubmit="return confirm('Delete this employee?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center rounded-md bg-rose-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-rose-500">Delete</button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <div class="flex flex-wrap items-center gap-4 border-b border-slate-200 pb-6">
                    <div class="h-20 w-20 overflow-hidden rounded-full border border-slate-200 bg-slate-100">
                        @if ($employee->photo_path)
                            <img src="{{ asset('storage/' . $employee->photo_path) }}" alt="{{ $employee->first_name }} {{ $employee->last_name }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center text-lg font-semibold text-slate-500">
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
                <dl class="mt-6 grid gap-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-widest text-slate-500">Employee ID</dt>
                        <dd class="mt-1 text-sm font-medium text-slate-900">{{ $employee->employee_no }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-widest text-slate-500">Status</dt>
                        <dd class="mt-1">
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium {{ $employee->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ ucfirst($employee->status) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-widest text-slate-500">Department</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ $employee->department }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-widest text-slate-500">Section</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ $employee->section }}</dd>
                    </div>
                </dl>
                <div class="mt-8">
                    <a href="{{ route('employees.index') }}" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">Back to employees</a>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-900">Incident history</h3>
                    <a href="{{ route('incidents.index', ['employee_id' => $employee->id]) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">View all incidents</a>
                </div>
                <div class="mt-4 overflow-hidden rounded-xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">Incident date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">Description</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">Logged</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-widest text-slate-500">Actions</th>
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
                                    <td class="px-4 py-3 text-right text-sm">
                                        <a href="{{ route('incidents.show', $incident) }}" class="text-indigo-600 hover:text-indigo-500">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-500">
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
</x-app-layout>
