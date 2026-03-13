<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rekrutmen;
use App\Models\SeleksiTahap;
use Illuminate\Http\Request;

class RekrutmenController extends Controller
{
    public function index()
    {
        $rekrutmen = Rekrutmen::withCount('pendaftaran')
            ->orderByDesc('tahun')
            ->paginate(10);

        return view('admin.rekrutmen.index', compact('rekrutmen'));
    }

    public function create()
    {
        return view('admin.rekrutmen.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tahun'         => 'required|integer|min:2020|max:2099',
            'nama'          => 'required|string|max:255',
            'deskripsi'     => 'nullable|string',
            'tanggal_buka'  => 'required|date',
            'tanggal_tutup' => 'required|date|after:tanggal_buka',
            'kuota_putra'   => 'nullable|integer|min:1',
            'kuota_putri'   => 'nullable|integer|min:1',
            'is_aktif'      => 'boolean',
            'syarat'        => 'nullable|string',
            'catatan'       => 'nullable|string',
        ]);

        if (!empty($data['is_aktif'])) {
            Rekrutmen::where('is_aktif', true)->update(['is_aktif' => false]);
        }

        $rekrutmen = Rekrutmen::create($data);

        // Buat tahap seleksi default
        $tahapDefault = [
            ['nama' => 'Seleksi Administrasi', 'urutan' => 1, 'passing_grade' => 70],
            ['nama' => 'Seleksi Fisik & PBB',  'urutan' => 2, 'passing_grade' => 70],
            ['nama' => 'Wawancara & TIU',       'urutan' => 3, 'passing_grade' => 70],
        ];
        foreach ($tahapDefault as $tahap) {
            $rekrutmen->seleksiTahap()->create($tahap);
        }

        return redirect()->route('admin.rekrutmen.index')
            ->with('success', 'Rekrutmen berhasil dibuat beserta 3 tahap seleksi default.');
    }

    public function show(Rekrutmen $rekrutmen)
    {
        $rekrutmen->loadCount([
            'pendaftaran',
            'pendaftaran as pendaftar_putra' => fn($q) => $q->where('jenis_kelamin', 'L'),
            'pendaftaran as pendaftar_putri' => fn($q) => $q->where('jenis_kelamin', 'P'),
            'pendaftaran as lulus_count'     => fn($q) => $q->where('is_lulus_final', true),
        ]);

        $tahapList   = $rekrutmen->seleksiTahap;
        $pendaftaran = $rekrutmen->pendaftaran()->with('user')->latest()->paginate(15);

        return view('admin.rekrutmen.show', compact('rekrutmen', 'tahapList', 'pendaftaran'));
    }

    public function edit(Rekrutmen $rekrutmen)
    {
        $tahapList = $rekrutmen->seleksiTahap;
        return view('admin.rekrutmen.edit', compact('rekrutmen', 'tahapList'));
    }

    public function update(Request $request, Rekrutmen $rekrutmen)
    {
        $data = $request->validate([
            'tahun'         => 'required|integer|min:2020|max:2099',
            'nama'          => 'required|string|max:255',
            'deskripsi'     => 'nullable|string',
            'tanggal_buka'  => 'required|date',
            'tanggal_tutup' => 'required|date|after:tanggal_buka',
            'kuota_putra'   => 'nullable|integer|min:1',
            'kuota_putri'   => 'nullable|integer|min:1',
            'is_aktif'      => 'boolean',
            'syarat'        => 'nullable|string',
            'catatan'       => 'nullable|string',
        ]);

        if (!empty($data['is_aktif']) && !$rekrutmen->is_aktif) {
            Rekrutmen::where('is_aktif', true)->update(['is_aktif' => false]);
        }

        $rekrutmen->update($data);

        return redirect()->route('admin.rekrutmen.index')
            ->with('success', 'Rekrutmen berhasil diperbarui.');
    }

    public function destroy(Rekrutmen $rekrutmen)
    {
        if ($rekrutmen->pendaftaran()->count() > 0) {
            return back()->with('error', 'Rekrutmen tidak bisa dihapus karena sudah ada pendaftar.');
        }

        // Hapus seleksi_hasil dulu (child dari seleksi_tahap)
        foreach ($rekrutmen->seleksiTahap as $tahap) {
            $tahap->hasil()->delete();
        }

        // Hapus seleksi_tahap
        $rekrutmen->seleksiTahap()->delete();

        // Baru hapus rekrutmen
        $rekrutmen->delete();

        return redirect()->route('admin.rekrutmen.index')
            ->with('success', 'Rekrutmen berhasil dihapus.');
    }

    public function toggleAktif(Rekrutmen $rekrutmen)
    {
        Rekrutmen::where('is_aktif', true)->update(['is_aktif' => false]);
        if (!$rekrutmen->is_aktif) {
            $rekrutmen->update(['is_aktif' => true]);
        }
        return back()->with('success', 'Status rekrutmen berhasil diubah.');
    }
}