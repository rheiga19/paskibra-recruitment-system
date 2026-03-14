<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Galeri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GaleriController extends Controller
{
    public function index()
    {
        $galeri = Galeri::latest()->paginate(18);
        return view('admin.galeri.index', compact('galeri'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'foto'    => ['required', 'array', 'min:1'],
            'foto.*'  => ['image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'judul'   => ['nullable', 'string', 'max:200'],
            'keterangan' => ['nullable', 'string', 'max:300'],
        ]);

        foreach ($request->file('foto') as $i => $file) {
            $path = $file->store('galeri', 'public');

            Galeri::create([
                'judul'      => $request->judul ?: $file->getClientOriginalName(),
                'path'       => $path,
                'keterangan' => $request->keterangan,
            ]);
        }

        $jumlah = count($request->file('foto'));
        return back()->with('success', $jumlah . ' foto berhasil diupload.');
    }

    public function update(Request $request, Galeri $galeri)
    {
        $request->validate([
            'judul'      => ['required', 'string', 'max:200'],
            'keterangan' => ['nullable', 'string', 'max:300'],
        ]);

        $galeri->update($request->only('judul', 'keterangan'));

        return back()->with('success', 'Info foto berhasil diupdate.');
    }

    public function destroy(Galeri $galeri)
    {
        Storage::disk('public')->delete($galeri->path);
        $galeri->delete();

        return back()->with('success', 'Foto berhasil dihapus.');
    }
}