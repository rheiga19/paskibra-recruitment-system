<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BeritaController extends Controller
{
    public function index(Request $request)
    {
        $query = Berita::with('admin')->latest();

        if ($search = $request->search) {
            $query->where('judul', 'like', "%{$search}%");
        }
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_published', $request->status);
        }

        $berita = $query->paginate(12)->withQueryString();

        return view('admin.berita.index', compact('berita'));
    }

    public function create()
    {
        return view('admin.berita.form', ['berita' => new Berita()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul'  => ['required', 'string', 'max:200'],
            'konten' => ['required', 'string'],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $data = [
            'judul'        => $request->judul,
            'slug'         => Str::slug($request->judul) . '-' . time(),
            'konten'       => $request->konten,
            'is_published' => $request->boolean('is_published'),
            'admin_id'     => auth()->id(),
        ];

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')
                ->store('berita', 'public');
        }

        Berita::create($data);

        return redirect()->route('admin.berita.index')
            ->with('success', 'Berita berhasil ditambahkan.');
    }

    public function edit(Berita $beritum)
    {
        return view('admin.berita.form', ['berita' => $beritum]);
    }

    public function update(Request $request, Berita $beritum)
    {
        $request->validate([
            'judul'  => ['required', 'string', 'max:200'],
            'konten' => ['required', 'string'],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $data = [
            'judul'        => $request->judul,
            'konten'       => $request->konten,
            'is_published' => $request->boolean('is_published'),
        ];

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama
            if ($beritum->gambar) {
                Storage::disk('public')->delete($beritum->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('berita', 'public');
        }

        // Hapus gambar tanpa upload baru
        if ($request->hapus_gambar && !$request->hasFile('gambar')) {
            Storage::disk('public')->delete($beritum->gambar);
            $data['gambar'] = null;
        }

        $beritum->update($data);

        return redirect()->route('admin.berita.index')
            ->with('success', 'Berita berhasil diupdate.');
    }

    public function destroy(Berita $beritum)
    {
        if ($beritum->gambar) {
            Storage::disk('public')->delete($beritum->gambar);
        }
        $beritum->delete();

        return back()->with('success', 'Berita berhasil dihapus.');
    }

    public function togglePublish(Berita $beritum)
    {
        $beritum->update(['is_published' => !$beritum->is_published]);
        $status = $beritum->is_published ? 'dipublikasi' : 'disembunyikan';
        return back()->with('success', "Berita berhasil {$status}.");
    }
}