<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\ProfileController;
use App\Models\Employee;
use App\Models\Incident;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $totalIncidents = Incident::count();
    $totalEmployees = Employee::count();
    $incidentsThisWeek = Incident::whereDate('created_at', '>=', now()->subDays(7))->count();
    $attachmentsCount = Incident::whereNotNull('attachment_path')->count();
    $attachmentRate = $totalIncidents > 0 ? (int) round(($attachmentsCount / $totalIncidents) * 100) : 0;

    $recentIncidents = Incident::with(['employee', 'recordedBy'])
        ->latest('created_at')
        ->limit(5)
        ->get();

    return view('dashboard', compact(
        'totalIncidents',
        'totalEmployees',
        'incidentsThisWeek',
        'attachmentRate',
        'recentIncidents'
    ));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('employees', EmployeeController::class);
    Route::resource('incidents', IncidentController::class);
    Route::get('incidents-export', [IncidentController::class, 'export'])->name('incidents.export');
});

require __DIR__.'/auth.php';
