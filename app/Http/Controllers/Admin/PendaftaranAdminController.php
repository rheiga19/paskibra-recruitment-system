<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\Rekrutmen;
use Illuminate\Http\Request;

class PendaftaranAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Pendaftaran::with(['user', 'rekrutmen']);

        if ($request->filled('rekrutmen_id')) {
            $query->where('rekrutmen_id', $request->rekrutmen_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%$search%")
                  ->orWhere('no_pendaftaran', 'like', "%$search%")
                  ->orWhere('nik', 'like', "%$search%");
            });
        }

        $pendaftaran  = $query->latest()->paginate(15)->withQueryString();
        $rekrutmenList = Rekrutmen::orderByDesc('tahun')->get();

        return view('admin.pendaftaran.index', compact('pendaftaran', 'rekrutmenList'));
    }

    public function show(Pendaftaran $pendaftaran)
    {
        $pendaftaran->load(['user', 'rekrutmen', 'dokumen', 'hasilSeleksi.tahap']);
        return view('admin.pendaftaran.show', compact('pendaftaran'));
    }

    public function verifikasi(Request $request, Pendaftaran $pendaftaran)
    {
        $request->validate([
            'status'         => 'required|in:menunggu,diverifikasi,lulus,tidak_lulus',
            'catatan_admin'  => 'nullable|string|max:1000',
        ]);

        $pendaftaran->update([
            'status'        => $request->status,
            'catatan_admin' => $request->catatan_admin,
        ]);

        if ($request->status === 'lulus') {
            $pendaftaran->update([
                'is_lulus_final'      => true,
                'tanggal_lulus_final' => now(),
            ]);
        }

        return back()->with('success', 'Status pendaftaran berhasil diperbarui.');
    }

    public function destroy(Pendaftaran $pendaftaran)
    {
        $pendaftaran->delete();
        return redirect()->route('admin.pendaftaran.index')
            ->with('success', 'Data pendaftaran berhasil dihapus.');
    }
}