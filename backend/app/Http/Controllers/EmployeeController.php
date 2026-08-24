<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:100', 'unique:employees,username'],
            'password' => ['required', 'string', 'min:4'],
            'device_id' => ['nullable', 'string', 'max:100'],
        ]);

        $data['password'] = Hash::make($data['password']);
        
        Employee::create($data);

        return back()->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $employee->delete();
        return back()->with('success', 'Karyawan berhasil dihapus.');
    }
}
