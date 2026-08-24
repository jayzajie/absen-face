<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\GalleryPhoto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function dashboard(Request $request): View
    {
        $request->validate([
            'view' => ['nullable', 'in:history,employees,devices'],
            'search' => ['nullable', 'string', 'max:100'],
            'date' => ['nullable', 'date_format:Y-m-d'],
            'type' => ['nullable', 'in:masuk,pulang'],
            'device' => ['nullable', 'string', 'max:100'],   // filter device di halaman Perangkat
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

        // Galeri per perangkat (untuk halaman Perangkat)
        $selectedDevice = $request->string('device')->toString() ?: null;
        $galleryPhotos = $selectedDevice
            ? GalleryPhoto::where('device_id', $selectedDevice)
                ->latest()
                ->get()
            : collect();

        $galleryStats = GalleryPhoto::selectRaw('device_id, count(*) as total, sum(status="flagged") as flagged')
            ->groupBy('device_id')
            ->get()
            ->keyBy('device_id');

        $registeredEmployees = \App\Models\Employee::all();

        return view('attendance', [
            'currentView' => $currentView,
            'attendances' => $history->latest('occurred_at')->limit(100)->get(),
            'employees' => $all->groupBy('employee_name'),
            'devices' => $all->groupBy('device_id'),
            'today' => $today,
            'checkedIn' => $today->contains('type', 'masuk'),
            'checkedOut' => $today->contains('type', 'pulang'),
            'latestDevice' => $today->first(),
            'galleryPhotos' => $galleryPhotos,
            'galleryStats' => $galleryStats,
            'selectedDevice' => $selectedDevice,
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

        $now = now();
        $targetTime = $now->copy()->setTime(9, 0, 0); // Target jam 09:00
        $isLate = $now->greaterThan($targetTime);
        $diffMinutes = $now->diffInMinutes($targetTime);

        $attendance = Attendance::create([
            'employee_name' => $data['employee_name'] ?? 'Dimas Pratama',
            'type' => $data['type'],
            'occurred_at' => $now,
            'status' => $data['type'] === 'masuk' && $isLate ? 'Terlambat' : 'Tepat waktu',
            'source' => 'mobile',
            'device_id' => $data['device_id'],
            'photo_access_granted' => $data['camera_access_granted'],
        ]);

        $responseData = $attendance->toArray();
        $responseData['diff_minutes'] = $diffMinutes;
        $responseData['is_late'] = $isLate;

        if ($data['type'] === 'pulang') {
            $checkIn = Attendance::where('employee_name', $attendance->employee_name)
                ->where('type', 'masuk')
                ->whereDate('occurred_at', $now->toDateString())
                ->latest('occurred_at')
                ->first();
            $responseData['check_in_time'] = $checkIn ? $checkIn->occurred_at->format('H:i') : '--:--';
        }

        return response()->json($responseData, 201);
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
}
