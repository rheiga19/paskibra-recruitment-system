@extends('layouts.app')
@section('title', 'Scan Absensi — ' . $jadwal->nama)

@section('content')
<div class="section-header">
    <h1><i class="fas fa-qrcode mr-2"></i>Scan Absensi</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('admin.absensi.index') }}">Absensi</a></div>
        <div class="breadcrumb-item active">Scan</div>
    </div>
</div>

{{-- Info jadwal --}}
<div class="alert alert-primary d-flex align-items-center mb-4">
    <i class="fas fa-calendar-day fa-2x mr-3"></i>
    <div>
        <strong>{{ $jadwal->nama }}</strong> &nbsp;·&nbsp;
        {{ $jadwal->tanggal->translatedFormat('l, d F Y') }} &nbsp;·&nbsp;
        {{ $jadwal->jam_masuk }} – {{ $jadwal->jam_pulang }}
        @if($jadwal->lokasi) &nbsp;·&nbsp; 📍 {{ $jadwal->lokasi }} @endif
    </div>
</div>

<div class="row">

    {{-- ── Panel Scan ── --}}
    <div class="col-lg-5">

        {{-- Toggle masuk/pulang --}}
        <div class="card mb-3">
            <div class="card-body py-3">
                <div class="d-flex">
                    <button id="btnMasuk" onclick="setTipe('masuk')"
                            class="btn btn-success btn-lg flex-fill font-weight-bold mr-2">
                        📥 ABSEN MASUK
                    </button>
                    <button id="btnPulang" onclick="setTipe('pulang')"
                            class="btn btn-outline-danger btn-lg flex-fill font-weight-bold">
                        📤 ABSEN PULANG
                    </button>
                </div>
                <div id="tipeLabel" class="text-center mt-2 font-weight-bold text-success" style="font-size:14px;">
                    Mode: MASUK
                </div>
            </div>
        </div>

        {{-- Kamera scan QR --}}
        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0"><i class="fas fa-camera mr-2"></i>Scan via Kamera</h5></div>
            <div class="card-body text-center">
                <div id="reader" style="width:100%;max-width:320px;margin:0 auto;border-radius:12px;overflow:hidden;"></div>
                <div class="mt-2">
                    <button id="btnStartScan" onclick="startScan()" class="btn btn-primary btn-sm">
                        <i class="fas fa-play mr-1"></i> Mulai Kamera
                    </button>
                    <button id="btnStopScan" onclick="stopScan()" class="btn btn-secondary btn-sm d-none">
                        <i class="fas fa-stop mr-1"></i> Stop
                    </button>
                </div>
            </div>
        </div>

        {{-- Input Manual --}}
        <div class="card">
            <div class="card-header"><h5 class="mb-0"><i class="fas fa-keyboard mr-2"></i>Input Manual</h5></div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success py-2">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                <div class="alert alert-danger py-2">{{ session('error') }}</div>
                @endif
                @if(session('info'))
                <div class="alert alert-info py-2">{{ session('info') }}</div>
                @endif

                <form action="{{ route('admin.absensi.manual', $jadwal) }}" method="POST">
                    @csrf
                    <div class="input-group mb-2">
                        <input type="text" name="no_pendaftaran" class="form-control form-control-lg"
                               placeholder="No. Pendaftaran / scan kartu..."
                               autocomplete="off" autofocus id="inputManual">
                        <div class="input-group-append">
                            <select name="tipe" class="form-control form-control-lg">
                                <option value="masuk">Masuk</option>
                                <option value="pulang">Pulang</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-check mr-1"></i> Catat Absensi
                    </button>
                </form>
                <small class="text-muted d-block mt-2">
                    Ketik no. pendaftaran atau arahkan scanner barcode ke field ini.
                </small>
            </div>
        </div>
    </div>

    {{-- ── Log + Daftar Peserta ── --}}
    <div class="col-lg-7">

        {{-- Notifikasi hasil scan --}}
        <div id="scanResult" class="d-none mb-3"></div>

        {{-- Log scan realtime --}}
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <h5 class="mb-0"><i class="fas fa-history mr-2"></i>Log Scan Hari Ini</h5>
                <button onclick="clearLog()" class="btn btn-xs btn-outline-secondary">Bersihkan</button>
            </div>
            <div id="scanLog" style="max-height:200px;overflow-y:auto;padding:12px;">
                <div class="text-muted text-center py-3" id="logEmpty">Belum ada scan.</div>
            </div>
        </div>

        {{-- Daftar peserta + status --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-users mr-2"></i>Daftar Peserta</h5>
                <div>
                    <span class="badge badge-success mr-1">
                        {{ $absensiList->where('status','hadir')->count() }} Hadir
                    </span>
                    <span class="badge badge-warning mr-1">
                        {{ $absensiList->where('status','izin')->count() }} Izin
                    </span>
                    <span class="badge badge-info mr-1">
                        {{ $absensiList->where('status','sakit')->count() }} Sakit
                    </span>
                    <span class="badge badge-danger">
                        {{ $absensiList->where('status','alpha')->count() }} Alpha
                    </span>
                </div>
            </div>
            <div class="table-responsive" style="max-height:400px;overflow-y:auto;">
                <table class="table table-sm mb-0">
                    <thead class="thead-light sticky-top">
                        <tr>
                            <th>Nama</th>
                            <th>No. Kartu</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Masuk</th>
                            <th class="text-center">Pulang</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyPeserta">
                        @foreach($absensiList as $abs)
                        <tr id="row-{{ $abs->pendaftaran->no_pendaftaran }}">
                            <td>
                                <div class="font-weight-bold" style="font-size:13px;">
                                    {{ $abs->pendaftaran->nama_lengkap }}
                                </div>
                                <small class="text-muted">
                                    {{ $abs->pendaftaran->jenis_kelamin === 'L' ? '♂' : '♀' }}
                                </small>
                            </td>
                            <td><code style="font-size:12px;">{{ $abs->pendaftaran->no_pendaftaran }}</code></td>
                            <td class="text-center">
                                <span class="badge badge-{{ $abs->badgeStatus() }}"
                                      id="badge-{{ $abs->pendaftaran->no_pendaftaran }}">
                                    {{ $abs->labelStatus() }}
                                </span>
                                {{-- Tampilkan label telat jika izin dengan waktu masuk --}}
                                @if($abs->status === 'izin' && $abs->waktu_masuk)
                                <br><small class="text-warning" style="font-size:10px;">telat</small>
                                @endif
                            </td>
                            <td class="text-center" id="masuk-{{ $abs->pendaftaran->no_pendaftaran }}"
                                style="font-size:13px;">
                                {{ $abs->waktu_masuk ? $abs->waktu_masuk->format('H:i') : '-' }}
                            </td>
                            <td class="text-center" id="pulang-{{ $abs->pendaftaran->no_pendaftaran }}"
                                style="font-size:13px;">
                                {{ $abs->waktu_pulang ? $abs->waktu_pulang->format('H:i') : '-' }}
                            </td>
                            <td class="text-center">
                                <button class="btn btn-xs btn-outline-secondary"
                                        data-toggle="modal"
                                        data-target="#modal{{ $abs->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>

                        {{-- Modal edit status --}}
                        <div class="modal fade" id="modal{{ $abs->id }}" tabindex="-1">
                            <div class="modal-dialog modal-sm">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h6 class="modal-title">
                                            <i class="fas fa-edit mr-1"></i>
                                            {{ $abs->pendaftaran->nama_lengkap }}
                                        </h6>
                                        <button type="button" class="close" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('admin.absensi.update-status', $abs) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <div class="modal-body">

                                            {{-- Info waktu tercatat --}}
                                            @if($abs->waktu_masuk || $abs->waktu_pulang)
                                            <div class="alert alert-light py-2 mb-3" style="font-size:12px;">
                                                @if($abs->waktu_masuk)
                                                <div>🕐 Masuk: <strong>{{ $abs->waktu_masuk->format('H:i') }}</strong></div>
                                                @endif
                                                @if($abs->waktu_pulang)
                                                <div>🕐 Pulang: <strong>{{ $abs->waktu_pulang->format('H:i') }}</strong></div>
                                                @endif
                                            </div>
                                            @endif

                                            <div class="form-group">
                                                <label class="font-weight-bold">Status</label>
                                                <select name="status" class="form-control" id="statusSelect{{ $abs->id }}"
                                                        onchange="cekStatusModal({{ $abs->id }})">
                                                    <option value="hadir" {{ $abs->status=='hadir' ? 'selected':'' }}>
                                                        ✅ Hadir
                                                    </option>
                                                    <option value="izin"  {{ $abs->status=='izin'  ? 'selected':'' }}>
                                                        🕐 Izin (bisa telat atau tidak hadir)
                                                    </option>
                                                    <option value="sakit" {{ $abs->status=='sakit' ? 'selected':'' }}>
                                                        🤒 Sakit
                                                    </option>
                                                    <option value="alpha" {{ $abs->status=='alpha' ? 'selected':'' }}>
                                                        ❌ Alpha
                                                    </option>
                                                </select>
                                            </div>

                                            {{-- Peringatan sakit/alpha hapus waktu --}}
                                            <div id="warnModal{{ $abs->id }}"
                                                 class="alert alert-warning py-2 mb-2 d-none"
                                                 style="font-size:12px;">
                                                ⚠️ Status <strong>Sakit</strong> dan <strong>Alpha</strong>
                                                akan menghapus waktu masuk &amp; pulang yang sudah tercatat.
                                            </div>

                                            <div class="form-group mb-0">
                                                <label class="font-weight-bold">Keterangan</label>
                                                <textarea name="keterangan" class="form-control" rows="2"
                                                          placeholder="Opsional...">{{ $abs->keterangan }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary btn-sm"
                                                    data-dismiss="modal">Batal</button>
                                            <button class="btn btn-primary btn-sm">
                                                <i class="fas fa-save mr-1"></i> Simpan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Keterangan badge --}}
            <div class="card-footer py-2" style="font-size:12px;">
                <span class="badge badge-success mr-1">H</span> Hadir &nbsp;
                <span class="badge badge-warning mr-1">I</span> Izin &nbsp;
                <span class="badge badge-info mr-1">S</span> Sakit &nbsp;
                <span class="badge badge-danger mr-1">A</span> Alpha
                &nbsp;·&nbsp; Izin + <small class="text-warning">telat</small> = masuk tapi lewat jam
            </div>
        </div>
    </div>
</div>

{{-- Library QR Scanner --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
<script>
let html5QrCode = null;
let tipe        = 'masuk';
const jadwalId  = {{ $jadwal->id }};
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// ── Badge color map ──────────────────────────────────────────────────
const badgeMap = {
    hadir : { cls: 'badge-success', label: 'Hadir' },
    izin  : { cls: 'badge-warning', label: 'Izin'  },
    sakit : { cls: 'badge-info',    label: 'Sakit'  },
    alpha : { cls: 'badge-danger',  label: 'Alpha'  },
};

// ── Toggle mode masuk/pulang ─────────────────────────────────────────
function setTipe(t) {
    tipe = t;
    document.getElementById('btnMasuk').className = t === 'masuk'
        ? 'btn btn-success btn-lg flex-fill font-weight-bold mr-2'
        : 'btn btn-outline-success btn-lg flex-fill font-weight-bold mr-2';
    document.getElementById('btnPulang').className = t === 'pulang'
        ? 'btn btn-danger btn-lg flex-fill font-weight-bold'
        : 'btn btn-outline-danger btn-lg flex-fill font-weight-bold';
    document.getElementById('tipeLabel').textContent = 'Mode: ' + t.toUpperCase();
    document.getElementById('tipeLabel').className   = t === 'masuk'
        ? 'text-center mt-2 font-weight-bold text-success'
        : 'text-center mt-2 font-weight-bold text-danger';
    document.querySelector('select[name="tipe"]').value = t;
}

// ── Kamera ───────────────────────────────────────────────────────────
function startScan() {
    html5QrCode = new Html5Qrcode("reader");
    html5QrCode.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: { width: 250, height: 250 } },
        (decodedText) => prosesQr(decodedText),
        (err) => {}
    ).then(() => {
        document.getElementById('btnStartScan').classList.add('d-none');
        document.getElementById('btnStopScan').classList.remove('d-none');
    }).catch(err => alert('Tidak bisa akses kamera: ' + err));
}

function stopScan() {
    if (html5QrCode) {
        html5QrCode.stop().then(() => {
            html5QrCode = null;
            document.getElementById('btnStartScan').classList.remove('d-none');
            document.getElementById('btnStopScan').classList.add('d-none');
        });
    }
}

// ── Anti-duplikat debounce ───────────────────────────────────────────
let lastScanned = '';
let lastTime    = 0;

function prosesQr(qrCode) {
    const now = Date.now();
    if (qrCode === lastScanned && now - lastTime < 3000) return;
    lastScanned = qrCode;
    lastTime    = now;

    fetch(`/admin/absensi/${jadwalId}/qr`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ qr_code: qrCode, tipe: tipe }),
    })
    .then(r => r.json())
    .then(data => {
        tampilHasil(data);
        if (data.success) updateRow(data);
    })
    .catch(() => tampilHasil({ success: false, message: 'Koneksi gagal.' }));
}

// ── Tampil notifikasi + log ──────────────────────────────────────────
function tampilHasil(data) {
    const el = document.getElementById('scanResult');
    el.className = data.success
        ? 'alert alert-success mb-3'
        : (data.already ? 'alert alert-warning mb-3' : 'alert alert-danger mb-3');
    el.innerHTML = `<strong>${data.message}</strong>`;
    el.classList.remove('d-none');

    // Tambah ke log
    const log = document.getElementById('scanLog');
    document.getElementById('logEmpty')?.remove();
    const time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    const item = document.createElement('div');
    item.className = 'd-flex justify-content-between align-items-center py-1 border-bottom';
    item.style.fontSize = '13px';
    item.innerHTML = `
        <span>${data.success ? '✅' : (data.already ? '⚠️' : '❌')} ${data.peserta || '-'}</span>
        <span class="text-muted">${data.tipe ? data.tipe.toUpperCase() : ''} ${time}</span>
    `;
    log.insertBefore(item, log.firstChild);

    setTimeout(() => el.classList.add('d-none'), 4000);
}

// ── Update baris tabel realtime ──────────────────────────────────────
function updateRow(data) {
    const row = document.getElementById(`row-${data.no_daftar}`);
    if (!row) return;

    // Update badge status
    const badge = document.getElementById(`badge-${data.no_daftar}`);
    if (badge && data.status) {
        const info    = badgeMap[data.status] || badgeMap['hadir'];
        badge.className   = 'badge ' + info.cls;
        badge.textContent = info.label;

        // Tambah label telat jika izin
        const telatEl = badge.nextElementSibling;
        if (data.status === 'izin') {
            if (!telatEl || !telatEl.classList.contains('text-warning')) {
                const span = document.createElement('span');
                span.className   = 'text-warning d-block';
                span.style.fontSize = '10px';
                span.textContent = 'telat';
                badge.after(span);
            }
        }
    }

    // Update waktu masuk
    if (data.tipe === 'masuk') {
        const m = document.getElementById(`masuk-${data.no_daftar}`);
        if (m) m.textContent = data.waktu;

        // Update waktu pulang jika auto-set
        if (data.waktu_pulang) {
            const p = document.getElementById(`pulang-${data.no_daftar}`);
            if (p) p.textContent = data.waktu_pulang;
        }
    } else {
        const p = document.getElementById(`pulang-${data.no_daftar}`);
        if (p) p.textContent = data.waktu;
    }

    // Highlight baris
    row.style.background = '#d4edda';
    setTimeout(() => row.style.background = '', 2000);
}

// ── Peringatan di modal edit jika pilih sakit/alpha ──────────────────
function cekStatusModal(id) {
    const val  = document.getElementById('statusSelect' + id).value;
    const warn = document.getElementById('warnModal' + id);
    if (warn) {
        warn.classList.toggle('d-none', !['sakit', 'alpha'].includes(val));
    }
}

function clearLog() {
    document.getElementById('scanLog').innerHTML =
        '<div class="text-muted text-center py-3" id="logEmpty">Belum ada scan.</div>';
}
</script>
@endsection