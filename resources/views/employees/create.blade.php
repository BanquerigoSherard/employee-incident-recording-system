<x-app-layout>
    @php($pageTitle = 'Add Employee')
    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-semibold text-slate-900">Add employee</h2>
            <p class="text-sm text-slate-500">Create a new employee record.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <form method="POST" action="{{ route('employees.store') }}" enctype="multipart/form-data" class="space-y-6" data-upload-form>
                    @csrf

                    @include('employees.partials.form')

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('employees.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-800">Cancel</a>
                        <x-primary-button>Save employee</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
