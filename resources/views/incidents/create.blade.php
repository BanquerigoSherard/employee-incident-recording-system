<x-app-layout>
    @php($pageTitle = 'Add Incident Report')
    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-semibold text-slate-900">Add incident report</h2>
            <p class="text-sm text-slate-500">Document workplace incidents with complete and accurate details.</p>
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
                <form method="POST" action="{{ route('incidents.store') }}" enctype="multipart/form-data" class="space-y-6" data-upload-form>
                    @csrf

                    @include('incidents.partials.form')

                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-6">
                        <a href="{{ route('dashboard') }}" class="text-sm font-medium text-slate-600 hover:text-slate-800" data-upload-cancel>Cancel</a>
                        <x-primary-button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800" data-upload-submit>
                            <span data-upload-submit-text>Submit report</span>
                            <span data-upload-spinner class="ml-2 hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white" aria-hidden="true"></span>
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
