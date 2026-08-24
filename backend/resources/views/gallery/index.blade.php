<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gallery Monitor – AbsenKu HR</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        :root {
            --green: #075a3e; --green2: #0b7452; --soft: #eaf4ef;
            --bg: #f0f2f0; --ink: #17201c; --muted: #6f7a74;
            --line: #e0e7e3; --orange: #d86421; --red: #c0392b;
            --red-soft: #fdebed; --warn-soft: #fff0e7;
            --card: #ffffff; --sidebar: linear-gradient(160deg,#06452f,#063724);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: var(--bg); color: var(--ink); font-family: 'Inter', Arial, sans-serif; font-size: 14px; }

        /* ── Layout ─────────────────────────────── */
        .layout { display: grid; grid-template-columns: 248px minmax(0,1fr); min-height: 100vh; }
        .sidebar { position: sticky; top: 0; height: 100vh; background: var(--sidebar); color: #fff; padding: 28px 20px; overflow-y: auto; }
        .brand { display: flex; align-items: center; gap: 12px; padding: 0 9px; font-size: 22px; font-weight: 800; }
        .brand-mark { display: grid; place-items: center; width: 40px; height: 44px; border: 2px solid #fff; border-radius: 10px 10px 19px 19px; }
        .brand small { display: block; margin-top: 3px; color: #aad1bf; font-size: 10px; font-weight: 400; letter-spacing: .5px; }
        .nav { display: grid; gap: 7px; margin-top: 47px; }
        .nav a { display: flex; align-items: center; gap: 12px; padding: 13px 14px; border-radius: 9px; color: #cce4d9; text-decoration: none; font-weight: 600; transition: all .2s; }
        .nav a:hover, .nav a.active { background: #fff; color: var(--green); }
        .nav-icon { display: grid; place-items: center; width: 23px; height: 23px; border: 1px solid currentColor; border-radius: 6px; font-size: 10px; }
        .sidebar-foot { margin-top: auto; padding-top: 30px; color: #9dc7b4; font-size: 11px; line-height: 1.6; }

        /* ── Main content ───────────────────────── */
        .content { min-width: 0; padding: 31px 38px; }
        .top-bar { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 24px; }
        .top-bar h1 { font-size: 24px; font-weight: 800; letter-spacing: -.5px; }
        .top-bar p { margin-top: 5px; color: var(--muted); font-size: 13px; }
        .admin-badge { display: flex; align-items: center; gap: 10px; }
        .avatar { display: grid; place-items: center; width: 42px; height: 42px; border-radius: 50%; background: var(--soft); color: var(--green); font-weight: 800; }

        /* ── Stat cards ─────────────────────────── */
        .stats { display: grid; grid-template-columns: repeat(4,minmax(0,1fr)); gap: 14px; margin-bottom: 22px; }
        .stat-card { background: var(--card); border: 1px solid var(--line); border-radius: 12px; padding: 18px; box-shadow: 0 4px 14px #0e3d2706; }
        .stat-label { font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: .5px; }
        .stat-value { font-size: 30px; font-weight: 800; margin: 8px 0 4px; }
        .stat-value.orange { color: var(--orange); }
        .stat-value.red { color: var(--red); }
        .stat-value.green { color: var(--green); }
        .stat-icon { float: right; width: 36px; height: 36px; border-radius: 9px; background: var(--soft); display: grid; place-items: center; font-size: 16px; }

        /* ── Filters ────────────────────────────── */
        .filters-bar { background: var(--card); border: 1px solid var(--line); border-radius: 12px; padding: 16px 20px; margin-bottom: 18px; display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end; }
        .filter-group { display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 150px; }
        .filter-group label { font-size: 11px; color: var(--muted); font-weight: 600; text-transform: uppercase; letter-spacing: .5px; }
        .filter-group select, .filter-group input { height: 38px; border: 1px solid var(--line); border-radius: 8px; padding: 0 10px; color: var(--ink); font-family: inherit; font-size: 13px; background: #fff; }
        .btn-filter { height: 38px; padding: 0 20px; border: none; border-radius: 8px; background: var(--green); color: #fff; font-weight: 700; cursor: pointer; font-family: inherit; }
        .btn-reset { height: 38px; padding: 0 14px; border: 1px solid var(--line); border-radius: 8px; background: #fff; color: var(--muted); font-weight: 600; cursor: pointer; font-family: inherit; text-decoration: none; display: inline-flex; align-items: center; }

        /* ── Flash ──────────────────────────────── */
        .flash { margin-bottom: 18px; padding: 13px 16px; border-radius: 9px; background: #dff3e8; color: var(--green); font-weight: 600; border: 1px solid #b2dfc6; }
        .flash.err { background: var(--red-soft); color: var(--red); border-color: #f5c6cb; }

        /* ── Gallery grid ───────────────────────── */
        .photo-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px; }
        .photo-card { background: var(--card); border: 1px solid var(--line); border-radius: 12px; overflow: hidden; box-shadow: 0 4px 14px #0e3d2706; transition: transform .2s, box-shadow .2s; position: relative; }
        .photo-card:hover { transform: translateY(-3px); box-shadow: 0 10px 30px #0e3d2714; }
        .photo-card.flagged { border-color: #f5c6cb; }
        .photo-card.ok { border-color: #b2dfc6; }

        .photo-thumb { width: 100%; aspect-ratio: 1/1; object-fit: cover; display: block; background: #f0f0f0; }
        .photo-thumb-placeholder { width: 100%; aspect-ratio: 1/1; background: linear-gradient(135deg, #e8ede9, #d4ddd6); display: grid; place-items: center; color: var(--muted); font-size: 32px; }

        .status-ribbon { position: absolute; top: 10px; left: 10px; padding: 3px 9px; border-radius: 20px; font-size: 10px; font-weight: 700; letter-spacing: .3px; }
        .status-ribbon.pending { background: #fff8e1; color: #8a6300; }
        .status-ribbon.flagged { background: #fdebed; color: var(--red); }
        .status-ribbon.ok      { background: #dff3e8; color: var(--green); }

        .photo-info { padding: 12px 14px; }
        .photo-employee { font-weight: 700; font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .photo-device { font-size: 11px; color: var(--muted); margin-top: 3px; }
        .photo-date { font-size: 10px; color: var(--muted); margin-top: 2px; }
        .photo-flag-note { margin-top: 8px; padding: 8px 10px; background: var(--red-soft); border-radius: 7px; font-size: 11px; color: var(--red); line-height: 1.4; }
        .photo-flag-note strong { display: block; font-size: 10px; text-transform: uppercase; letter-spacing: .4px; margin-bottom: 2px; }

        .photo-actions { padding: 10px 14px 14px; display: flex; gap: 6px; flex-wrap: wrap; }
        .btn { display: inline-flex; align-items: center; gap: 5px; padding: 6px 11px; border-radius: 7px; font-size: 11px; font-weight: 700; cursor: pointer; border: none; font-family: inherit; transition: all .15s; }
        .btn-danger { background: var(--red-soft); color: var(--red); }
        .btn-danger:hover { background: var(--red); color: #fff; }
        .btn-ok     { background: var(--soft); color: var(--green); }
        .btn-ok:hover { background: var(--green); color: #fff; }
        .btn-del    { background: #f0f2f0; color: var(--muted); }
        .btn-del:hover { background: #ddd; color: var(--ink); }
        .btn-view   { background: #f0f2f0; color: var(--ink); }
        .btn-view:hover { background: var(--ink); color: #fff; }

        /* ── Modal ──────────────────────────────── */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.7); z-index: 1000; place-items: center; }
        .modal-overlay.open { display: grid; }
        .modal { background: #fff; border-radius: 16px; width: 92vw; max-width: 520px; padding: 28px; box-shadow: 0 20px 60px rgba(0,0,0,.35); }
        .modal h3 { font-size: 17px; font-weight: 800; margin-bottom: 6px; }
        .modal p { color: var(--muted); font-size: 13px; margin-bottom: 18px; line-height: 1.5; }
        .modal img { width: 100%; border-radius: 10px; margin-bottom: 16px; max-height: 220px; object-fit: cover; }
        .modal label { font-size: 12px; font-weight: 600; color: var(--muted); text-transform: uppercase; display: block; margin-bottom: 6px; }
        .modal textarea { width: 100%; min-height: 90px; border: 1px solid var(--line); border-radius: 9px; padding: 10px 12px; font-size: 13px; font-family: inherit; resize: vertical; }
        .modal-actions { display: flex; gap: 10px; margin-top: 18px; justify-content: flex-end; }
        .btn-cancel { padding: 10px 18px; border: 1px solid var(--line); border-radius: 9px; background: #fff; cursor: pointer; font-family: inherit; font-weight: 600; }
        .btn-submit { padding: 10px 22px; border: none; border-radius: 9px; background: var(--red); color: #fff; cursor: pointer; font-family: inherit; font-weight: 700; }

        /* ── Lightbox ───────────────────────────── */
        .lightbox { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.92); z-index: 2000; place-items: center; cursor: zoom-out; }
        .lightbox.open { display: grid; }
        .lightbox img { max-width: 90vw; max-height: 90vh; border-radius: 10px; object-fit: contain; }

        /* ── Empty / pagination ─────────────────── */
        .empty-state { text-align: center; padding: 64px; color: var(--muted); }
        .empty-state .icon { font-size: 48px; margin-bottom: 12px; }
        .pagination { display: flex; gap: 6px; justify-content: center; margin-top: 28px; }
        .pagination a, .pagination span { display: inline-flex; align-items: center; justify-content: center; min-width: 36px; height: 36px; border-radius: 9px; font-size: 13px; font-weight: 600; text-decoration: none; border: 1px solid var(--line); color: var(--ink); background: #fff; padding: 0 10px; }
        .pagination a:hover { background: var(--soft); border-color: var(--green); color: var(--green); }
        .pagination .active { background: var(--green); color: #fff; border-color: var(--green); }

        @media (max-width: 1050px) {
            .stats { grid-template-columns: repeat(2,1fr); }
        }
        @media (max-width: 760px) {
            .layout { display: block; }
            .sidebar { position: static; height: auto; padding: 18px; }
            .nav { grid-template-columns: repeat(4,1fr); margin-top: 18px; gap: 4px; }
            .nav a { justify-content: center; padding: 10px 4px; font-size: 10px; }
            .nav-icon { display: none; }
            .content { padding: 18px 12px; }
            .admin-badge { display: none; }
            .stats { grid-template-columns: 1fr 1fr; }
            .photo-grid { grid-template-columns: repeat(auto-fill, minmax(160px,1fr)); }
        }
    </style>
</head>
<body>
<div class="layout">

    {{-- ── Sidebar ── --}}
    <aside class="sidebar">
        <div class="brand">
            <span class="brand-mark">A</span>
            <span>AbsenKu<small>HR CONTROL CENTER</small></span>
        </div>
        <nav class="nav">
            <a href="{{ route('dashboard') }}"><span class="nav-icon">DB</span>Dashboard</a>
            <a href="{{ route('dashboard', ['view' => 'history']) }}"><span class="nav-icon">RH</span>Riwayat</a>
            <a href="{{ route('dashboard', ['view' => 'employees']) }}"><span class="nav-icon">KR</span>Karyawan</a>
            <a href="{{ route('dashboard', ['view' => 'devices']) }}"><span class="nav-icon">HP</span>Perangkat</a>
            <a class="active" href="{{ route('gallery.index') }}"><span class="nav-icon">📷</span>Gallery Monitor</a>
        </nav>
        <div class="sidebar-foot">
            Foto tersimpan terenkripsi di server.<br>
            Akses terbatas hanya Administrator HR.
        </div>
    </aside>

    {{-- ── Main ── --}}
    <main class="content">

        <div class="top-bar">
            <div>
                <h1>📷 Gallery Monitor</h1>
                <p>Review foto dari HP karyawan & berikan teguran jika ditemukan konten tidak sesuai.</p>
            </div>
            <div class="admin-badge">
                <div class="avatar">HR</div>
                <div><b>Administrator</b><br><small style="color:var(--muted)">Human Resources</small></div>
            </div>
        </div>

        {{-- Flash --}}
        @if(session('success'))
            <div class="flash">✅ {{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="flash err">⚠️ {{ $errors->first() }}</div>
        @endif

        {{-- Stat cards --}}
        <div class="stats">
            <div class="stat-card">
                <span class="stat-icon">🖼️</span>
                <div class="stat-label">Total Foto</div>
                <div class="stat-value">{{ number_format($stats['total']) }}</div>
                <small style="color:var(--muted)">Seluruh perangkat</small>
            </div>
            <div class="stat-card">
                <span class="stat-icon">⏳</span>
                <div class="stat-label">Perlu Review</div>
                <div class="stat-value orange">{{ number_format($stats['pending']) }}</div>
                <small style="color:var(--muted)">Belum diperiksa</small>
            </div>
            <div class="stat-card">
                <span class="stat-icon">🚩</span>
                <div class="stat-label">Ditandai</div>
                <div class="stat-value red">{{ number_format($stats['flagged']) }}</div>
                <small style="color:var(--muted)">Perlu tindakan</small>
            </div>
            <div class="stat-card">
                <span class="stat-icon">✅</span>
                <div class="stat-label">Dinyatakan OK</div>
                <div class="stat-value green">{{ number_format($stats['ok']) }}</div>
                <small style="color:var(--muted)">Tidak bermasalah</small>
            </div>
        </div>

        {{-- Filters --}}
        <form class="filters-bar" method="get" action="{{ route('gallery.index') }}">
            <div class="filter-group">
                <label>Karyawan</label>
                <input name="employee" value="{{ request('employee') }}" placeholder="Cari nama...">
            </div>
            <div class="filter-group">
                <label>Perangkat</label>
                <select name="device">
                    <option value="">Semua perangkat</option>
                    @foreach($devices as $d)
                        <option value="{{ $d }}" @selected(request('device') === $d)>{{ $d }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>Status</label>
                <select name="status">
                    <option value="">Semua status</option>
                    <option value="pending" @selected(request('status') === 'pending')>⏳ Perlu Review</option>
                    <option value="flagged" @selected(request('status') === 'flagged')>🚩 Ditandai</option>
                    <option value="ok"      @selected(request('status') === 'ok')>✅ OK</option>
                </select>
            </div>
            <button class="btn-filter" type="submit">Terapkan</button>
            <a class="btn-reset" href="{{ route('gallery.index') }}">Reset</a>
        </form>

        {{-- Photo grid --}}
        @forelse($photos as $photo)
            @php($isFirst = $loop->first)
        @empty
        @endforelse

        @if($photos->isEmpty())
            <div class="empty-state">
                <div class="icon">📂</div>
                <p>Belum ada foto yang diunggah dari perangkat karyawan.</p>
            </div>
        @else
            <div class="photo-grid">
                @foreach($photos as $photo)
                    <div class="photo-card {{ $photo->status }}" id="photo-{{ $photo->id }}">

                        {{-- Status ribbon --}}
                        <span class="status-ribbon {{ $photo->status }}">
                            {{ $photo->status === 'pending' ? '⏳ Review' : ($photo->status === 'flagged' ? '🚩 Ditandai' : '✅ OK') }}
                        </span>

                        {{-- Thumbnail --}}
                        @if(Storage::disk('public')->exists($photo->file_path))
                            <img
                                class="photo-thumb"
                                src="{{ asset('storage/' . $photo->file_path) }}"
                                alt="Foto dari {{ $photo->employee_name }}"
                                loading="lazy"
                                onclick="openLightbox('{{ asset('storage/' . $photo->file_path) }}')"
                                style="cursor:zoom-in"
                            >
                        @else
                            <div class="photo-thumb-placeholder">🖼️</div>
                        @endif

                        {{-- Info --}}
                        <div class="photo-info">
                            <div class="photo-employee">{{ $photo->employee_name }}</div>
                            <div class="photo-device">📱 {{ $photo->device_id }}</div>
                            <div class="photo-date">
                                📅 {{ $photo->created_at->format('d M Y, H:i') }}
                                @if($photo->original_name)
                                    &nbsp;·&nbsp; {{ $photo->original_name }}
                                @endif
                            </div>

                            {{-- Flag note --}}
                            @if($photo->isFlagged() && $photo->flag_note)
                                <div class="photo-flag-note">
                                    <strong>🚩 Catatan Teguran</strong>
                                    {{ $photo->flag_note }}
                                    @if($photo->flagged_at)
                                        <br><span style="font-size:10px;opacity:.7">
                                            — {{ $photo->flagged_by }}, {{ $photo->flagged_at->format('d M Y H:i') }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="photo-actions">
                            @unless($photo->isFlagged())
                                <button
                                    class="btn btn-danger"
                                    onclick="openFlagModal({{ $photo->id }}, '{{ addslashes($photo->employee_name) }}', '{{ Storage::disk('public')->exists($photo->file_path) ? asset('storage/' . $photo->file_path) : '' }}')"
                                >🚩 Tegur</button>
                            @endunless

                            @unless($photo->isOk())
                                <form method="post" action="{{ route('gallery.approve', $photo) }}" style="display:inline">
                                    @csrf
                                    <button class="btn btn-ok" type="submit">✅ OK</button>
                                </form>
                            @endunless

                            <form method="post" action="{{ route('gallery.destroy', $photo) }}" style="display:inline" onsubmit="return confirm('Hapus foto ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-del" type="submit">🗑️</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Custom pagination --}}
            @if($photos->hasPages())
            <div class="pagination">
                @if($photos->onFirstPage())
                    <span style="opacity:.4">‹ Sebelum</span>
                @else
                    <a href="{{ $photos->previousPageUrl() }}">‹ Sebelum</a>
                @endif

                @foreach($photos->getUrlRange(max(1, $photos->currentPage()-2), min($photos->lastPage(), $photos->currentPage()+2)) as $page => $url)
                    @if($page == $photos->currentPage())
                        <span class="active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if($photos->hasMorePages())
                    <a href="{{ $photos->nextPageUrl() }}">Berikut ›</a>
                @else
                    <span style="opacity:.4">Berikut ›</span>
                @endif
            </div>
            @endif
        @endif

    </main>
</div>

{{-- ── Flag modal ── --}}
<div class="modal-overlay" id="flagModal">
    <div class="modal">
        <h3>🚩 Tandai & Berikan Teguran</h3>
        <p>
            Foto dari karyawan <strong id="modal-employee-name"></strong> akan ditandai dan catatan teguran akan disimpan untuk keperluan HR.
        </p>
        <img id="modal-preview" src="" alt="Preview" style="display:none">
        <form id="flagForm" method="post">
            @csrf
            <label>Catatan Teguran (wajib diisi)</label>
            <textarea name="flag_note" placeholder="Contoh: Terdapat foto yang tidak sesuai dengan kebijakan perusahaan. Karyawan diminta untuk menghapus konten tersebut dari perangkat kantor..." required></textarea>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeFlagModal()">Batal</button>
                <button type="submit" class="btn-submit">🚩 Tandai &amp; Simpan Teguran</button>
            </div>
        </form>
    </div>
</div>

{{-- ── Lightbox ── --}}
<div class="lightbox" id="lightbox" onclick="closeLightbox()">
    <img id="lightbox-img" src="" alt="Lightbox">
</div>

<script>
    // Flag modal
    function openFlagModal(photoId, employeeName, previewUrl) {
        const modal = document.getElementById('flagModal');
        document.getElementById('modal-employee-name').textContent = employeeName;
        document.getElementById('flagForm').action = '/gallery/' + photoId + '/flag';
        const preview = document.getElementById('modal-preview');
        if (previewUrl) { preview.src = previewUrl; preview.style.display = 'block'; }
        else { preview.style.display = 'none'; }
        modal.classList.add('open');
    }
    function closeFlagModal() {
        document.getElementById('flagModal').classList.remove('open');
    }
    document.getElementById('flagModal').addEventListener('click', function(e) {
        if (e.target === this) closeFlagModal();
    });

    // Lightbox
    function openLightbox(src) {
        document.getElementById('lightbox-img').src = src;
        document.getElementById('lightbox').classList.add('open');
    }
    function closeLightbox() {
        document.getElementById('lightbox').classList.remove('open');
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { closeFlagModal(); closeLightbox(); }
    });
</script>
</body>
</html>
