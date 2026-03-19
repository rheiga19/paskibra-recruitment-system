<?php

namespace App\Http\Controllers\Panitia;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;

class VerifikasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Pendaftaran::latest();

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('no_pendaftaran', 'like', "%{$search}%");
            });
        }

        if ($status = $request->status) $query->where('status', $status);
        if ($jk     = $request->jk)     $query->where('jenis_kelamin', $jk);

        $pendaftaran = $query->with('dokumen')->paginate(15)->withQueryString();

        return view('panitia.verifikasi.index', [
            'pendaftaran' => $pendaftaran,
            'total'       => Pendaftaran::count(),
            'menunggu'    => Pendaftaran::where('status', 'menunggu')->count(),
            'diterima'    => Pendaftaran::where('status', 'diverifikasi')->count(),
            'ditolak'     => Pendaftaran::where('status', 'ditolak')->count(),
        ]);
    }

    public function show(Pendaftaran $pendaftaran)
    {
        $pendaftaran->load('dokumen');

        $dokumen = $pendaftaran->dokumen->keyBy('jenis');

        $ids  = Pendaftaran::orderBy('id')->pluck('id');
        $idx  = $ids->search($pendaftaran->id);
        $prev = $idx > 0                 ? Pendaftaran::find($ids[$idx - 1]) : null;
        $next = $idx < $ids->count() - 1 ? Pendaftaran::find($ids[$idx + 1]) : null;

        return view('panitia.verifikasi.show', compact('pendaftaran', 'dokumen', 'prev', 'next'));
    }

    public function proses(Request $request, Pendaftaran $pendaftaran)
    {
        $request->validate([
            'keputusan' => ['required', 'in:diverifikasi,ditolak'],
            'catatan'   => ['nullable', 'string', 'max:500'],
        ]);

        $pendaftaran->update([
            'status'             => $request->keputusan,
            'catatan_verifikasi' => $request->catatan,
            'diverifikasi_oleh'  => auth()->id(),
            'diverifikasi_at'    => now(),
        ]);

        $pesan = $request->keputusan === 'diverifikasi'
            ? "Pendaftaran {$pendaftaran->nama_lengkap} berhasil diterima."
            : "Pendaftaran {$pendaftaran->nama_lengkap} ditolak.";

        return redirect()->route('panitia.verifikasi.index')->with('success', $pesan);
    }
}