<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kartu Peserta Rekrutmen Paskibra</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'Times New Roman', Times, serif;
    padding: 10mm 12mm;
    position: relative;
    min-height: 100%;
}

/* HEADER */
.header { width: 100%; border-bottom: 2px solid #000; margin-bottom: 10px; }
.header td { vertical-align: middle; padding-bottom: 6px; }
.header img { width: 16mm; height: 16mm; }
.title { font-size: 13pt; font-weight: bold; }
.subtitle { font-size: 10pt; font-weight: bold; }

/* DATA */
.data td { font-size: 10pt; padding: 3px 0; }
.label { width: 40mm; text-transform: uppercase; font-size: 9pt; }
.sep { width: 5mm; }
.value { font-weight: bold; text-transform: uppercase; }

/* FOTO */
.foto-box {
    width: 40mm;
    height: 60mm;
    border: 1px solid #000;
    text-align: center;
    vertical-align: middle;
}
.foto-box span { font-size: 9pt; line-height: 1.5; display: inline-block; }

/* FOOTER — fixed di bawah */
.footer {
    position: fixed;
    bottom: 10mm;
    left: 12mm;
    right: 12mm;
    font-size: 8pt;
    font-style: italic;
    color: #555;
    border-top: 1px solid #ccc;
    padding-top: 5px;
}
</style>
</head>
<body>

<!-- HEADER -->
<table class="header" width="100%">
<tr>
    <td width="20mm">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo">
    </td>
    <td>
        <div class="title">Kartu Peserta Rekrutmen Paskibra</div>
        <div class="subtitle">
            Kecamatan {{ config('app.nama_kecamatan', 'Compreng') }} — Tahun {{ config('app.tahun', date('Y')) }}
        </div>
    </td>
</tr>
</table>

<!-- BODY -->
<table width="100%">
<tr>

    <td width="70%" valign="top">
        <table class="data">
            <tr>
                <td class="label">No. Peserta</td>
                <td class="sep">:</td>
                <td class="value">{{ $pendaftaran->no_pendaftaran }}</td>
            </tr>
            <tr>
                <td class="label">Nama</td>
                <td class="sep">:</td>
                <td class="value">{{ $pendaftaran->nama_lengkap }}</td>
            </tr>
            <tr>
                <td class="label">Tempat Lahir</td>
                <td class="sep">:</td>
                <td class="value">{{ $pendaftaran->tempat_lahir }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Lahir</td>
                <td class="sep">:</td>
                <td class="value">
                    {{ \Carbon\Carbon::parse($pendaftaran->tanggal_lahir)->translatedFormat('d F Y') }}
                </td>
            </tr>
            <tr>
                <td class="label">Asal Sekolah</td>
                <td class="sep">:</td>
                <td class="value">{{ $pendaftaran->nama_sekolah }}</td>
            </tr>
            <tr>
                <td class="label">Alamat</td>
                <td class="sep">:</td>
                <td class="value">{{ $pendaftaran->alamat_lengkap }}</td>
            </tr>
        </table>
    </td>

    <td width="30%" align="center" valign="top">
        <table>
            <tr>
                <td class="foto-box">
                    <span>
                        Tempelkan<br>
                        Pas Foto<br>
                        Ukuran 4×6
                    </span>
                </td>
            </tr>
        </table>
    </td>

</tr>
</table>

<!-- FOOTER fixed di margin bawah -->
<div class="footer">
    * Kartu ini wajib dibawa saat seleksi berlangsung.
</div>

</body>
</html>