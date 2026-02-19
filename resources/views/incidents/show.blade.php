<x-app-layout>
    @php($pageTitle = 'Incident Details')
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900">Incident report</h2>
                <p class="text-sm text-slate-500">{{ $incident->employee?->first_name }} {{ $incident->employee?->last_name }} • {{ $incident->incident_date->format('M d, Y') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('incidents.index') }}" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">Back to reports</a>
                @if (auth()->user()->name === 'Allen Tamang')
                    <a href="{{ route('incidents.edit', $incident) }}" class="inline-flex items-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-slate-800">Edit report</a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                @php($attachmentUrl = $incident->attachment_path ? asset('storage/' . $incident->attachment_path) : null)
                @php($extension = $incident->attachment_path ? strtolower(pathinfo($incident->attachment_path, PATHINFO_EXTENSION)) : null)
                @php($imageExtensions = ['jpg', 'jpeg', 'png', 'webp'])
                @php($videoExtensions = ['mp4', 'mov', 'avi', 'wmv'])
                @php($isImage = $extension && in_array($extension, $imageExtensions, true))
                @php($isVideo = $extension && in_array($extension, $videoExtensions, true))

                <dl class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-widest text-slate-500">Employee</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ $incident->employee?->first_name }} {{ $incident->employee?->last_name }} ({{ $incident->employee?->employee_no }})</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-widest text-slate-500">Incident date</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ $incident->incident_date->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-widest text-slate-500">Report logged</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ $incident->created_at->timezone(config('app.timezone'))->format('M d, Y \a\t h:i A') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-widest text-slate-500">Last updated</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ $incident->updated_at->timezone(config('app.timezone'))->format('M d, Y \a\t h:i A') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-widest text-slate-500">Recorded by</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ $incident->recordedBy?->name ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-widest text-slate-500">Updated by</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ $incident->updatedBy?->name ?? 'N/A' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-semibold uppercase tracking-widest text-slate-500">Description</dt>
                        <dd class="mt-1 text-sm text-slate-900 whitespace-pre-line">{{ $incident->description }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-semibold uppercase tracking-widest text-slate-500">Attachment</dt>
                        <dd class="mt-1 text-sm text-slate-900">
                            @if ($attachmentUrl)
                                @if ($isImage)
                                    <img src="{{ $attachmentUrl }}" alt="Incident attachment" class="mt-2 max-h-96 w-full rounded-xl border border-slate-200 object-contain bg-slate-50" />
                                @elseif ($isVideo)
                                    <video controls class="mt-2 w-full max-h-96 rounded-xl border border-slate-200 bg-black">
                                        <source src="{{ $attachmentUrl }}" type="video/{{ $extension === 'mov' ? 'quicktime' : $extension }}">
                                        Your browser does not support the video tag.
                                    </video>
                                @else
                                    <a href="{{ $attachmentUrl }}" class="text-slate-700 underline" target="_blank" rel="noreferrer">Download attachment</a>
                                @endif
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            @if (auth()->user()->name === 'Allen Tamang')
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                    <h3 class="text-sm font-semibold text-slate-900">Delete report</h3>
                    <p class="mt-1 text-sm text-slate-500">Admin password is required to delete this incident report.</p>
                    <form method="POST" action="{{ route('incidents.destroy', $incident) }}" class="mt-4 space-y-4">
                        @csrf
                        @method('DELETE')
                        <div>
                            <x-input-label for="admin_password" value="Your password" />
                            <x-text-input id="admin_password" name="admin_password" type="password" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('admin_password')" class="mt-2" />
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-700">Delete report</button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
