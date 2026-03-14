<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\JadwalLatihan;
use App\Models\Pendaftaran;
use App\Models\Rekrutmen;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    // Toleransi hadir dalam menit
    const TOLERANSI_HADIR = 5;

    // ── Daftar Jadwal ────────────────────────────────────────────
    public function index(Request $request)
    {
        $rekrutmenId   = $request->get('rekrutmen_id', Rekrutmen::where('is_aktif', true)->value('id'));
        $rekrutmenList = Rekrutmen::orderByDesc('tahun')->get();
        $jadwalList    = JadwalLatihan::where('rekrutmen_id', $rekrutmenId)
                            ->orderBy('tanggal')->get();

        return view('admin.absensi.index', compact('jadwalList', 'rekrutmenList', 'rekrutmenId'));
    }

    // ── Buat Jadwal Baru ─────────────────────────────────────────
    public function store(Request $request)
    {
        $request->merge([
            'jam_masuk'  => $request->jam_masuk_h  . ':' . $request->jam_masuk_m,
            'jam_pulang' => $request->jam_pulang_h . ':' . $request->jam_pulang_m,
        ]);

        $data = $request->validate([
            'rekrutmen_id' => 'required|exists:rekrutmen,id',
            'nama'         => 'required|string|max:255',
            'tanggal'      => 'required|date',
            'jam_masuk'    => 'required',
            'jam_pulang'   => 'required',
            'lokasi'       => 'nullable|string|max:255',
            'keterangan'   => 'nullable|string',
        ]);

        $jadwal = JadwalLatihan::create($data);

        $pesertaIds = Pendaftaran::where('rekrutmen_id', $data['rekrutmen_id'])
            ->where('is_lulus_final', true)
            ->pluck('id');

        $rows = $pesertaIds->map(fn($id) => [
            'jadwal_latihan_id' => $jadwal->id,
            'pendaftaran_id'    => $id,
            'status'            => 'alpha',
            'created_at'        => now(),
            'updated_at'        => now(),
        ])->toArray();

        Absensi::insert($rows);

        return back()->with('success', "Jadwal \"{$jadwal->nama}\" dibuat. {$pesertaIds->count()} peserta ditambahkan.");
    }

    // ── Buat Jadwal Massal ────────────────────────────────────────
    public function storeBulk(Request $request)
    {
        $request->merge([
            'jam_masuk'  => $request->jam_masuk_h  . ':' . $request->jam_masuk_m,
            'jam_pulang' => $request->jam_pulang_h . ':' . $request->jam_pulang_m,
        ]);

        $data = $request->validate([
            'rekrutmen_id'    => 'required|exists:rekrutmen,id',
            'nama'            => 'required|string|max:255',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'hari'            => 'required|array|min:1',
            'hari.*'          => 'integer|between:0,6',
            'jam_masuk'       => 'required',
            'jam_pulang'      => 'required',
            'lokasi'          => 'nullable|string|max:255',
        ]);

        $pesertaIds = Pendaftaran::where('rekrutmen_id', $data['rekrutmen_id'])
            ->where('is_lulus_final', true)
            ->pluck('id');

        $tanggal      = Carbon::parse($data['tanggal_mulai']);
        $selesai      = Carbon::parse($data['tanggal_selesai']);
        $hariAktif    = array_map('intval', $data['hari']);
        $jumlahJadwal = 0;

        while ($tanggal->lte($selesai)) {
            if (in_array($tanggal->dayOfWeek, $hariAktif)) {
                $namaJadwal = $data['nama'] . ' - ' . $tanggal->translatedFormat('l, d M Y');

                $jadwal = JadwalLatihan::create([
                    'rekrutmen_id' => $data['rekrutmen_id'],
                    'nama'         => $namaJadwal,
                    'tanggal'      => $tanggal->toDateString(),
                    'jam_masuk'    => $data['jam_masuk'],
                    'jam_pulang'   => $data['jam_pulang'],
                    'lokasi'       => $data['lokasi'] ?? null,
                ]);

                $rows = $pesertaIds->map(fn($id) => [
                    'jadwal_latihan_id' => $jadwal->id,
                    'pendaftaran_id'    => $id,
                    'status'            => 'alpha',
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ])->toArray();

                Absensi::insert($rows);
                $jumlahJadwal++;
            }
            $tanggal->addDay();
        }

        return back()->with('success', "{$jumlahJadwal} jadwal berhasil dibuat untuk {$pesertaIds->count()} peserta.");
    }

    // ── Hapus Jadwal ─────────────────────────────────────────────
    public function destroy(JadwalLatihan $jadwal)
    {
        $jadwal->absensi()->delete();
        $jadwal->delete();
        return back()->with('success', 'Jadwal berhasil dihapus.');
    }

    // ── Halaman Scan QR per Jadwal ───────────────────────────────
    public function scan(JadwalLatihan $jadwal)
    {
        $absensiList = Absensi::with('pendaftaran')
            ->where('jadwal_latihan_id', $jadwal->id)
            ->get()
            ->keyBy('pendaftaran_id');

        return view('admin.absensi.scan', compact('jadwal', 'absensiList'));
    }

    // ── Proses Scan QR (AJAX) ─────────────────────────────────────
    public function prosesQr(Request $request, JadwalLatihan $jadwal)
    {
        $request->validate([
            'qr_code' => 'required|string',
            'tipe'    => 'required|in:masuk,pulang',
        ]);

        $noPendaftaran = $this->parseQrCode($request->qr_code);

        $pendaftaran = Pendaftaran::where('no_pendaftaran', $noPendaftaran)
            ->where('rekrutmen_id', $jadwal->rekrutmen_id)
            ->where('is_lulus_final', true)
            ->first();

        if (!$pendaftaran) {
            return response()->json([
                'success' => false,
                'message' => "❌ Peserta tidak ditemukan. (QR: {$noPendaftaran})",
            ], 404);
        }

        // ── ABSEN MASUK ──
        if ($request->tipe === 'masuk') {
            $cek = $this->validasiWaktuMasuk($jadwal);
            if (!$cek['boleh']) {
                return response()->json(['success' => false, 'message' => $cek['pesan']]);
            }

            $absensi = Absensi::firstOrCreate(
                ['jadwal_latihan_id' => $jadwal->id, 'pendaftaran_id' => $pendaftaran->id],
                ['status' => 'alpha', 'dicatat_oleh' => auth()->id()]
            );

            if ($absensi->waktu_masuk) {
                return response()->json([
                    'success' => false,
                    'already' => true,
                    'peserta' => $pendaftaran->nama_lengkap,
                    'message' => "⚠️ {$pendaftaran->nama_lengkap} sudah absen masuk pukul {$absensi->waktu_masuk->format('H:i')}.",
                ]);
            }

            $absensi->update([
                'status'       => $cek['status'],
                'waktu_masuk'  => now(),
                'dicatat_oleh' => auth()->id(),
            ]);

            $this->autoSetPulang($absensi->fresh(), $jadwal);
            $absensi->refresh();

            $labelStatus = $cek['status'] === 'izin' ? ' (telat)' : '';
            $pesanPulang = $absensi->waktu_pulang
                ? " | Pulang otomatis: {$absensi->waktu_pulang->format('H:i')}"
                : '';

            return response()->json([
                'success'      => true,
                'tipe'         => 'masuk',
                'peserta'      => $pendaftaran->nama_lengkap,
                'no_daftar'    => $pendaftaran->no_pendaftaran,
                'waktu'        => now()->format('H:i'),
                'waktu_pulang' => $absensi->waktu_pulang?->format('H:i'),
                'status'       => $cek['status'],
                'message'      => "✅ {$pendaftaran->nama_lengkap} absen MASUK{$labelStatus} pukul " . now()->format('H:i') . $pesanPulang,
            ]);
        }

        // ── ABSEN PULANG ──
        $absensi = Absensi::firstOrCreate(
            ['jadwal_latihan_id' => $jadwal->id, 'pendaftaran_id' => $pendaftaran->id],
            ['status' => 'alpha', 'dicatat_oleh' => auth()->id()]
        );

        if (!$absensi->waktu_masuk) {
            return response()->json([
                'success' => false,
                'message' => "❌ {$pendaftaran->nama_lengkap} belum absen masuk.",
            ]);
        }

        if ($absensi->waktu_pulang) {
            return response()->json([
                'success' => false,
                'already' => true,
                'message' => "⚠️ {$pendaftaran->nama_lengkap} sudah absen pulang pukul {$absensi->waktu_pulang->format('H:i')}.",
            ]);
        }

        $absensi->update(['waktu_pulang' => now(), 'dicatat_oleh' => auth()->id()]);

        return response()->json([
            'success'   => true,
            'tipe'      => 'pulang',
            'peserta'   => $pendaftaran->nama_lengkap,
            'no_daftar' => $pendaftaran->no_pendaftaran,
            'waktu'     => now()->format('H:i'),
            'message'   => "✅ {$pendaftaran->nama_lengkap} absen PULANG pukul " . now()->format('H:i'),
        ]);
    }

    // ── Input Manual ─────────────────────────────────────────────
    public function inputManual(Request $request, JadwalLatihan $jadwal)
    {
        $request->validate([
            'no_pendaftaran' => 'required|string',
            'tipe'           => 'required|in:masuk,pulang',
        ]);

        $pendaftaran = Pendaftaran::where('no_pendaftaran', $request->no_pendaftaran)
            ->where('rekrutmen_id', $jadwal->rekrutmen_id)
            ->where('is_lulus_final', true)
            ->first();

        if (!$pendaftaran) {
            return back()->with('error', "No. pendaftaran \"{$request->no_pendaftaran}\" tidak ditemukan.");
        }

        $absensi = Absensi::firstOrCreate(
            ['jadwal_latihan_id' => $jadwal->id, 'pendaftaran_id' => $pendaftaran->id],
            ['status' => 'alpha']
        );

        if ($request->tipe === 'masuk') {
            $cek = $this->validasiWaktuMasuk($jadwal);
            if (!$cek['boleh']) {
                return back()->with('error', $cek['pesan']);
            }

            if ($absensi->waktu_masuk) {
                return back()->with('info', "⚠️ {$pendaftaran->nama_lengkap} sudah absen masuk pukul {$absensi->waktu_masuk->format('H:i')}.");
            }

            $absensi->update([
                'status'       => $cek['status'],
                'waktu_masuk'  => now(),
                'dicatat_oleh' => auth()->id(),
            ]);

            $this->autoSetPulang($absensi->fresh(), $jadwal);
            $absensi->refresh();

            $labelStatus = $cek['status'] === 'izin' ? ' (telat)' : '';
            $infoPulang  = $absensi->waktu_pulang ? " | Pulang otomatis: {$absensi->waktu_pulang->format('H:i')}" : '';

            return back()->with('success', "✅ {$pendaftaran->nama_lengkap} absen MASUK{$labelStatus} pukul " . now()->format('H:i') . $infoPulang);
        }

        if (!$absensi->waktu_masuk) {
            return back()->with('error', "❌ {$pendaftaran->nama_lengkap} belum absen masuk.");
        }
        if ($absensi->waktu_pulang) {
            return back()->with('info', "⚠️ {$pendaftaran->nama_lengkap} sudah absen pulang pukul {$absensi->waktu_pulang->format('H:i')}.");
        }

        $absensi->update(['waktu_pulang' => now(), 'dicatat_oleh' => auth()->id()]);

        return back()->with('success', "✅ {$pendaftaran->nama_lengkap} absen PULANG pukul " . now()->format('H:i'));
    }

    // ── Update Status Manual ──────────────────────────────────────
    public function updateStatus(Request $request, Absensi $absensi)
    {
        $request->validate([
            'status'     => 'required|in:hadir,izin,sakit,alpha',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $updateData = ['status' => $request->status, 'keterangan' => $request->keterangan];

        if (in_array($request->status, ['sakit', 'alpha'])) {
            $updateData['waktu_masuk']  = null;
            $updateData['waktu_pulang'] = null;
        }

        $absensi->update($updateData);
        return back()->with('success', 'Status absensi berhasil diupdate.');
    }

    // ── Rekap ────────────────────────────────────────────────────
    public function rekap(Request $request)
    {
        $rekrutmenId   = $request->get('rekrutmen_id', Rekrutmen::where('is_aktif', true)->value('id'));
        $rekrutmenList = Rekrutmen::orderByDesc('tahun')->get();
        $rekrutmen     = Rekrutmen::find($rekrutmenId);
        $mode          = $request->get('mode', 'semua');

        $tanggalFilter = $request->get('tanggal', now()->format('Y-m-d'));
        $mingguFilter  = $request->get('minggu', now()->format('Y-W'));
        $bulanFilter   = $request->get('bulan', now()->format('Y-m'));
        $mingguLabel   = '';

        $query = JadwalLatihan::where('rekrutmen_id', $rekrutmenId)->orderBy('tanggal');

        if ($mode === 'harian') {
            $query->whereDate('tanggal', $tanggalFilter);

        } elseif ($mode === 'mingguan') {
            try {
                $parts       = explode('-W', $mingguFilter);
                $tahunMinggu = (int) ($parts[0] ?? now()->year);
                $noMinggu    = (int) ($parts[1] ?? now()->weekOfYear);
                $awalMinggu  = Carbon::now()->setISODate($tahunMinggu, $noMinggu)->startOfWeek();
                $akhirMinggu = $awalMinggu->copy()->endOfWeek();
                $mingguLabel = $awalMinggu->translatedFormat('d M') . ' – ' . $akhirMinggu->translatedFormat('d M Y');
                $query->whereBetween('tanggal', [$awalMinggu->toDateString(), $akhirMinggu->toDateString()]);
            } catch (\Exception $e) {
                $mingguLabel = $mingguFilter;
            }

        } elseif ($mode === 'bulanan') {
            $tgl = Carbon::parse($bulanFilter . '-01');
            $query->whereYear('tanggal', $tgl->year)->whereMonth('tanggal', $tgl->month);
        }

        $jadwalList = $query->get();

        $peserta = Pendaftaran::where('rekrutmen_id', $rekrutmenId)
            ->where('is_lulus_final', true)
            ->with(['absensi' => fn($q) => $q->whereIn('jadwal_latihan_id', $jadwalList->pluck('id'))])
            ->orderBy('jenis_kelamin')->orderBy('nama_lengkap')
            ->get();

        return view('admin.absensi.rekap', compact(
            'peserta', 'jadwalList', 'rekrutmen', 'rekrutmenList', 'rekrutmenId',
            'mode', 'tanggalFilter', 'mingguFilter', 'bulanFilter', 'mingguLabel'
        ));
    } // ← tutup rekap()

    // ── Export Excel ──────────────────────────────────────────────
    public function exportExcel(Request $request)
    {
        $rekrutmenId = $request->get('rekrutmen_id', Rekrutmen::where('is_aktif', true)->value('id'));
        $rekrutmen   = Rekrutmen::find($rekrutmenId);
        $jadwalList  = JadwalLatihan::where('rekrutmen_id', $rekrutmenId)->orderBy('tanggal')->get();
        $peserta     = Pendaftaran::where('rekrutmen_id', $rekrutmenId)
            ->where('is_lulus_final', true)
            ->with(['absensi' => fn($q) => $q->whereIn('jadwal_latihan_id', $jadwalList->pluck('id'))])
            ->orderBy('nama_lengkap')->get();

        $filename = 'rekap-absensi-' . ($rekrutmen->tahun ?? date('Y')) . '.csv';
        $headers  = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"{$filename}\""];

        $callback = function () use ($peserta, $jadwalList) {
            $f    = fopen('php://output', 'w');
            $head = ['No', 'No. Pendaftaran', 'Nama', 'L/P', 'Sekolah'];
            foreach ($jadwalList as $j) {
                $head[] = $j->nama . ' (' . $j->tanggal->format('d/m') . ')';
            }
            $head = array_merge($head, ['Total Hadir', 'Total Izin', 'Total Sakit', 'Total Alpha', '% Kehadiran']);
            fputcsv($f, $head);

            foreach ($peserta as $i => $p) {
                $row   = [$i + 1, $p->no_pendaftaran, $p->nama_lengkap, $p->jenis_kelamin, $p->nama_sekolah];
                $hadir = $izin = $sakit = $alpha = 0;
                foreach ($jadwalList as $j) {
                    $abs   = $p->absensi->firstWhere('jadwal_latihan_id', $j->id);
                    $s     = $abs ? $abs->status : 'alpha';
                    $row[] = strtoupper($s);
                    match($s) {
                        'hadir' => $hadir++,
                        'izin'  => $izin++,
                        'sakit' => $sakit++,
                        default => $alpha++,
                    };
                }
                $total = $jadwalList->count();
                $row[] = $hadir;
                $row[] = $izin;
                $row[] = $sakit;
                $row[] = $alpha;
                $row[] = ($total > 0 ? round($hadir / $total * 100, 1) : 0) . '%';
                fputcsv($f, $row);
            }
            fclose($f);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ── Export PDF ────────────────────────────────────────────────
    public function exportPdf(Request $request)
    {
        $rekrutmenId = $request->get('rekrutmen_id', Rekrutmen::where('is_aktif', true)->value('id'));
        $rekrutmen   = Rekrutmen::find($rekrutmenId);
        $jadwalList  = JadwalLatihan::where('rekrutmen_id', $rekrutmenId)->orderBy('tanggal')->get();
        $peserta     = Pendaftaran::where('rekrutmen_id', $rekrutmenId)
            ->where('is_lulus_final', true)
            ->with(['absensi' => fn($q) => $q->whereIn('jadwal_latihan_id', $jadwalList->pluck('id'))])
            ->orderBy('nama_lengkap')->get();
        $pengaturan  = \App\Models\Pengaturan::ambil();

        return view('admin.absensi.export-pdf', compact('peserta', 'jadwalList', 'rekrutmen', 'pengaturan'));
    }

    // ── Helper: Parse QR code berbagai format ────────────────────
    private function parseQrCode(string $qrCode): string
    {
        $qrCode = trim($qrCode);

        if (str_contains($qrCode, '|')) {
            $qrCode = explode('|', $qrCode)[0];
        }

        if (str_starts_with($qrCode, 'PSK-PSK-')) {
            $qrCode = substr($qrCode, 4);
        }

        return trim($qrCode);
    }

    // ── Helper: Validasi waktu absen masuk ───────────────────────
    private function validasiWaktuMasuk(JadwalLatihan $jadwal): array
    {
        $jamMasuk  = $this->parseJamJadwal($jadwal->tanggal, $jadwal->jam_masuk);
        $jamPulang = $this->parseJamJadwal($jadwal->tanggal, $jadwal->jam_pulang);
        $sekarang  = now();

        if ($sekarang->lt($jamMasuk)) {
            return [
                'boleh'  => false,
                'status' => null,
                'pesan'  => "❌ Belum waktunya absen. Jadwal masuk pukul {$jamMasuk->format('H:i')} WIB.",
            ];
        }

        if ($sekarang->gt($jamPulang)) {
            return [
                'boleh'  => false,
                'status' => null,
                'pesan'  => "❌ Waktu absen masuk sudah berakhir pukul {$jamPulang->format('H:i')} WIB.",
            ];
        }

        if ($sekarang->lte($jamMasuk->copy()->addMinutes(self::TOLERANSI_HADIR))) {
            return ['boleh' => true, 'status' => 'hadir', 'pesan' => ''];
        }

        return ['boleh' => true, 'status' => 'izin', 'pesan' => ''];
    }

    // ── Helper: Parse jam jadwal ──────────────────────────────────
    private function parseJamJadwal($tanggal, string $jam): Carbon
    {
        $jamBersih = substr($jam, 0, 5);
        return Carbon::createFromFormat(
            'Y-m-d H:i',
            $tanggal->format('Y-m-d') . ' ' . $jamBersih
        );
    }

    // ── Helper: Auto set waktu pulang ────────────────────────────
    private function autoSetPulang(Absensi $absensi, JadwalLatihan $jadwal): void
    {
        if (!in_array($absensi->status, ['hadir', 'izin'])) return;
        if (!$absensi->waktu_masuk) return;
        if ($absensi->waktu_pulang) return;

        $jamPulang = Carbon::createFromFormat(
            'Y-m-d H:i',
            $jadwal->tanggal->format('Y-m-d') . ' 17:00'
        );

        if (now()->gte($jamPulang)) {
            $absensi->update(['waktu_pulang' => $jamPulang]);
        }
    }
}