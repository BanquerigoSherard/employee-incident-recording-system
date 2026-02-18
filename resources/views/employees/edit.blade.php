<x-app-layout>
    @php($pageTitle = 'Edit Employee')
    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-semibold text-slate-900">Edit employee</h2>
            <p class="text-sm text-slate-500">Update employee information.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <form method="POST" action="{{ route('employees.update', $employee) }}" enctype="multipart/form-data" class="space-y-6" data-upload-form>
                    @csrf
                    @method('PUT')

                    @include('employees.partials.form', ['employee' => $employee])

                    <div>
                        <x-input-label for="admin_password" value="Your password (required to update)" />
                        <x-text-input id="admin_password" name="admin_password" type="password" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('admin_password')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('employees.show', $employee) }}" class="text-sm font-medium text-slate-600 hover:text-slate-800">Cancel</a>
                        <x-primary-button>Save changes</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
