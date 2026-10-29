<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $validUser = config('services.hr.username');
        $validPass = config('services.hr.password');

        if (
            $validUser !== '' && $validPass !== ''
            && hash_equals((string) $validUser, $request->string('username')->toString())
            && hash_equals((string) $validPass, $request->string('password')->toString())
        ) {
            session()->regenerate();
            session()->put('hr_authenticated', true);

            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withInput(['username' => $request->username])
            ->withErrors(['password' => 'Username atau password salah.']);
    }

    public function logout(Request $request): RedirectResponse
    {
        session()->forget('hr_authenticated');
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    }

    public function mobileLogin(Request $request): JsonResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'device_id' => ['nullable', 'string', 'max:100'],
        ]);

        $employee = Employee::where('username', $request->username)->first();

        if (! $employee || ! Hash::check($request->password, $employee->password)) {
            return response()->json(['message' => 'Username atau password salah'], 401);
        }

        if ($request->filled('device_id') && $employee->device_id !== $request->string('device_id')->toString()) {
            $employee->update(['device_id' => $request->string('device_id')->toString()]);
        }

        $hasFace = ! empty($employee->face_photo_path) && Storage::disk('local')->exists($employee->face_photo_path);

        return response()->json([
            'name' => $employee->name,
            'device_id' => $employee->device_id,
            'has_face_registered' => $hasFace,
            'token' => Crypt::encryptString($employee->id.'|'.now()->addHours(12)->timestamp),
        ]);
    }

    public function registerFace(Request $request): JsonResponse
    {
        $employee = $this->authenticatedEmployee($request);

        $request->validate([
            'face_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        // Opsional: Validasi dengan service Python jika aktif agar foto memuat tepat 1 wajah
        $faceServiceUrl = config('services.face.url');
        if (! empty($faceServiceUrl)) {
            try {
                $faceBytes = file_get_contents($request->file('face_photo')->getRealPath());
                $response = Http::acceptJson()->timeout(15)->post(rtrim($faceServiceUrl, '/').'/verify', [
                    'reference_base64' => base64_encode($faceBytes),
                    'selfie_base64' => base64_encode($faceBytes),
                ]);

                if ($response->status() === 422) {
                    return response()->json([
                        'message' => $response->json('message') ?: 'Wajah tidak terdeteksi dengan jelas pada foto. Pastikan pencahayaan cukup.',
                    ], 422);
                }
            } catch (Throwable) {
                // Lewati verifikasi jika service ML sedang offline
            }
        }

        $path = $request->file('face_photo')->store('employee-faces', 'local');
        abort_if(! $path, 500, 'Foto wajah gagal disimpan.');

        $previousPath = $employee->face_photo_path;
        $employee->update(['face_photo_path' => $path]);

        if ($previousPath && $previousPath !== $path && Storage::disk('local')->exists($previousPath)) {
            Storage::disk('local')->delete($previousPath);
        }

        return response()->json([
            'message' => 'Foto wajah berhasil didaftarkan.',
            'has_face_registered' => true,
        ]);
    }

    private function authenticatedEmployee(Request $request): Employee
    {
        try {
            [$employeeId, $expiresAt] = explode('|', Crypt::decryptString((string) $request->bearerToken()), 2);
        } catch (Throwable) {
            abort(401, 'Sesi aplikasi tidak valid. Silakan login kembali.');
        }

        abort_if(! ctype_digit($employeeId) || ! ctype_digit($expiresAt) || now()->timestamp > (int) $expiresAt, 401, 'Sesi aplikasi telah berakhir. Silakan login kembali.');

        return Employee::findOrFail((int) $employeeId);
    }
}
