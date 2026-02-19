<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Employee::query();

        $search = request('search');
        $department = request('department');
        $status = request('status');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('employee_no', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('section', 'like', "%{$search}%");
            });
        }

        if ($department) {
            $query->where('department', $department);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $employees = $query
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(10);

        $employees->appends(request()->query());

        $departments = Employee::query()
            ->select('department')
            ->whereNotNull('department')
            ->distinct()
            ->orderBy('department')
            ->pluck('department');

        return view('employees.index', compact('employees', 'departments', 'search', 'department', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('employees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_no' => ['required', 'string', 'max:50', 'unique:employees,employee_no'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'department' => ['required', 'string', 'max:150'],
            'section' => ['required', 'string', 'max:150'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $photo = $request->file('photo');
        unset($validated['photo']);

        $employee = Employee::create($validated);

        if ($photo) {
            $employee->update([
                'photo_path' => $photo->store('employees', 'public'),
            ]);
        }

        return redirect()->route('employees.show', $employee)->with('status', 'Employee created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        $incidents = $employee->incidents()
            ->latest('created_at')
            ->latest('incident_date')
            ->get();

        return view('employees.show', compact('employee', 'incidents'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        if (!$this->isAuthorizedUser()) {
            return redirect()->route('employees.show', $employee)->with('error', 'You do not have permission to edit employees.');
        }

        return view('employees.edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        if (!$this->isAuthorizedUser()) {
            return back()->with('error', 'You do not have permission to edit employees.');
        }

        if (!$this->validateAdminPassword($request)) {
            return back()->withInput()->withErrors([
                'admin_password' => 'Invalid admin password.',
            ]);
        }

        $validated = $request->validate([
            'employee_no' => ['required', 'string', 'max:50', Rule::unique('employees', 'employee_no')->ignore($employee->id)],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'department' => ['required', 'string', 'max:150'],
            'section' => ['required', 'string', 'max:150'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $photo = $request->file('photo');
        unset($validated['photo']);

        $employee->update($validated);

        if ($photo) {
            if ($employee->photo_path) {
                Storage::disk('public')->delete($employee->photo_path);
            }

            $employee->update([
                'photo_path' => $photo->store('employees', 'public'),
            ]);
        }

        Log::info('Employee updated.', [
            'employee_id' => $employee->id,
            'employee_no' => $employee->employee_no,
            'updated_fields' => array_values(array_unique(array_merge(
                array_keys($validated),
                $photo ? ['photo'] : []
            ))),
            'has_photo' => (bool) $employee->photo_path,
            'ip' => $request->ip(),
        ]);

        return redirect()->route('employees.show', $employee)->with('status', 'Employee updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Employee $employee)
    {
        if (!$this->isAuthorizedUser()) {
            return back()->with('error', 'You do not have permission to delete employees.');
        }

        if (!$this->validateAdminPassword($request)) {
            return back()->withInput()->withErrors([
                'admin_password' => 'Invalid admin password.',
            ]);
        }

        if ($employee->photo_path) {
            Storage::disk('public')->delete($employee->photo_path);
        }

        Log::info('Employee deleted.', [
            'employee_id' => $employee->id,
            'employee_no' => $employee->employee_no,
            'has_photo' => (bool) $employee->photo_path,
            'ip' => $request->ip(),
        ]);

        $employee->delete();

        return redirect()->route('employees.index')->with('status', 'Employee deleted successfully.');
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

    private function isAuthorizedUser(): bool
    {
        $user = Auth::user();
        return $user && $user->id === 1;
    }
}
