<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengambil semua role untuk dikirim ke dropdown di modal
        $roles = Role::all();
        return view('users.index', compact('roles'));
    }

    /**
     * Process datatables ajax request.
     */
    public function data(Request $request)
    {
        $users = User::with('roles')->select('users.*');

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('role', function ($user) {
                // Mengambil nama role pertama atau menampilkan 'No Role'
                $roleName = $user->roles->first()->name ?? 'No Role';
                return Str::ucfirst($roleName);
            })
            ->editColumn('created_at', function ($user) {
                return $user->created_at->format('Y-m-d'); // Format tanggal
            })
            ->addColumn('action', function ($user) {
                $deleteUrl = route('users.destroy', $user->id);
                return '
                    <a href="javascript:void(0)" data-id="' . $user->id . '" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-xs edit-btn">Edit</a>
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
            'role' => ['required', 'integer', 'exists:roles,id'],
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $role = Role::findById($request->role);
            $user->assignRole($role->name);
        });

        return response()->json(['success' => 'User created successfully.']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::with('roles')->findOrFail($id);
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'integer', 'exists:roles,id'],
        ]);

        DB::transaction(function () use ($request, $user) {
            $user->name = $request->name;
            $user->email = $request->email;
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->save();

            $role = Role::findById($request->role);
            $user->syncRoles($role->name);
        });

        return response()->json(['success' => 'User updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Mencegah user menghapus akunnya sendiri
            if ($id == auth()->id()) {
                return response()->json(['error' => 'You cannot delete your own account.'], 403);
            }
            
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json(['success' => 'User has been deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete user.'], 500);
        }
    }
}