<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class AttendanceController extends Controller
{
    public function dashboard(Request $request): View
    {
        $request->validate([
            'view' => ['nullable', 'in:history,employees,devices'],
            'search' => ['nullable', 'string', 'max:100'],
            'date' => ['nullable', 'date_format:Y-m-d'],
            'type' => ['nullable', 'in:masuk,pulang'],
        ]);

        $currentView = in_array($request->string('view')->toString(), ['history', 'employees', 'devices'], true)
            ? $request->string('view')->toString()
            : 'dashboard';
        $mobile = Attendance::where('source', 'mobile');
        $today = (clone $mobile)->whereDate('occurred_at', today())->latest('occurred_at')->get();
        $history = clone $mobile;

        $history->when($request->filled('date'), fn ($q) => $q->whereDate('occurred_at', $request->date('date')))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->string('type')))
            ->when($request->filled('search'), fn ($q) => $q->where('employee_name', 'like', '%'.$request->string('search').'%'));

        $all = (clone $mobile)->latest('occurred_at')->get();

        $registeredEmployees = Employee::all();

        return view('attendance', [
            'currentView' => $currentView,
            'attendances' => $history->latest('occurred_at')->limit(100)->get(),
            'employees' => $all->groupBy('employee_name'),
            'devices' => $all->groupBy('device_id'),
            'today' => $today,
            'checkedIn' => $today->contains('type', 'masuk'),
            'checkedOut' => $today->contains('type', 'pulang'),
            'latestDevice' => $today->first(),
            'registeredEmployees' => $registeredEmployees,
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorizeDevice($request);

        return response()->json(Attendance::latest('occurred_at')->limit(50)->get());
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorizeDevice($request);

        $data = $request->validate([
            'employee_name' => ['nullable', 'string', 'max:100'],
            'type' => ['required', 'in:masuk,pulang'],
            'device_id' => ['required', 'string', 'max:100'],
            'camera_access_granted' => ['required', 'boolean'],
        ]);

        return response()->json($this->recordAttendance(
            $data['employee_name'] ?? 'Dimas Pratama',
            $data['type'],
            $data['device_id'],
            $data['camera_access_granted'],
        ), 201);
    }

    public function storeVerified(Request $request): JsonResponse
    {
        $employee = $this->authenticatedEmployee($request);
        $data = $request->validate([
            'type' => ['required', 'in:masuk,pulang'],
            'device_id' => ['required', 'string', 'max:100'],
            'selfie' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        abort_if(! $employee->face_photo_path || ! Storage::disk('local')->exists($employee->face_photo_path), 422, 'Foto acuan wajah belum tersedia.');

        try {
            $response = Http::acceptJson()->timeout(30)->post(rtrim(config('services.face.url'), '/').'/verify', [
                'reference_base64' => base64_encode(Storage::disk('local')->get($employee->face_photo_path)),
                'selfie_base64' => base64_encode(file_get_contents($request->file('selfie')->getRealPath())),
            ]);
        } catch (Throwable) {
            return response()->json(['message' => 'Layanan verifikasi wajah tidak dapat dihubungi.'], 503);
        }

        if (! $response->successful()) {
            return response()->json(['message' => $response->json('message') ?: 'Foto wajah tidak dapat diproses.'], 422);
        }

        $verification = $response->json();
        if (! ($verification['matched'] ?? false)) {
            return response()->json([
                'message' => 'Wajah tidak cocok dengan akun yang sedang login.',
                'face_match_score' => $verification['score'] ?? null,
                'face_threshold' => $verification['threshold'] ?? null,
            ], 422);
        }

        if ($employee->device_id !== $data['device_id']) {
            $employee->update(['device_id' => $data['device_id']]);
        }

        return response()->json($this->recordAttendance(
            $employee->name,
            $data['type'],
            $data['device_id'],
            true,
            $verification,
        ), 201);
    }

    private function recordAttendance(string $employeeName, string $type, string $deviceId, bool $cameraAccessGranted, ?array $verification = null): array
    {
        $now = now();
        $targetTime = $now->copy()->setTime(9, 0, 0); // Target jam 09:00
        $isLate = $now->greaterThan($targetTime);
        $diffMinutes = $now->diffInMinutes($targetTime);

        $attendance = Attendance::create([
            'employee_name' => $employeeName,
            'type' => $type,
            'occurred_at' => $now,
            'status' => $type === 'masuk' && $isLate ? 'Terlambat' : 'Tepat waktu',
            'source' => 'mobile',
            'device_id' => $deviceId,
            'photo_access_granted' => $cameraAccessGranted,
            'face_match_score' => $verification['score'] ?? null,
            'face_threshold' => $verification['threshold'] ?? null,
            'face_model_version' => $verification['model_version'] ?? null,
        ]);

        $responseData = $attendance->toArray();
        $responseData['diff_minutes'] = $diffMinutes;
        $responseData['is_late'] = $isLate;

        if ($type === 'pulang') {
            $checkIn = Attendance::where('employee_name', $attendance->employee_name)
                ->where('type', 'masuk')
                ->whereDate('occurred_at', $now->toDateString())
                ->latest('occurred_at')
                ->first();
            $responseData['check_in_time'] = $checkIn ? $checkIn->occurred_at->format('H:i') : '--:--';
        }

        return $responseData;
    }

    public function destroy(Attendance $attendance): RedirectResponse
    {
        $attendance->delete();

        return back()->with('success', 'Data absensi dihapus.');
    }

    private function authorizeDevice(Request $request): void
    {
        $expected = (string) config('services.mobile.token');
        abort_if($expected === '' || ! hash_equals($expected, (string) $request->bearerToken()), 401);
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
