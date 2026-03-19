<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kartu Calon Anggota Paskibra</title>
<link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

@page {
    size: A4 landscape;
    margin: 0;
}

body {
    font-family: 'DejaVu Sans', Arial, sans-serif;
    width: 297mm;
    height: 210mm;
    overflow: hidden;
    background: #fff;
}

.card-wrap {
    width: 297mm;
    height: 210mm;
    display: flex;
    position: relative;
    background: #fff;
}

/* ── SISI KIRI ── */
.left {
    width: 72mm;
    height: 210mm;
    background: linear-gradient(160deg, #c0392b 0%, #922b21 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 10mm 6mm;
    position: relative;
    overflow: hidden;
}

.left::before {
    content: '';
    position: absolute;
    top: -30mm; left: -20mm;
    width: 70mm; height: 70mm;
    border-radius: 50%;
    background: rgba(255,255,255,0.07);
}
.left::after {
    content: '';
    position: absolute;
    bottom: -20mm; right: -25mm;
    width: 60mm; height: 60mm;
    border-radius: 50%;
    background: rgba(255,255,255,0.07);
}

.logo-wrap {
    width: 22mm;
    height: 22mm;
    margin-bottom: 5mm;
    border-radius: 50%;
    background: rgba(255,255,255,0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3mm;
}

.logo-wrap img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.org-name {
    color: #fff;
    font-size: 11pt;
    font-weight: bold;
    text-align: center;
    line-height: 1.4;
    letter-spacing: 1px;
    text-transform: uppercase;
    margin-bottom: 2mm;
}

.org-sub {
    color: rgba(255,255,255,0.75);
    font-size: 7.5pt;
    text-align: center;
    line-height: 1.5;
}

.divider-left {
    width: 30mm;
    height: 1px;
    background: rgba(255,255,255,0.35);
    margin: 5mm 0;
}

.kartu-label {
    color: #fff;
    font-size: 8pt;
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 2px;
    font-weight: bold;
}

.tahun {
    color: rgba(255,255,255,0.8);
    font-size: 9pt;
    text-align: center;
    margin-top: 2mm;
}

/* ── SISI KANAN ── */
.right {
    flex: 1;
    height: 210mm;
    padding: 10mm 12mm 8mm 10mm;
    display: flex;
    flex-direction: column;
    position: relative;
    background: #fff;
}

.right::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 2.5mm;
    background: linear-gradient(90deg, #c0392b, #e74c3c, #f39c12);
}

.right-header {
    margin-top: 5mm;
    margin-bottom: 6mm;
    border-bottom: 0.5px solid #e0e0e0;
    padding-bottom: 5mm;
}

.right-header .kartu-title {
    font-size: 14pt;
    font-weight: bold;
    color: #c0392b;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.right-header .kartu-subtitle {
    font-size: 8.5pt;
    color: #666;
    margin-top: 1mm;
}

.body-wrap {
    display: flex;
    flex: 1;
    gap: 8mm;
}

.data-wrap { flex: 1; }

.no-peserta {
    background: #c0392b;
    color: #fff;
    font-size: 11pt;
    font-weight: bold;
    letter-spacing: 2px;
    padding: 2mm 5mm;
    border-radius: 2mm;
    display: inline-block;
    margin-bottom: 5mm;
}

.data-row {
    display: flex;
    margin-bottom: 3.5mm;
    align-items: flex-start;
}

.data-label {
    width: 38mm;
    font-size: 8pt;
    color: #888;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding-top: 0.5mm;
    flex-shrink: 0;
}

.data-sep {
    width: 5mm;
    font-size: 8pt;
    color: #aaa;
    flex-shrink: 0;
}

.data-value {
    font-size: 9.5pt;
    font-weight: bold;
    color: #222;
    line-height: 1.4;
}

.badge-status {
    display: inline-block;
    background: #27ae60;
    color: #fff;
    font-size: 7.5pt;
    font-weight: bold;
    padding: 1mm 3mm;
    border-radius: 1mm;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-top: 4mm;
}

.fisik-wrap {
    display: flex;
    gap: 4mm;
    margin-top: 4mm;
}

.fisik-box {
    background: #f8f8f8;
    border: 0.5px solid #e0e0e0;
    border-radius: 2mm;
    padding: 2mm 4mm;
    text-align: center;
}

.fisik-val {
    font-size: 11pt;
    font-weight: bold;
    color: #c0392b;
}

.fisik-lab {
    font-size: 7pt;
    color: #999;
    text-transform: uppercase;
}

.foto-qr-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 3mm;
    flex-shrink: 0;
}

.foto-box {
    width: 35mm;
    height: 45mm;
    border: 1px solid #ddd;
    overflow: hidden;
    background: #f5f5f5;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 1.5mm;
}

.foto-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.foto-placeholder {
    font-size: 7.5pt;
    color: #bbb;
    text-align: center;
    line-height: 1.6;
}

.qr-box {
    width: 25mm;
    height: 25mm;
    border: 0.5px solid #ddd;
    padding: 1mm;
    border-radius: 1mm;
    background: #fff;
}

.qr-box img { width: 100%; height: 100%; }

.qr-label {
    font-size: 6.5pt;
    color: #aaa;
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.footer {
    border-top: 0.5px solid #eee;
    padding-top: 3mm;
    margin-top: auto;
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
}

.footer-note {
    font-size: 7pt;
    color: #aaa;
    font-style: italic;
    line-height: 1.6;
}

.ttd-wrap {
    text-align: center;
    min-width: 45mm;
}

.ttd-line {
    border-bottom: 1px solid #333;
    width: 40mm;
    margin: 0 auto 1mm;
    height: 10mm;
}

.ttd-nama {
    font-size: 7.5pt;
    font-weight: bold;
    color: #333;
}

.ttd-jabatan {
    font-size: 6.5pt;
    color: #888;
}
</style>
</head>
<body>

<div class="card-wrap">

    {{-- ── KIRI ── --}}
    <div class="left">
        <div class="logo-wrap">
            {{--
                FIX LOGO: DomPDF tidak bisa load gambar via URL atau public_path biasa.
                Harus pakai path absolut file:// atau embed base64.
                Gunakan base64 agar paling reliable di semua environment.
            --}}
            @php
                $logoPath = public_path('images/logo.png');
                $logoSrc  = '';
                if (file_exists($logoPath)) {
                    $logoData = base64_encode(file_get_contents($logoPath));
                    $logoMime = mime_content_type($logoPath);
                    $logoSrc  = 'data:' . $logoMime . ';base64,' . $logoData;
                }
            @endphp
            @if($logoSrc)
                <img src="{{ $logoSrc }}" alt="Logo">
            @else
                {{-- Fallback: teks jika logo tidak ditemukan --}}
                <div style="color:#fff;font-size:7pt;text-align:center;font-weight:bold;">PASKIBRA</div>
            @endif
        </div>
        <div class="org-name">PASKIBRA<br>KECAMATAN</div>
        <div class="org-sub">{{ config('app.nama_kecamatan', 'Compreng') }}<br>Kabupaten Subang</div>
        <div class="divider-left"></div>
        <div class="kartu-label">Kartu<br>Peserta</div>
        <div class="tahun">{{ $pendaftaran->rekrutmen->tahun ?? date('Y') }}</div>
    </div>

    {{-- ── KANAN ── --}}
    <div class="right">

        <div class="right-header">
            <div class="kartu-title">Kartu Calon Anggota</div>
            <div class="kartu-subtitle">
                Paskibra Kecamatan {{ config('app.nama_kecamatan', 'Compreng') }} &mdash;
                Seleksi Tahun {{ $pendaftaran->rekrutmen->tahun ?? date('Y') }}
            </div>
        </div>

        <div class="body-wrap">

            <div class="data-wrap">
                <div class="no-peserta">{{ $pendaftaran->no_pendaftaran }}</div>

                <div class="data-row">
                    <div class="data-label">Nama Lengkap</div>
                    <div class="data-sep">:</div>
                    <div class="data-value">{{ strtoupper($pendaftaran->nama_lengkap) }}</div>
                </div>

                <div class="data-row">
                    <div class="data-label">Tempat, Tgl Lahir</div>
                    <div class="data-sep">:</div>
                    <div class="data-value">
                        {{ strtoupper($pendaftaran->tempat_lahir) }},
                        {{ \Carbon\Carbon::parse($pendaftaran->tanggal_lahir)->translatedFormat('d F Y') }}
                    </div>
                </div>

                <div class="data-row">
                    <div class="data-label">Jenis Kelamin</div>
                    <div class="data-sep">:</div>
                    <div class="data-value">
                        {{ $pendaftaran->jenis_kelamin === 'L' ? 'LAKI-LAKI' : 'PEREMPUAN' }}
                    </div>
                </div>

                <div class="data-row">
                    <div class="data-label">Asal Sekolah</div>
                    <div class="data-sep">:</div>
                    <div class="data-value">{{ strtoupper($pendaftaran->nama_sekolah) }}</div>
                </div>

                <div class="data-row">
                    <div class="data-label">Kelas</div>
                    <div class="data-sep">:</div>
                    <div class="data-value">{{ $pendaftaran->kelas }}</div>
                </div>

                <div class="data-row">
                    <div class="data-label">Alamat</div>
                    <div class="data-sep">:</div>
                    <div class="data-value" style="font-size:8.5pt;">
                        {{ $pendaftaran->alamat_lengkap }}
                    </div>
                </div>

                <div class="fisik-wrap">
                    <div class="fisik-box">
                        <div class="fisik-val">{{ $pendaftaran->tinggi_badan }} cm</div>
                        <div class="fisik-lab">Tinggi</div>
                    </div>
                    <div class="fisik-box">
                        <div class="fisik-val">{{ $pendaftaran->berat_badan }} kg</div>
                        <div class="fisik-lab">Berat</div>
                    </div>
                    @if($pendaftaran->golongan_darah)
                    <div class="fisik-box">
                        <div class="fisik-val">{{ $pendaftaran->golongan_darah }}</div>
                        <div class="fisik-lab">Gol. Darah</div>
                    </div>
                    @endif
                </div>

                <div class="badge-status">&#10003; Peserta Lulus Seleksi</div>
            </div>

            {{-- Foto & QR --}}
            <div class="foto-qr-wrap">
                <div class="foto-box">
                    @php
                        $fotoDok  = $pendaftaran->dokumen->firstWhere('jenis', 'foto_4x6');
                        $fotoSrc  = '';
                        if ($fotoDok) {
                            $fotoPath = storage_path('app/public/' . $fotoDok->path);
                            if (file_exists($fotoPath)) {
                                $fotoData = base64_encode(file_get_contents($fotoPath));
                                $fotoMime = mime_content_type($fotoPath);
                                $fotoSrc  = 'data:' . $fotoMime . ';base64,' . $fotoData;
                            }
                        }
                    @endphp
                    @if($fotoSrc)
                        <img src="{{ $fotoSrc }}" alt="Foto">
                    @else
                        <div class="foto-placeholder">Pas Foto<br>4 × 6</div>
                    @endif
                </div>

                @php
                    $qrData = 'PSK-' . $pendaftaran->no_pendaftaran . '|' . $pendaftaran->nama_lengkap;
                    $qrUrl  = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($qrData);
                @endphp
                <div class="qr-box">
                    <img src="{{ $qrUrl }}" alt="QR">
                </div>
                <div class="qr-label">Scan untuk absensi</div>
            </div>

        </div>

        <div class="footer">
            <div class="footer-note">
                * Kartu ini wajib dibawa saat latihan berlangsung.<br>
                * Tunjukkan kartu kepada panitia untuk pencatatan kehadiran.
            </div>
            <div class="ttd-wrap">
                <div class="ttd-line"></div>
                <div class="ttd-nama">Ketua Panitia</div>
                <div class="ttd-jabatan">Paskibra Kec. {{ config('app.nama_kecamatan', 'Compreng') }}</div>
            </div>
        </div>

    </div>
</div>

</body>
</html>