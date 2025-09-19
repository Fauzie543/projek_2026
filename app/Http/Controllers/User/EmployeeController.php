<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::all();
        return view('employees.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function data()
    {
        $employees = Employee::with(['user', 'role']);

        return DataTables::of($employees)
            ->addIndexColumn()
            ->addColumn('name', function ($employee) {
                return $employee->user->name;
            })
            ->addColumn('email', function ($employee) {
                return $employee->user->email;
            })
            ->addColumn('role', function ($employee) {
                return $employee->role ? Str::ucfirst($employee->role->name) : 'No Role';
            })
            ->addColumn('action', function ($employee) {
                $deleteUrl = route('employees.destroy', $employee->id);
                
                // --- PERUBAHAN DI SINI ---
                // Mengubah form delete menjadi tombol biasa untuk di-handle oleh AJAX
                return '
                    <a href="javascript:void(0)" data-id="' . $employee->id . '" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-xs edit-btn">Edit</a>
                    <a href="javascript:void(0)" data-url="' . $deleteUrl . '" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-xs delete-btn ml-2">Delete</a>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'nik' => ['required', 'string', 'max:255', 'unique:'.Employee::class],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'hire_date' => ['nullable', 'date'],
            'salary_monthly' => ['nullable', 'numeric'],
            'role' => ['required', 'integer', 'exists:roles,id'],
            'position' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $role = Role::findById($request->role);
            $user->assignRole($role->name);

            $user->employee()->create([
                'nik' => $request->nik,
                'phone' => $request->phone,
                'address' => $request->address,
                'hire_date' => $request->hire_date,
                'salary_monthly' => $request->salary_monthly,
                'role_id' => $request->role,
                'position' => $request->position,
            ]);
        });

        return response()->json(['success' => 'Employee created successfully.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $employee = Employee::with(['user', 'role'])->findOrFail($id);
        return response()->json($employee);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $employee = Employee::findOrFail($id);

        if (!$employee->user) {
            return response()->json(['message' => 'Data inconsistency: User for this employee not found.'], 500);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($employee->user_id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'nik' => ['required', 'string', 'max:255', Rule::unique('employees')->ignore($id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'hire_date' => ['nullable', 'date'],
            'salary_monthly' => ['nullable', 'numeric'],
            'role' => ['required', 'integer', 'exists:roles,id'],
            'position' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($request, $employee) {
            $user = $employee->user;
            $user->name = $request->name;
            $user->email = $request->email;
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->save();

            $role = Role::findById($request->role);
            $user->syncRoles($role->name);

            $employee->update([
                'nik' => $request->nik,
                'phone' => $request->phone,
                'address' => $request->address,
                'hire_date' => $request->hire_date,
                'salary_monthly' => $request->salary_monthly,
                'role_id' => $request->role,
                'position' => $request->position,
            ]);
        });

        return response()->json(['success' => 'Employee updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $employee = Employee::findOrFail($id);
            DB::transaction(function () use ($employee) {
                if ($employee->user) {
                    $employee->user->delete();
                } else {
                    $employee->delete();
                }
            });

            // --- PERUBAHAN DI SINI ---
            // Mengembalikan response JSON untuk di-handle oleh AJAX
            return response()->json(['success' => 'Employee has been deleted successfully.']);
        } catch (\Exception $e) {
            // Mengembalikan response error jika gagal
            return response()->json(['error' => 'Failed to delete employee.'], 500);
        }
    }
}