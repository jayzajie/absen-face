<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal HR | GJP</title>
    <link rel="stylesheet" href="{{ asset('css/hr.css') }}">
</head>
<body>
@php
    $titles = [
        'dashboard' => ['Kehadiran hari ini', 'Tinjau catatan terbaru dan kondisi perangkat yang mengirim data.'],
        'history' => ['Riwayat Absensi', 'Cari catatan masuk dan pulang berdasarkan karyawan atau tanggal.'],
        'employees' => ['Data Karyawan', 'Kelola akun dan foto acuan wajah karyawan.'],
        'devices' => ['Perangkat Kantor', 'Lihat perangkat yang pernah mengirim catatan absensi.'],
    ];
@endphp

<div class="brand-rule" aria-hidden="true"><span></span><span></span></div>
<header class="site-header">
    <div class="header-inner">
        <a class="brand-lockup" href="{{ route('dashboard') }}" aria-label="GJP Sistem Kehadiran, ke dashboard">
            <img class="brand-logo" src="{{ route('brand.logo') }}" alt="">
            <div class="brand-copy"><strong>Sistem Kehadiran</strong><span>Ruang kerja HR</span></div>
        </a>
        <nav class="primary-nav" aria-label="Navigasi utama">
            <a class="{{ $currentView === 'dashboard' ? 'active' : '' }}" href="{{ route('dashboard') }}" @if($currentView === 'dashboard') aria-current="page" @endif>Hari ini</a>
            <a class="{{ $currentView === 'history' ? 'active' : '' }}" href="{{ route('dashboard', ['view' => 'history']) }}" @if($currentView === 'history') aria-current="page" @endif>Riwayat</a>
            <a class="{{ $currentView === 'employees' ? 'active' : '' }}" href="{{ route('dashboard', ['view' => 'employees']) }}" @if($currentView === 'employees') aria-current="page" @endif>Karyawan</a>
            <a class="{{ $currentView === 'devices' ? 'active' : '' }}" href="{{ route('dashboard', ['view' => 'devices']) }}" @if($currentView === 'devices') aria-current="page" @endif>Perangkat</a>
        </nav>
        <div class="account-actions">
            <div class="account-copy"><strong>Administrator</strong><span>Human Resources</span></div>
            <form method="POST" action="{{ route('logout') }}" data-loading-form>@csrf<button type="submit" class="logout-button">Keluar</button></form>
        </div>
    </div>
</header>

<main class="page">
    <header class="page-heading">
        <div><h1>{{ $titles[$currentView][0] }}</h1><p>{{ $titles[$currentView][1] }}</p></div>
        <time class="page-date" datetime="{{ now()->toDateString() }}">{{ now()->format('d M Y') }} · WITA</time>
    </header>

    @if(session('success'))
        <div class="flash" role="status">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="form-errors" role="alert"><strong>Data belum dapat disimpan.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    @if($currentView === 'dashboard')
        <section class="overview" aria-labelledby="today-summary">
            <div class="overview-lead">
                <div><span class="overview-kicker" id="today-summary">Catatan terkirim hari ini</span><div class="overview-total">{{ $today->count() }}</div></div>
                <p>{{ $today->isEmpty() ? 'Belum ada perangkat yang mengirim catatan hari ini.' : 'Catatan masuk dan pulang dari aplikasi mobile GJP.' }}</p>
            </div>
            <div class="overview-breakdown">
                <div class="summary-item"><span>Masuk</span><strong>{{ $today->where('type', 'masuk')->count() }}</strong></div>
                <div class="summary-item"><span>Pulang</span><strong>{{ $today->where('type', 'pulang')->count() }}</strong></div>
                <div class="summary-item"><span>Karyawan aktif</span><strong>{{ $today->pluck('employee_name')->unique()->count() }}</strong></div>
                <div class="summary-item"><span>Perangkat aktif</span><strong>{{ $today->pluck('device_id')->unique()->count() }}</strong></div>
            </div>
        </section>
        <section class="panel" aria-labelledby="latest-heading">
            <div class="panel-header"><div><h2 id="latest-heading">Aktivitas terbaru</h2><p>Urutan berdasarkan waktu laporan terakhir.</p></div><a class="text-link" href="{{ route('dashboard', ['view' => 'history']) }}">Buka riwayat</a></div>
            @include('partials.attendance-table', ['items' => $attendances->take(8)])
        </section>

    @elseif($currentView === 'history')
        <section class="panel" aria-labelledby="history-heading">
            <form class="filters" method="GET" data-loading-form>
                <input type="hidden" name="view" value="history">
                <div class="field"><label for="search">Karyawan</label><input id="search" name="search" value="{{ request('search') }}" placeholder="Cari nama"></div>
                <div class="field"><label for="date">Tanggal</label><input id="date" type="date" name="date" value="{{ request('date') }}"></div>
                <div class="field"><label for="type">Jenis</label><select id="type" name="type"><option value="">Semua</option><option value="masuk" @selected(request('type') === 'masuk')>Masuk</option><option value="pulang" @selected(request('type') === 'pulang')>Pulang</option></select></div>
                <button class="button" type="submit">Terapkan filter</button>
                @if(request()->hasAny(['search', 'date', 'type']))<a class="button-secondary" href="{{ route('dashboard', ['view' => 'history']) }}">Reset</a>@endif
            </form>
            <div class="panel-header"><div><h2 id="history-heading">Hasil pencarian</h2><p>{{ $attendances->count() }} catatan ditemukan.</p></div><span class="status neutral">Sumber mobile</span></div>
            @include('partials.attendance-table', ['items' => $attendances])
        </section>

    @elseif($currentView === 'employees')
        <div class="employee-layout">
            <section class="panel" aria-labelledby="add-employee-heading">
                <div class="panel-header"><div><h2 id="add-employee-heading">Tambah karyawan</h2><p>ID perangkat tercatat otomatis saat karyawan login dari aplikasi.</p></div></div>
                <div class="panel-body">
                    <form class="employee-form" action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data" data-loading-form>
                        @csrf
                        <div class="field"><label for="name">Nama lengkap</label><input id="name" type="text" name="name" value="{{ old('name') }}" autocomplete="name" required></div>
                        <div class="field"><label for="username">Username</label><input id="username" type="text" name="username" value="{{ old('username') }}" autocomplete="off" required></div>
                        <div class="field"><label for="employee-password">Password aplikasi</label><input id="employee-password" type="password" name="password" minlength="4" autocomplete="new-password" required></div>
                        <div class="field"><label for="face-photo">Foto acuan wajah</label><input id="face-photo" type="file" name="face_photo" accept="image/jpeg,image/png,image/webp" required><span class="field-hint">JPG, PNG, atau WebP. Maksimal 5 MB. Disimpan privat sebagai data enrollment; pencocokan aktif setelah layanan model dihubungkan.</span></div>
                        <div class="form-actions"><button type="submit" class="button">Simpan karyawan</button></div>
                    </form>
                </div>
            </section>

            <section class="panel" aria-labelledby="employee-list-heading">
                <div class="panel-header"><div><h2 id="employee-list-heading">Karyawan terdaftar</h2><p>{{ $registeredEmployees->count() }} akun tersedia.</p></div></div>
                @if($registeredEmployees->isEmpty())
                    <div class="empty-state"><strong>Belum ada akun karyawan</strong><p>Isi formulir di samping untuk membuat akun pertama.</p></div>
                @else
                    <ul class="employee-list">
                        @foreach($registeredEmployees as $emp)
                            <li class="employee-row">
                                <div class="person">
                                    @if($emp->face_photo_path)<img class="face-thumb" src="{{ route('employees.face-photo.show', $emp) }}" alt="Foto acuan {{ $emp->name }}">@else<span class="person-avatar">{{ strtoupper(substr($emp->name, 0, 2)) }}</span>@endif
                                    <span><span class="person-name">{{ $emp->name }}</span><span class="person-meta">{{ $emp->username }}</span></span>
                                </div>
                                <span class="employee-device">{{ $emp->device_id ?: 'Terisi otomatis saat login' }}</span>
                                <form class="face-upload" action="{{ route('employees.face-photo.update', $emp) }}" method="POST" enctype="multipart/form-data" data-loading-form>
                                    @csrf @method('PUT')
                                    <label for="face-photo-{{ $emp->id }}">{{ $emp->face_photo_path ? 'Ganti foto acuan' : 'Tambahkan foto acuan' }}</label>
                                    <div><input id="face-photo-{{ $emp->id }}" type="file" name="face_photo" accept="image/jpeg,image/png,image/webp" required><button type="submit" class="button-secondary">Unggah</button></div>
                                </form>
                                <form action="{{ route('employees.destroy', $emp) }}" method="POST" onsubmit="return confirm('Hapus akun {{ addslashes($emp->name) }}?')" data-loading-form>@csrf @method('DELETE')<button type="submit" class="button-quiet danger">Hapus</button></form>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>

    @else
        <section class="device-list" aria-label="Daftar perangkat kantor">
            @forelse($devices as $deviceId => $records)
                @php($last = $records->first())
                <article class="device-row">
                    <div><div class="device-id">{{ $deviceId }}</div><div class="device-meta">Digunakan oleh {{ $last->employee_name }}</div></div>
                    <div class="device-meta">Laporan terakhir<br><strong>{{ $last->occurred_at->format('d M Y, H:i') }} WITA</strong></div>
                    <div class="device-stats"><strong>{{ $records->count() }}</strong><span>catatan absensi</span></div>
                </article>
            @empty
                <div class="panel"><div class="empty-state"><strong>Belum ada perangkat yang melapor</strong><p>Perangkat akan muncul setelah aplikasi mobile berhasil mengirim absensi.</p></div></div>
            @endforelse
        </section>
    @endif
</main>

<div class="loading-status" role="status" aria-live="polite">Memuat data...</div>
<script>
document.querySelectorAll('[data-loading-form]').forEach(form => form.addEventListener('submit', () => document.body.classList.add('busy')));
</script>
</body>
</html>
