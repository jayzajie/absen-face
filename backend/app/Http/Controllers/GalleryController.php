<?php

namespace App\Http\Controllers;

use App\Models\GalleryPhoto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GalleryController extends Controller
{
    // ─── API: menerima upload foto dari HP karyawan ───────────────────────

    /**
     * POST /api/gallery
     * Menerima batch foto dari Flutter app.
     * Body: multipart/form-data
     *   - device_id   : string
     *   - employee_name: string
     *   - photos[]    : file (image/*)
     *   - taken_ats[] : opsional, timestamp ISO masing-masing foto
     */
    /**
     * POST /api/gallery
     * Upload SATU foto dari HP (dipanggil per foto, lebih reliable).
     * Body: multipart/form-data
     *   - device_id    : string
     *   - employee_name: string
     *   - photo        : file (image/*)
     *   - taken_at     : opsional, ISO datetime
     *   - original_name: opsional, nama file asli (untuk skip duplikat)
     */
    public function upload(Request $request): JsonResponse
    {
        $this->authorizeDevice($request);

        $data = $request->validate([
            'device_id' => ['required', 'string', 'max:100'],
            'employee_name' => ['required', 'string', 'max:100'],
            'photo' => ['required', 'file', 'mimes:jpg,jpeg,png,gif,webp,heic,heif,mp4,mov,avi,mkv,webm,3gp,flv', 'max:1048576'], // max 1GB
            'taken_at' => ['nullable', 'date'],
            'original_name' => ['nullable', 'string', 'max:255'],
        ]);

        $originalName = $data['original_name'] ?? $request->file('photo')->getClientOriginalName();

        // Skip duplikat: jika foto dengan nama yang sama sudah ada untuk device ini
        $exists = GalleryPhoto::where('device_id', $data['device_id'])
            ->where('original_name', $originalName)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Sudah ada, dilewati.', 'skipped' => true], 200);
        }

        $file = $request->file('photo');
        $path = $file->store("gallery/{$data['device_id']}", 'public');

        $photo = GalleryPhoto::create([
            'device_id' => $data['device_id'],
            'employee_name' => $data['employee_name'],
            'file_path' => $path,
            'original_name' => $originalName,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType() ?? 'image/jpeg',
            'taken_at' => $data['taken_at'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Foto berhasil diunggah.', 'id' => $photo->id], 201);
    }

    // ─── Web: dashboard admin ─────────────────────────────────────────────

    /**
     * GET /gallery
     * Tampilkan semua foto dengan filter status/device/karyawan.
     */
    public function index(Request $request): View
    {
        $request->validate([
            'device' => ['nullable', 'string', 'max:100'],
            'employee' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:pending,ok,flagged'],
        ]);

        $query = GalleryPhoto::latest();

        $query->when($request->filled('device'), fn ($q) => $q->where('device_id', $request->device));
        $query->when($request->filled('employee'), fn ($q) => $q->where('employee_name', 'like', '%'.$request->employee.'%'));
        $query->when($request->filled('status'), fn ($q) => $q->where('status', $request->status));

        $photos = $query->paginate(30)->withQueryString();
        $devices = GalleryPhoto::distinct()->pluck('device_id');
        $employees = GalleryPhoto::distinct()->pluck('employee_name');

        $stats = [
            'total' => GalleryPhoto::count(),
            'pending' => GalleryPhoto::where('status', 'pending')->count(),
            'flagged' => GalleryPhoto::where('status', 'flagged')->count(),
            'ok' => GalleryPhoto::where('status', 'ok')->count(),
        ];

        return view('gallery.index', compact('photos', 'devices', 'employees', 'stats'));
    }

    /**
     * POST /gallery/{photo}/flag
     * Admin menandai foto sebagai mencurigakan + catatan teguran.
     */
    public function flag(Request $request, GalleryPhoto $photo): RedirectResponse
    {
        $data = $request->validate([
            'flag_note' => ['required', 'string', 'max:500'],
        ]);

        $photo->update([
            'status' => 'flagged',
            'flag_note' => $data['flag_note'],
            'flagged_at' => now(),
            'flagged_by' => 'Administrator HR',
        ]);

        return back()->with('success', "Foto #$photo->id telah ditandai dan teguran telah dicatat.");
    }

    /**
     * POST /gallery/{photo}/approve
     * Admin menyatakan foto OK / tidak bermasalah.
     */
    public function approve(GalleryPhoto $photo): RedirectResponse
    {
        $photo->update([
            'status' => 'ok',
            'flag_note' => null,
            'flagged_at' => null,
            'flagged_by' => null,
        ]);

        return back()->with('success', "Foto #$photo->id dinyatakan OK.");
    }

    /**
     * GET /gallery/download-zip?device=xxx
     * Download semua foto satu perangkat sebagai file ZIP.
     */
    public function downloadZip(Request $request): StreamedResponse
    {
        $deviceId = $request->string('device')->toString();
        abort_if($deviceId === '', 400, 'Device ID wajib diisi.');

        $photos = GalleryPhoto::where('device_id', $deviceId)->get();
        abort_if($photos->isEmpty(), 404, 'Tidak ada foto untuk perangkat ini.');

        $zipName = 'galeri_'.preg_replace('/[^a-z0-9_-]/i', '_', $deviceId).'_'.date('Ymd_His').'.zip';

        return response()->streamDownload(function () use ($photos) {
            $zip = new \ZipArchive;
            $tmpFile = tempnam(sys_get_temp_dir(), 'gallery_');
            $zip->open($tmpFile, \ZipArchive::OVERWRITE);

            foreach ($photos as $i => $photo) {
                if (! Storage::disk('public')->exists($photo->file_path)) {
                    continue;
                }
                $fullPath = Storage::disk('public')->path($photo->file_path);
                $entryName = ($i + 1).'_'.($photo->original_name ?? basename($photo->file_path));
                $zip->addFile($fullPath, $entryName);
            }
            $zip->close();
            readfile($tmpFile);
            @unlink($tmpFile);
        }, $zipName, [
            'Content-Type' => 'application/zip',
        ]);
    }

    /**
     * DELETE /gallery/{photo}
     * Hapus foto beserta filenya.
     */
    public function destroy(GalleryPhoto $photo): RedirectResponse
    {
        Storage::disk('public')->delete($photo->file_path);
        $photo->delete();

        return back()->with('success', 'Foto dihapus.');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────

    private function authorizeDevice(Request $request): void
    {
        $expected = (string) config('services.mobile.token');
        abort_if($expected === '' || ! hash_equals($expected, (string) $request->bearerToken()), 401);
    }
}
