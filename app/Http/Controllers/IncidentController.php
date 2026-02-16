<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class IncidentController extends Controller
{
    public function index()
    {
        $search = request('search');
        $employeeFilter = request('employee_id');
        $dateFrom = request('date_from');
        $dateTo = request('date_to');
        $currentUserId = Auth::id();

        if ($currentUserId) {
            Incident::whereNull('created_by')->orWhereNull('updated_by')->update([
                'created_by' => $currentUserId,
                'updated_by' => $currentUserId,
            ]);
        }

        $incidentsQuery = Incident::with(['employee', 'recordedBy', 'updatedBy']);

        if ($search) {
            $incidentsQuery->where(function ($query) use ($search) {
                $query->where('description', 'like', "%{$search}%")
                    ->orWhereHas('employee', function ($employeeQuery) use ($search) {
                        $employeeQuery->where('employee_no', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('department', 'like', "%{$search}%")
                            ->orWhere('section', 'like', "%{$search}%");
                    });
            });
        }

        if ($employeeFilter) {
            $incidentsQuery->where('employee_id', $employeeFilter);
        }

        if ($dateFrom) {
            $incidentsQuery->whereDate('incident_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $incidentsQuery->whereDate('incident_date', '<=', $dateTo);
        }

        $incidents = $incidentsQuery
            ->latest('created_at')
            ->latest('incident_date')
            ->paginate(10)
            ->withQueryString();

        $oldInput = session()->getOldInput();
        $errorContext = $oldInput['form_context'] ?? null;
        $errorIncidentId = $oldInput['incident_id'] ?? null;
        $errorIncident = $errorIncidentId
            ? $incidents->getCollection()->firstWhere('id', (int) $errorIncidentId)
            : null;
        $openCreateOnError = $errorContext === 'create';
        $openEditOnError = $errorContext === 'edit';
        $openDeleteOnError = $errorContext === 'delete';
        $currentIncidentPayload = ($openEditOnError || $openDeleteOnError) ? [
            'id' => (int) $errorIncidentId,
            'employee_id' => $oldInput['employee_id'] ?? $errorIncident?->employee_id,
            'incident_date' => $oldInput['incident_date'] ?? $errorIncident?->incident_date?->format('Y-m-d'),
            'description' => $oldInput['description'] ?? $errorIncident?->description,
            'attachment_url' => $errorIncident?->attachment_path ? asset('storage/' . $errorIncident->attachment_path) : '',
            'incident_summary' => $errorIncident ? \Illuminate\Support\Str::limit($errorIncident->description, 60) : 'this report',
            'employee_name' => $errorIncident ? ($errorIncident->employee?->first_name . ' ' . $errorIncident->employee?->last_name) : 'this employee',
        ] : [];

        $employees = Employee::query()
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('incidents.index', compact(
            'incidents',
            'employees',
            'search',
            'employeeFilter',
            'dateFrom',
            'dateTo',
            'openCreateOnError',
            'openEditOnError',
            'openDeleteOnError',
            'currentIncidentPayload'
        ));
    }

    public function create()
    {
        $employees = Employee::query()
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('incidents.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'incident_date' => ['required', 'date'],
            'description' => ['required', 'string'],
            'attachment' => ['nullable', 'file', 'max:102400', 'mimetypes:image/jpeg,image/png,image/webp,video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv'],
        ]);

        $attachment = $request->file('attachment');
        unset($validated['attachment']);

        $currentUserId = Auth::id();

        $incident = Incident::create(array_merge($validated, [
            'created_by' => $currentUserId,
            'updated_by' => $currentUserId,
        ]));

        if ($attachment) {
            $incident->update([
                'attachment_path' => $attachment->store('incidents', 'public'),
            ]);
        }

        Log::info('Incident report created.', [
            'incident_id' => $incident->id,
            'employee_id' => $incident->employee_id,
            'incident_date' => $incident->incident_date->format('Y-m-d'),
            'has_attachment' => (bool) $incident->attachment_path,
            'ip' => $request->ip(),
        ]);

        return redirect()->route('incidents.index')
            ->with('status', 'Incident report submitted successfully.');
    }

    public function show(Incident $incident)
    {
        $incident->load('employee');

        return view('incidents.show', compact('incident'));
    }

    public function edit(Incident $incident)
    {
        $employees = Employee::query()
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('incidents.edit', compact('incident', 'employees'));
    }

    public function update(Request $request, Incident $incident)
    {
        if (!$this->validateAdminPassword($request)) {
            return back()->withInput()->withErrors([
                'admin_password' => 'Invalid admin password.',
            ]);
        }

        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'incident_date' => ['required', 'date'],
            'description' => ['required', 'string'],
            'attachment' => ['nullable', 'file', 'max:102400', 'mimetypes:image/jpeg,image/png,image/webp,video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv'],
        ]);

        $attachment = $request->file('attachment');
        unset($validated['attachment']);

            $incident->update(array_merge($validated, [
                'updated_by' => Auth::id(),
            ]));

        if ($attachment) {
            if ($incident->attachment_path) {
                Storage::disk('public')->delete($incident->attachment_path);
            }

            $incident->update([
                'attachment_path' => $attachment->store('incidents', 'public'),
            ]);
        }

        Log::info('Incident report updated.', [
            'incident_id' => $incident->id,
            'employee_id' => $incident->employee_id,
            'incident_date' => $incident->incident_date->format('Y-m-d'),
            'updated_fields' => array_values(array_unique(array_merge(
                array_keys($validated),
                $attachment ? ['attachment'] : []
            ))),
            'has_attachment' => (bool) $incident->attachment_path,
            'ip' => $request->ip(),
        ]);

        return redirect()->route('incidents.show', $incident)
            ->with('status', 'Incident report updated successfully.');
    }

    public function destroy(Request $request, Incident $incident)
    {
        if (!$this->validateAdminPassword($request)) {
            return back()->withInput()->withErrors([
                'admin_password' => 'Invalid admin password.',
            ]);
        }

        if ($incident->attachment_path) {
            Storage::disk('public')->delete($incident->attachment_path);
        }

        $incident->delete();

        return redirect()->route('incidents.index')->with('status', 'Incident report deleted successfully.');
    }

    public function export()
    {
        $timestamp = now()->format('Ymd_His');
        $filename = "incident_report_{$timestamp}.csv";

        $incidents = Incident::with(['employee', 'recordedBy', 'updatedBy'])
            ->latest('created_at')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($incidents) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Incident ID',
                'Employee ID',
                'Employee Name',
                'Department',
                'Section',
                'Incident Date',
                'Description',
                'Recorded By',
                'Updated By',
                'Logged At',
                'Updated At',
                'Attachment URL',
            ]);

            foreach ($incidents as $incident) {
                fputcsv($handle, [
                    $incident->id,
                    $incident->employee?->employee_no,
                    trim(($incident->employee?->first_name ?? '') . ' ' . ($incident->employee?->last_name ?? '')),
                    $incident->employee?->department,
                    $incident->employee?->section,
                    optional($incident->incident_date)->format('Y-m-d'),
                    $incident->description,
                    $incident->recordedBy?->name,
                    $incident->updatedBy?->name,
                    $incident->created_at?->format('Y-m-d H:i:s'),
                    $incident->updated_at?->format('Y-m-d H:i:s'),
                    $incident->attachment_path ? asset('storage/' . $incident->attachment_path) : null,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function validateAdminPassword(Request $request): bool
    {
        $request->validate([
            'admin_password' => ['required', 'string'],
        ]);

        $user = Auth::user();

        if (!$user) {
            return false;
        }

        return Hash::check($request->input('admin_password'), $user->password);
    }
}
