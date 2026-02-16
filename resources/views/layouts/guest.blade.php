<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($pageTitle) ? $pageTitle . ' | ' . config('app.name') : config('app.name') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="min-h-screen bg-slate-100">
            <div class="mx-auto flex min-h-screen max-w-6xl flex-col justify-center px-4 py-12 sm:px-6 lg:px-8">
                <div class="grid gap-10 lg:grid-cols-[1.1fr_1fr] lg:items-center">
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-900">
                                <x-application-logo class="h-7 w-7 fill-current text-white" />
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">EIRS — Employee Incident Recording System</p>
                                <h1 class="text-3xl font-semibold text-slate-900">Secure access for incident reporting</h1>
                            </div>
                        </div>
                        <p class="text-base text-slate-600">
                            Record, review, and manage employee incidents with a streamlined workflow built for compliance and accountability.
                        </p>
                        <div class="flex flex-wrap gap-3 text-sm text-slate-600">
                            <span class="rounded-full bg-white px-3 py-1 shadow-sm ring-1 ring-slate-200">Audit-ready logs</span>
                            <span class="rounded-full bg-white px-3 py-1 shadow-sm ring-1 ring-slate-200">Role-based access</span>
                            <span class="rounded-full bg-white px-3 py-1 shadow-sm ring-1 ring-slate-200">Centralized reporting</span>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white p-6 shadow-lg ring-1 ring-slate-200 sm:p-8">
                        {{ $slot }}
                    </div>
                </div>

                <div class="mt-10 border-t border-slate-200 pt-6 text-xs text-slate-500">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <span>Confidential • Authorized Personnel Only</span>
                        <span>EIRS © {{ now()->year }}</span>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
