<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

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

        return response()->json([
            'name' => $employee->name,
            'device_id' => $employee->device_id,
            'token' => Crypt::encryptString($employee->id.'|'.now()->addHours(12)->timestamp),
        ]);
    }
}
