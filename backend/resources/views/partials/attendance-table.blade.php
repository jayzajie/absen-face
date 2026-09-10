<div class="table-wrap">
    <table class="attendance-table">
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Karyawan</th>
                <th>Jenis</th>
                <th>Status</th>
                <th>Perangkat</th>
                <th>Kamera</th>
                <th>Verifikasi wajah</th>
                <th><span class="visually-hidden">Aksi</span></th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                <tr>
                    <td class="time-cell" data-label="Waktu"><strong>{{ $item->occurred_at->format('H:i') }}</strong><span class="person-meta">{{ $item->occurred_at->format('d M Y') }} WITA</span></td>
                    <td data-label="Karyawan"><div class="person"><span class="person-avatar">{{ strtoupper(substr($item->employee_name, 0, 2)) }}</span><span class="person-name">{{ $item->employee_name }}</span></div></td>
                    <td data-label="Jenis">{{ ucfirst($item->type) }}</td>
                    <td data-label="Status"><span class="status {{ $item->status === 'Terlambat' ? 'warning' : '' }}">{{ $item->status }}</span></td>
                    <td data-label="Perangkat">{{ $item->device_id }}</td>
                    <td data-label="Kamera"><span class="status {{ $item->photo_access_granted ? 'neutral' : 'warning' }}">{{ $item->photo_access_granted ? 'Diizinkan' : 'Belum' }}</span></td>
                    <td data-label="Verifikasi wajah">
                        @if($item->face_match_score !== null)
                            <strong>{{ number_format($item->face_match_score, 3) }}</strong><span class="person-meta">batas {{ number_format($item->face_threshold, 3) }}</span>
                        @else
                            <span class="muted">Belum diverifikasi</span>
                        @endif
                    </td>
                    <td data-label="Aksi">
                        <form method="POST" action="{{ route('attendances.destroy', $item) }}" onsubmit="return confirm('Hapus catatan absensi ini?')" data-loading-form>
                            @csrf @method('DELETE')
                            <button type="submit" class="button-quiet danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" data-label=""><div class="empty-state"><strong>Belum ada catatan absensi</strong><p>Data akan muncul setelah aplikasi mobile berhasil mengirim absensi.</p></div></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
