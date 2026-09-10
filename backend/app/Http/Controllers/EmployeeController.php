<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EmployeeController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:100', 'unique:employees,username'],
            'password' => ['required', 'string', 'min:4'],
            'face_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $path = $request->file('face_photo')->store('employee-faces', 'local');
        abort_if(! $path, 500, 'Foto wajah gagal disimpan.');

        try {
            Employee::create([
                'name' => $data['name'],
                'username' => $data['username'],
                'password' => Hash::make($data['password']),
                'face_photo_path' => $path,
            ]);
        } catch (\Throwable $error) {
            Storage::disk('local')->delete($path);
            throw $error;
        }

        return back()->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function updateFacePhoto(Request $request, Employee $employee): RedirectResponse
    {
        $request->validate([
            'face_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $path = $request->file('face_photo')->store('employee-faces', 'local');
        abort_if(! $path, 500, 'Foto wajah gagal disimpan.');

        $previousPath = $employee->face_photo_path;
        $employee->update(['face_photo_path' => $path]);
        if ($previousPath) {
            Storage::disk('local')->delete($previousPath);
        }

        return back()->with('success', 'Foto acuan wajah berhasil diperbarui.');
    }

    public function facePhoto(Employee $employee): BinaryFileResponse
    {
        abort_if(! $employee->face_photo_path || ! Storage::disk('local')->exists($employee->face_photo_path), 404);

        $response = response()->file(Storage::disk('local')->path($employee->face_photo_path));
        $response->setPrivate();
        $response->setMaxAge(300);

        return $response;
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $photoPath = $employee->face_photo_path;
        $employee->delete();
        if ($photoPath) {
            Storage::disk('local')->delete($photoPath);
        }

        return back()->with('success', 'Karyawan berhasil dihapus.');
    }
}
