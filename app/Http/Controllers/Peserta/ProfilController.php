<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\ProfilPeserta;
use Illuminate\Http\Request;

class ProfilController extends Controller
{
    public function edit()
    {
        $profil = ProfilPeserta::firstOrNew(['user_id' => auth()->id()]);
        return view('peserta.profil.edit', compact('profil'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_lengkap'   => ['required', 'string', 'max:100'],
            'nik'            => ['required', 'digits:16'],
            'jenis_kelamin'  => ['required', 'in:L,P'],
            'tempat_lahir'   => ['required', 'string', 'max:60'],
            'tanggal_lahir'  => ['required', 'date', 'before:today'],
            'no_hp'          => ['required', 'string', 'max:15'],
            'alamat_lengkap' => ['required', 'string', 'max:300'],
            'nama_sekolah'   => ['required', 'string', 'max:100'],
            'jenjang'        => ['required', 'in:SMP,MTs,SMA,MA,SMK'],
            'kelas'          => ['required', 'in:VII,VIII,IX,X,XI,XII'],
            'nilai_rata'     => ['required', 'numeric', 'min:0', 'max:100'],
            'tinggi_badan'   => ['required', 'integer', 'min:100', 'max:250'],
            'berat_badan'    => ['required', 'integer', 'min:20',  'max:200'],
            'golongan_darah' => ['nullable', 'in:A,B,AB,O'],
            'nama_ortu'      => ['required', 'string', 'max:100'],
            'hp_ortu'        => ['required', 'string', 'max:15'],
            'hubungan_ortu'  => ['required', 'in:Ayah,Ibu,Wali'],
            'prestasi'       => ['nullable', 'string', 'max:500'],
        ]);

        auth()->user()->update(['name' => $request->nama_lengkap]);

        ProfilPeserta::updateOrCreate(
            ['user_id' => auth()->id()],
            $request->only([
                'nik', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir',
                'no_hp', 'alamat_lengkap', 'nama_sekolah', 'jenjang', 'kelas',
                'nilai_rata', 'tinggi_badan', 'berat_badan', 'golongan_darah',
                'nama_ortu', 'hp_ortu', 'hubungan_ortu', 'prestasi',
            ])
        );

        return redirect()->route('peserta.profil.edit')
                         ->with('success', 'Biodata berhasil disimpan.');
    }
}