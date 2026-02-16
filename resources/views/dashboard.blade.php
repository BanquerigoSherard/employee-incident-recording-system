<x-app-layout>
    @php($pageTitle = 'Dashboard')
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900">EIRS Dashboard</h2>
                <p class="text-sm text-slate-500">Overview of incident activity and compliance status.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('incidents.export') }}" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">
                    Export report
                </a>
                <a href="{{ route('incidents.create') }}" class="inline-flex items-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-slate-800">
                    New incident
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">Total incidents</p>
                    <div class="mt-3 flex items-end justify-between">
                        <p class="text-3xl font-semibold text-slate-900">{{ $totalIncidents }}</p>
                        <span class="rounded-full bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700">+{{ $incidentsThisWeek }} this week</span>
                    </div>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">Employees</p>
                    <div class="mt-3 flex items-end justify-between">
                        <p class="text-3xl font-semibold text-slate-900">{{ $totalEmployees }}</p>
                        <span class="rounded-full bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700">Active roster</span>
                    </div>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">Incidents this week</p>
                    <div class="mt-3 flex items-end justify-between">
                        <p class="text-3xl font-semibold text-slate-900">{{ $incidentsThisWeek }}</p>
                        <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">Last 7 days</span>
                    </div>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">Attachment rate</p>
                    <div class="mt-3 flex items-end justify-between">
                        <p class="text-3xl font-semibold text-slate-900">{{ $attachmentRate }}%</p>
                        <span class="rounded-full bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700">With attachments</span>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1.6fr_1fr]">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-slate-900">Recent incident activity</h3>
                        <a href="{{ route('incidents.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">View all</a>
                    </div>
                    <div class="mt-4 space-y-4">
                        @forelse ($recentIncidents as $incident)
                            <div class="flex items-start justify-between rounded-xl border border-slate-200 p-4">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ \Illuminate\Support\Str::limit($incident->description, 60) }}</p>
                                    <p class="text-xs text-slate-500">
                                        Logged by {{ $incident->recordedBy?->name ?? 'System' }} • {{ $incident->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">Logged</span>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">No recent incidents logged.</p>
                        @endforelse
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-slate-900">Quick actions</h3>
                        <div class="mt-4 space-y-3">
                            <a href="{{ route('incidents.create') }}" class="block w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-center text-sm font-medium text-slate-700 hover:bg-slate-50">
                                Create incident report
                            </a>
                            <a href="{{ route('incidents.index') }}" class="block w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-center text-sm font-medium text-slate-700 hover:bg-slate-50">
                                Review incident list
                            </a>
                            <a href="{{ route('incidents.export') }}" class="block w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-center text-sm font-medium text-slate-700 hover:bg-slate-50">
                                Generate incident export
                            </a>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-slate-900">System health</h3>
                        <div class="mt-4 space-y-3 text-sm text-slate-600">
                            <div class="flex items-center justify-between">
                                <span>Upload readiness</span>
                                <span class="font-medium text-emerald-600">Operational</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Incident volume</span>
                                <span class="font-medium text-emerald-600">{{ $incidentsThisWeek }} this week</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Attachment coverage</span>
                                <span class="font-medium text-amber-600">{{ $attachmentRate }}% uploaded</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
