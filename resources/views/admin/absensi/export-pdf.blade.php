<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Rekap Absensi — {{ $rekrutmen?->nama }}</title>
<style>
  body { font-family: Arial, sans-serif; font-size: 11px; margin: 20px; }
  h2 { text-align: center; margin-bottom: 4px; font-size: 14px; }
  .sub { text-align: center; color: #555; margin-bottom: 16px; font-size: 11px; }
  table { width: 100%; border-collapse: collapse; margin-top: 10px; }
  th { background: #cc0000; color: #fff; padding: 6px 8px; text-align: center; font-size: 10px; border: 1px solid #aaa; }
  td { padding: 5px 8px; border: 1px solid #ddd; font-size: 11px; }
  tr:nth-child(even) td { background: #f9f9f9; }
  .H { color: #155724; background: #d4edda; padding: 1px 5px; border-radius: 3px; font-weight:bold; }
  .I { color: #0c5460; background: #d1ecf1; padding: 1px 5px; border-radius: 3px; font-weight:bold; }
  .S { color: #856404; background: #fff3cd; padding: 1px 5px; border-radius: 3px; font-weight:bold; }
  .A { color: #721c24; background: #f8d7da; padding: 1px 5px; border-radius: 3px; font-weight:bold; }
  .footer { margin-top: 30px; display: flex; justify-content: space-between; font-size: 10px; color: #888; }
  @media print { .no-print { display: none; } body { margin: 0; } }
</style>
</head>
<body>

<div class="no-print" style="margin-bottom:16px;">
    <button onclick="window.print()"
            style="background:#cc0000;color:#fff;border:none;padding:8px 20px;border-radius:6px;cursor:pointer;font-size:14px;">
        🖨️ Print / Save PDF
    </button>
</div>

<h2>REKAP ABSENSI LATIHAN PASKIBRA {{ strtoupper($rekrutmen?->nama) }}</h2>
<div class="sub">
    {{ $pengaturan->nama_kecamatan ?? 'Kecamatan Compreng' }} &nbsp;·&nbsp;
    Dicetak: {{ now()->translatedFormat('d F Y, H:i') }}
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Nama Peserta</th>
            <th>L/P</th>
            <th>Sekolah</th>
            @foreach($jadwalList as $j)
            <th>{{ $j->tanggal->format('d/m') }}<br>{{ Str::limit($j->nama, 10) }}</th>
            @endforeach
            <th>Hadir</th>
            <th>%</th>
        </tr>
    </thead>
    <tbody>
        @foreach($peserta as $i => $p)
        @php $totalHadir = 0; $total = $jadwalList->count(); @endphp
        <tr>
            <td style="text-align:center;">{{ $i + 1 }}</td>
            <td><strong>{{ $p->nama_lengkap }}</strong><br><small style="color:#888;">{{ $p->no_pendaftaran }}</small></td>
            <td style="text-align:center;">{{ $p->jenis_kelamin }}</td>
            <td>{{ $p->nama_sekolah }}</td>
            @foreach($jadwalList as $j)
            @php
                $abs = $p->absensi->firstWhere('jadwal_latihan_id', $j->id);
                $s   = $abs ? $abs->status : 'alpha';
                if ($s === 'hadir') $totalHadir++;
                $lbl = match($s) { 'hadir'=>'H','izin'=>'I','sakit'=>'S',default=>'A' };
                $cls = strtoupper($lbl);
            @endphp
            <td style="text-align:center;"><span class="{{ $cls }}">{{ $lbl }}</span></td>
            @endforeach
            @php $persen = $total > 0 ? round($totalHadir / $total * 100) : 0; @endphp
            <td style="text-align:center;font-weight:bold;">{{ $totalHadir }}/{{ $total }}</td>
            <td style="text-align:center;font-weight:bold;">{{ $persen }}%</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    <span>Keterangan: H=Hadir, I=Izin, S=Sakit, A=Alpha</span>
    <span>{{ $pengaturan->nama_kecamatan ?? 'Paskibra Kecamatan Compreng' }} · {{ now()->format('d/m/Y') }}</span>
</div>
</body>
</html>