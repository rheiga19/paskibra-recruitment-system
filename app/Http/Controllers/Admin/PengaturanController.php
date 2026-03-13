<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function edit()
    {
        $pengaturan = Pengaturan::ambil();
        return view('admin.pengaturan.edit', compact('pengaturan'));
    }

    public function update(Request $request)
    {
        $pengaturan = Pengaturan::ambil();
        $aksi = $request->input('aksi');

        if ($aksi === 'aktifkan') {
            $pengaturan->update([
                'pengumuman_aktif'         => true,
                'pengumuman_diaktifkan_at' => now(),
                'pesan_pengumuman'         => $request->pesan_pengumuman,
            ]);
            return back()->with('success', 'Pengumuman kelulusan berhasil diaktifkan. Publik kini dapat melihat daftar peserta lulus.');
        }

        if ($aksi === 'nonaktifkan') {
            $pengaturan->update([
                'pengumuman_aktif' => false,
                'pesan_pengumuman' => $request->pesan_pengumuman,
            ]);
            return back()->with('success', 'Pengumuman kelulusan berhasil dinonaktifkan.');
        }

        if ($aksi === 'simpan_pesan') {
            $pengaturan->update([
                'pesan_pengumuman' => $request->pesan_pengumuman,
            ]);
            return back()->with('success', 'Pesan pengumuman berhasil disimpan.');
        }

        if ($aksi === 'simpan_info') {
            $request->validate([
                'nama_kecamatan'    => 'nullable|string|max:255',
                'alamat_sekretariat'=> 'nullable|string|max:500',
                'no_hp_panitia'     => 'nullable|string|max:20',
            ]);
            $pengaturan->update($request->only([
                'nama_kecamatan', 'alamat_sekretariat', 'no_hp_panitia',
            ]));
            return back()->with('success', 'Informasi sistem berhasil disimpan.');
        }

        return back();
    }
}