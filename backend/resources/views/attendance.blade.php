<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>AbsenKu HR</title>
<style>
:root{--green:#075a3e;--green2:#0b7452;--soft:#eaf4ef;--bg:#f4f7f5;--ink:#17201c;--muted:#6f7a74;--line:#e0e7e3;--orange:#d86421;--red:#b63f45}
*{box-sizing:border-box;margin:0;padding:0}
body{background:var(--bg);color:var(--ink);font:14px Arial,sans-serif}
.layout{display:grid;grid-template-columns:248px minmax(0,1fr);min-height:100vh}
.sidebar{position:sticky;top:0;height:100vh;overflow-y:auto;background:linear-gradient(160deg,#06452f,#063724);color:#fff;padding:28px 20px;display:flex;flex-direction:column}
.brand{display:flex;align-items:center;gap:12px;padding:0 9px;font-size:22px;font-weight:800}
.brand-mark{display:grid;place-items:center;width:40px;height:44px;border:2px solid #fff;border-radius:10px 10px 19px 19px}
.brand small{display:block;margin-top:3px;color:#aad1bf;font-size:10px;font-weight:400;letter-spacing:.5px}
.nav{display:grid;gap:7px;margin-top:47px}
.nav a{display:flex;align-items:center;gap:12px;padding:13px 14px;border-radius:9px;color:#cce4d9;text-decoration:none;font-weight:600;transition:all .15s}
.nav a:hover,.nav a.active{background:#fff;color:var(--green)}
.nav-icon{display:grid;place-items:center;width:23px;height:23px;border:1px solid currentColor;border-radius:6px;font-size:10px}
.sidebar-foot{margin-top:auto;padding-top:24px;color:#9dc7b4;font-size:11px;line-height:1.6}
.brand-mark-svg{display:grid;place-items:center;width:40px;height:44px;border:2px solid rgba(255,255,255,.5);border-radius:10px 10px 19px 19px;background:rgba(255,255,255,.1);flex-shrink:0}
.brand-mark-svg svg{width:22px;height:22px;stroke:#fff}
.content{min-width:0;padding:31px 38px}
.header{display:flex;align-items:center;justify-content:space-between}
.header h1{margin:0;font-size:25px;letter-spacing:-.5px}
.header p{margin:7px 0 0;color:var(--muted)}
.admin{display:flex;align-items:center;gap:11px}
.avatar{display:grid;place-items:center;width:42px;height:42px;border-radius:50%;background:var(--soft);color:var(--green);font-weight:800}
.admin small{display:block;margin-top:3px;color:var(--muted)}
.cards{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:15px;margin-top:27px}
.card,.panel{background:#fff;border:1px solid var(--line);border-radius:12px;box-shadow:0 5px 18px #163b2b08}
.metric{padding:18px}
.metric-label{color:var(--muted);font-size:12px}
.metric strong{display:block;margin-top:10px;font-size:27px}
.metric small{color:var(--muted)}
.metric-icon{float:right;display:grid;place-items:center;width:35px;height:35px;border-radius:9px;background:var(--soft);color:var(--green);font-weight:800}
.panel{margin-top:20px;overflow:hidden}
.panel-title{display:flex;align-items:center;justify-content:space-between;padding:19px 21px;border-bottom:1px solid var(--line)}
.panel-title h2{margin:0 0 5px;font-size:16px}
.muted{color:var(--muted);font-size:12px}
.badge{display:inline-block;padding:6px 9px;border-radius:20px;background:var(--soft);color:var(--green);font-size:10px;font-weight:700;white-space:nowrap}
.badge.warn{background:#fff0e7;color:var(--orange)}
.badge.danger{background:#fdebed;color:var(--red)}
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;white-space:nowrap}
th,td{padding:14px 20px;text-align:left;border-bottom:1px solid #edf0ee}
th{background:#fafcfb;color:var(--muted);font-size:10px;text-transform:uppercase;letter-spacing:.5px}
tbody tr:hover{background:#fbfdfc}
.person{display:flex;align-items:center;gap:10px}
.person-avatar{display:grid;place-items:center;width:34px;height:34px;border-radius:50%;background:var(--soft);color:var(--green);font-size:11px;font-weight:800}
.delete{border:0;background:none;color:var(--red);cursor:pointer}
.empty{padding:48px!important;text-align:center!important;color:var(--muted)}
.filters{display:grid;grid-template-columns:1.3fr 1fr 1fr auto;gap:10px;padding:16px 20px;border-bottom:1px solid var(--line)}
input,select,.filter-btn{height:40px;border:1px solid var(--line);border-radius:8px;background:#fff;padding:0 12px;color:var(--ink)}
.filter-btn{border-color:var(--green);background:var(--green);color:#fff;font-weight:700;cursor:pointer}
.employee-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;margin-top:22px}
.profile{padding:20px}
.profile-top{display:flex;align-items:center;gap:12px}
.profile .person-avatar{width:44px;height:44px}
.profile h3{margin:0 0 5px}
.profile-info{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:19px;padding-top:16px;border-top:1px solid var(--line)}
.profile-info strong{display:block;margin-bottom:4px}
.flash{margin-top:20px;padding:13px 16px;border-radius:9px;background:#dff3e8;color:var(--green)}

/* ── Perangkat halaman ─────────────────── */
.device-list{display:grid;gap:12px;margin-top:20px}
.device-card{background:#fff;border:1px solid var(--line);border-radius:12px;padding:18px 20px;display:flex;align-items:center;gap:16px;box-shadow:0 4px 14px #0e3d2706;cursor:pointer;transition:all .2s;text-decoration:none;color:inherit}
.device-card:hover{border-color:var(--green);box-shadow:0 8px 24px #075a3e15}
.device-card.selected{border-color:var(--green);background:var(--soft)}
.device-icon{display:grid;place-items:center;width:48px;height:48px;border-radius:12px;background:var(--soft);color:var(--green);font-size:22px;flex-shrink:0}
.device-info{flex:1;min-width:0}
.device-id-text{font-size:15px;font-weight:800}
.device-meta{font-size:12px;color:var(--muted);margin-top:3px}
.device-right{display:flex;flex-direction:column;align-items:flex-end;gap:6px}

/* ── Gallery panel ─────────────────────── */
.gallery-panel{margin-top:22px}
.gallery-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px}
.gallery-header h2{font-size:17px;font-weight:800}
.btn-dl-all{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border:none;border-radius:9px;background:var(--green);color:#fff;font-weight:700;cursor:pointer;font-size:13px;text-decoration:none}
.photo-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px}
.photo-wrap{position:relative;border-radius:10px;overflow:hidden;border:1px solid var(--line);background:#f0f0f0;aspect-ratio:1/1;cursor:zoom-in}
.photo-wrap img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .2s}
.photo-wrap:hover img{transform:scale(1.04)}
.photo-overlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.6) 0%,transparent 50%);opacity:0;transition:opacity .2s;display:flex;align-items:flex-end;padding:10px}
.photo-wrap:hover .photo-overlay{opacity:1}
.photo-actions-bar{display:flex;gap:6px}
.btn-sm{padding:5px 9px;border:none;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer}
.btn-dl{background:#fff;color:var(--ink)}
.btn-flag-sm{background:#fdebed;color:var(--red)}
.btn-ok-sm{background:#dff3e8;color:var(--green)}
.photo-ribbon{position:absolute;top:8px;left:8px;padding:3px 8px;border-radius:12px;font-size:10px;font-weight:700}
.ribbon-flagged{background:#fdebed;color:var(--red)}
.ribbon-ok{background:#dff3e8;color:var(--green)}
.photo-date{color:#fff;font-size:10px;margin-left:auto;opacity:.85}

/* ── Modal flag ────────────────────────── */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.65);z-index:999;place-items:center}
.modal-overlay.open{display:grid}
.modal{background:#fff;border-radius:16px;width:92vw;max-width:480px;padding:28px;box-shadow:0 20px 60px rgba(0,0,0,.3)}
.modal h3{font-size:17px;font-weight:800;margin-bottom:8px}
.modal p{color:var(--muted);font-size:13px;margin-bottom:16px;line-height:1.5}
.modal img{width:100%;border-radius:9px;margin-bottom:14px;max-height:200px;object-fit:cover}
.modal label{font-size:11px;font-weight:700;text-transform:uppercase;color:var(--muted);display:block;margin-bottom:6px;letter-spacing:.4px}
.modal textarea{width:100%;min-height:80px;border:1px solid var(--line);border-radius:8px;padding:10px 12px;font-size:13px;font-family:inherit;resize:vertical}
.modal-actions{display:flex;gap:8px;justify-content:flex-end;margin-top:16px}
.btn-cancel{padding:9px 16px;border:1px solid var(--line);border-radius:8px;background:#fff;cursor:pointer;font-weight:600}
.btn-submit-flag{padding:9px 18px;border:none;border-radius:8px;background:var(--red);color:#fff;cursor:pointer;font-weight:700}

/* ── Lightbox ──────────────────────────── */
.lightbox{display:none;position:fixed;inset:0;background:rgba(0,0,0,.93);z-index:1000;place-items:center;cursor:zoom-out}
.lightbox.open{display:grid}
.lightbox img{max-width:90vw;max-height:90vh;border-radius:10px;object-fit:contain}
.lightbox-info{position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:rgba(255,255,255,.12);backdrop-filter:blur(8px);color:#fff;padding:10px 20px;border-radius:30px;font-size:12px;white-space:nowrap}

@media(max-width:1050px){.cards{grid-template-columns:repeat(2,1fr)}.employee-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:760px){.layout{display:block}.sidebar{position:static;height:auto;padding:18px}.brand small,.sidebar-foot{display:none}.nav{grid-template-columns:repeat(5,1fr);margin-top:18px;gap:4px}.nav a{justify-content:center;padding:10px 4px;font-size:10px}.nav-icon{display:none}.content{padding:22px 14px}.admin{display:none}.cards,.employee-grid{grid-template-columns:1fr}.filters{grid-template-columns:1fr}.header h1{font-size:21px}}
</style>
</head>
<body>
@php
    $titles = [
        'dashboard' => ['Dashboard', 'Ringkasan absensi dari seluruh perangkat kantor.'],
        'history'   => ['Riwayat Absensi', 'Cari dan periksa aktivitas masuk maupun pulang.'],
        'employees' => ['Data Karyawan', 'Ringkasan aktivitas karyawan dari aplikasi mobile.'],
        'devices'   => ['Perangkat Kantor', 'Pantau & kontrol galeri HP kantor yang dipakai karyawan.'],
    ];
@endphp
<div class="layout">
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-mark-svg">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M7 3H5a2 2 0 0 0-2 2v2"/>
                    <path d="M17 3h2a2 2 0 0 1 2 2v2"/>
                    <path d="M7 21H5a2 2 0 0 1-2-2v-2"/>
                    <path d="M17 21h2a2 2 0 0 0 2-2v-2"/>
                    <circle cx="12" cy="10" r="3"/>
                    <path d="M8 17c0-2.21 1.79-4 4-4s4 1.79 4 4"/>
                </svg>
            </div>
            <span>AbsenKu<small>HR CONTROL CENTER</small></span>
        </div>
        <nav class="nav">
            <a class="{{ $currentView === 'dashboard' ? 'active' : '' }}" href="{{ route('dashboard') }}"><span class="nav-icon">DB</span>Dashboard</a>
            <a class="{{ $currentView === 'history'   ? 'active' : '' }}" href="{{ route('dashboard', ['view'=>'history']) }}"><span class="nav-icon">RH</span>Riwayat</a>
            <a class="{{ $currentView === 'employees' ? 'active' : '' }}" href="{{ route('dashboard', ['view'=>'employees']) }}"><span class="nav-icon">KR</span>Karyawan</a>
            <a class="{{ $currentView === 'devices'   ? 'active' : '' }}" href="{{ route('dashboard', ['view'=>'devices']) }}"><span class="nav-icon">HP</span>Perangkat</a>
        </nav>
        <div class="sidebar-foot">Galeri karyawan tersimpan di server.<br>Hanya dapat diakses Administrator HR.</div>
        <form method="POST" action="{{ route('logout') }}" style="margin-top:16px">
            @csrf
            <button type="submit" style="width:100%;padding:11px 14px;border-radius:9px;border:1px solid rgba(255,255,255,.2);background:rgba(255,255,255,.07);color:#cce4d9;font-size:13px;font-weight:600;cursor:pointer;text-align:left;display:flex;align-items:center;gap:10px;transition:background .15s" onmouseover="this.style.background='rgba(255,255,255,.14)'" onmouseout="this.style.background='rgba(255,255,255,.07)'">
                <span style="display:grid;place-items:center;width:23px;height:23px;border:1px solid currentColor;border-radius:6px;font-size:10px;flex-shrink:0">↩</span>
                Keluar
            </button>
        </form>
    </aside>

    <main class="content">
        <header class="header">
            <div><h1>{{ $titles[$currentView][0] }}</h1><p>{{ $titles[$currentView][1] }}</p></div>
            <div class="admin"><div class="avatar">HR</div><div><b>Administrator</b><small>Human Resources</small></div></div>
        </header>
        @if(session('success'))<div class="flash">✅ {{ session('success') }}</div>@endif

        @if($currentView === 'dashboard')
            <section class="cards">
                <article class="card metric"><span class="metric-icon">IN</span><span class="metric-label">Masuk hari ini</span><strong>{{ $today->where('type','masuk')->count() }}</strong><small>Catatan mobile</small></article>
                <article class="card metric"><span class="metric-icon">OUT</span><span class="metric-label">Pulang hari ini</span><strong>{{ $today->where('type','pulang')->count() }}</strong><small>Catatan mobile</small></article>
                <article class="card metric"><span class="metric-icon">KR</span><span class="metric-label">Karyawan terpantau</span><strong>{{ $employees->count() }}</strong><small>Dengan aktivitas</small></article>
                <article class="card metric"><span class="metric-icon">HP</span><span class="metric-label">Perangkat aktif</span><strong>{{ $devices->count() }}</strong><small>Perangkat terdaftar</small></article>
            </section>
            <section class="panel">
                <div class="panel-title"><div><h2>Aktivitas terbaru</h2><span class="muted">Data langsung dari aplikasi mobile</span></div><a class="badge" href="{{ route('dashboard',['view'=>'history']) }}">Lihat semua</a></div>
                @include('partials.attendance-table', ['items' => $attendances->take(8)])
            </section>

        @elseif($currentView === 'history')
            <section class="panel">
                <form class="filters" method="get"><input type="hidden" name="view" value="history"><input name="search" value="{{ request('search') }}" placeholder="Cari nama karyawan"><input type="date" name="date" value="{{ request('date') }}"><select name="type"><option value="">Semua jenis</option><option value="masuk" @selected(request('type')==='masuk')>Masuk</option><option value="pulang" @selected(request('type')==='pulang')>Pulang</option></select><button class="filter-btn">Terapkan</button></form>
                <div class="panel-title"><div><h2>Hasil riwayat</h2><span class="muted">{{ $attendances->count() }} data ditemukan</span></div><span class="badge">Mobile only</span></div>
                @include('partials.attendance-table', ['items' => $attendances])
            </section>

        @elseif($currentView === 'employees')
            <section class="panel" style="margin-bottom: 24px; padding: 24px;">
                <h3 style="margin-bottom: 16px;">Tambah Karyawan Baru</h3>
                <form action="{{ route('employees.store') }}" method="POST" style="display: flex; gap: 12px; align-items: flex-end;">
                    @csrf
                    <div class="field" style="flex:1; margin-bottom: 0;">
                        <label>Nama Lengkap</label>
                        <input type="text" name="name" required style="width:100%; padding: 8px 12px; border:1px solid var(--line); border-radius:6px;">
                    </div>
                    <div class="field" style="flex:1; margin-bottom: 0;">
                        <label>Username (Login HP)</label>
                        <input type="text" name="username" required style="width:100%; padding: 8px 12px; border:1px solid var(--line); border-radius:6px;">
                    </div>
                    <div class="field" style="flex:1; margin-bottom: 0;">
                        <label>Password (Login HP)</label>
                        <input type="text" name="password" required style="width:100%; padding: 8px 12px; border:1px solid var(--line); border-radius:6px;">
                    </div>
                    <div class="field" style="flex:1; margin-bottom: 0;">
                        <label>ID HP Kantor (Opsional)</label>
                        <input type="text" name="device_id" placeholder="Otomatis deteksi saat login" style="width:100%; padding: 8px 12px; border:1px solid var(--line); border-radius:6px;">
                    </div>
                    <button type="submit" style="padding: 10px 16px; background: var(--green); color: #fff; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">Simpan</button>
                </form>
            </section>
            
            <section class="employee-grid">
                @forelse($registeredEmployees as $emp)
                    <article class="card profile">
                        <div class="profile-top">
                            <div class="person-avatar">{{ strtoupper(substr($emp->name,0,2)) }}</div>
                            <div>
                                <h3>{{ $emp->name }}</h3>
                                <span class="muted">{{ $emp->username }} &middot; {{ $emp->device_id }}</span>
                            </div>
                            <form action="{{ route('employees.destroy', $emp->id) }}" method="POST" style="margin-left:auto" onsubmit="return confirm('Hapus karyawan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="delete" title="Hapus">🗑️</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="panel empty" style="grid-column: 1 / -1;">Belum ada karyawan terdaftar.</div>
                @endforelse
            </section>

        @else
            {{-- ═══════════════════════════════════════════════
                 HALAMAN PERANGKAT + GALLERY KONTROL
            ════════════════════════════════════════════════ --}}

            {{-- Daftar perangkat (klik untuk lihat galeri) --}}
            <div class="device-list">
                @forelse($devices as $deviceId => $records)
                    @php
                        $last    = $records->first();
                        $gStat   = $galleryStats[$deviceId] ?? null;
                    @endphp
                    <a class="device-card {{ $selectedDevice === $deviceId ? 'selected' : '' }}"
                       href="{{ route('dashboard', ['view'=>'devices','device'=>$deviceId]) }}">
                        <div class="device-icon">📱</div>
                        <div class="device-info">
                            <div class="device-id-text">{{ $deviceId }}</div>
                            <div class="device-meta">
                                👤 {{ $last->employee_name }}
                                &nbsp;·&nbsp; 🕐 Terakhir: {{ $last->occurred_at->format('d M Y, H:i') }}
                            </div>
                        </div>
                        <div class="device-right">
                            <span class="badge {{ $last->photo_access_granted ? '' : 'warn' }}">
                                {{ $last->photo_access_granted ? '✅ Galeri aktif' : '⚠️ Belum ada galeri' }}
                            </span>
                            @if($gStat)
                                <span style="font-size:11px;color:var(--muted)">
                                    📷 {{ $gStat->total }} foto
                                    @if($gStat->flagged > 0)
                                        &nbsp;<span style="color:var(--red);font-weight:700">🚩 {{ $gStat->flagged }} ditandai</span>
                                    @endif
                                </span>
                            @else
                                <span style="font-size:11px;color:var(--muted)">📷 Belum ada foto</span>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="panel empty">Belum ada perangkat yang melapor.</div>
                @endforelse
            </div>

            {{-- ── Gallery viewer (tampil jika device dipilih) ── --}}
            @if($selectedDevice)
                <div class="gallery-panel">
                    <div class="gallery-header">
                        <div>
                            <h2>📷 Galeri — {{ $selectedDevice }}</h2>
                            <span style="color:var(--muted);font-size:12px">
                                {{ $galleryPhotos->count() }} foto tersimpan
                                @if($galleryPhotos->where('status','flagged')->count() > 0)
                                    &nbsp;·&nbsp; <span style="color:var(--red);font-weight:700">🚩 {{ $galleryPhotos->where('status','flagged')->count() }} ditandai</span>
                                @endif
                            </span>
                        </div>
                        @if($galleryPhotos->isNotEmpty())
                            <a class="btn-dl-all"
                               href="{{ route('gallery.download-zip', ['device'=>$selectedDevice]) }}"
                               title="Download semua foto sebagai ZIP">
                                ⬇️ Download Semua
                            </a>
                        @endif
                    </div>

                    @if($galleryPhotos->isEmpty())
                        <div class="panel empty" style="margin-top:0">
                            Galeri dari perangkat ini belum tersinkron.<br>
                            <span style="font-size:12px">Foto akan muncul otomatis setelah karyawan melakukan absensi.</span>
                        </div>
                    @else
                        <div class="photo-grid">
                            @foreach($galleryPhotos as $photo)
                                <div class="photo-wrap" id="pw-{{ $photo->id }}">
                                    @if(\Illuminate\Support\Facades\Storage::disk('public')->exists($photo->file_path))
                                        @php $ext = strtolower(pathinfo($photo->file_path, PATHINFO_EXTENSION)); @endphp
                                        @if(in_array($ext, ['mp4','webm','mov','avi','mkv']))
                                            <video src="{{ asset('storage/'.$photo->file_path) }}" 
                                                   style="width:100%;height:100%;object-fit:cover;display:block;" 
                                                   controls preload="metadata"></video>
                                        @else
                                            <img
                                                src="{{ asset('storage/'.$photo->file_path) }}"
                                                alt="{{ $photo->original_name }}"
                                                loading="lazy"
                                                onclick="openLightbox('{{ asset('storage/'.$photo->file_path) }}','{{ addslashes($photo->employee_name) }}','{{ $photo->created_at->format('d M Y, H:i') }}')"
                                            >
                                        @endif
                                    @else
                                        <div style="width:100%;height:100%;display:grid;place-items:center;font-size:28px;color:var(--muted)">🖼️</div>
                                    @endif

                                    {{-- Status ribbon --}}
                                    @if($photo->status === 'flagged')
                                        <span class="photo-ribbon ribbon-flagged">🚩 Ditandai</span>
                                    @elseif($photo->status === 'ok')
                                        <span class="photo-ribbon ribbon-ok">✅ OK</span>
                                    @endif

                                    {{-- Hover overlay actions --}}
                                    <div class="photo-overlay">
                                        <div class="photo-actions-bar">
                                            @if(\Illuminate\Support\Facades\Storage::disk('public')->exists($photo->file_path))
                                                <a class="btn-sm btn-dl"
                                                   href="{{ asset('storage/'.$photo->file_path) }}"
                                                   download="{{ $photo->original_name ?? 'foto.jpg' }}"
                                                   onclick="event.stopPropagation()">⬇️</a>
                                            @endif
                                            @unless($photo->status === 'flagged')
                                                <button class="btn-sm btn-flag-sm" onclick="event.stopPropagation();openFlag({{ $photo->id }},'{{ addslashes($photo->employee_name) }}','{{ \Illuminate\Support\Facades\Storage::disk('public')->exists($photo->file_path) ? asset('storage/'.$photo->file_path) : '' }}')">🚩 Tegur</button>
                                            @endunless
                                            @unless($photo->status === 'ok')
                                                <form method="post" action="{{ route('gallery.approve',$photo) }}" style="display:inline" onclick="event.stopPropagation()">
                                                    @csrf
                                                    <button class="btn-sm btn-ok-sm" type="submit">✅</button>
                                                </form>
                                            @endunless
                                        </div>
                                        <span class="photo-date">{{ $photo->created_at->format('d M') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        @endif
    </main>
</div>

{{-- ── Modal Flag ── --}}
<div class="modal-overlay" id="flagModal">
    <div class="modal">
        <h3>🚩 Tandai & Catat Teguran</h3>
        <p>Foto dari karyawan <strong id="m-name"></strong> akan ditandai. Catatan teguran disimpan untuk keperluan HR.</p>
        <img id="m-preview" src="" alt="" style="display:none">
        <form id="flagForm" method="post">
            @csrf
            <label>Catatan Teguran</label>
            <textarea name="flag_note" placeholder="Contoh: Terdapat konten tidak sesuai kebijakan perusahaan..." required></textarea>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeFlag()">Batal</button>
                <button type="submit" class="btn-submit-flag">🚩 Simpan Teguran</button>
            </div>
        </form>
    </div>
</div>

{{-- ── Lightbox ── --}}
<div class="lightbox" id="lightbox" onclick="closeLightbox()">
    <img id="lb-img" src="" alt="">
    <div class="lightbox-info" id="lb-info"></div>
</div>

<script>
function openFlag(id, name, preview) {
    document.getElementById('m-name').textContent = name;
    document.getElementById('flagForm').action = '/gallery/' + id + '/flag';
    const p = document.getElementById('m-preview');
    if (preview) { p.src = preview; p.style.display = 'block'; }
    else p.style.display = 'none';
    document.getElementById('flagModal').classList.add('open');
}
function closeFlag() { document.getElementById('flagModal').classList.remove('open'); }
document.getElementById('flagModal').addEventListener('click', e => { if(e.target===e.currentTarget) closeFlag(); });

function openLightbox(src, name, date) {
    document.getElementById('lb-img').src = src;
    document.getElementById('lb-info').textContent = name + ' · ' + date;
    document.getElementById('lightbox').classList.add('open');
}
function closeLightbox() { document.getElementById('lightbox').classList.remove('open'); }
document.addEventListener('keydown', e => { if(e.key==='Escape'){closeFlag();closeLightbox();} });
</script>
</body>
</html>
