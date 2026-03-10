<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

// ─── Publik ──────────────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

// ─── Redirect dashboard berdasarkan role ─────────────────────────────────────
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->isAdmin())   return redirect()->route('admin.dashboard');
    if ($user->isPanitia()) return redirect()->route('panitia.dashboard');
    return redirect()->route('peserta.dashboard');
})->middleware('auth')->name('dashboard');

// ─── Breeze Auth ─────────────────────────────────────────────────────────────
require __DIR__.'/auth.php';

// ─── Peserta ─────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:peserta'])->prefix('peserta')->name('peserta.')->group(function () {
    Route::get('/dashboard', function () { return view('peserta.dashboard'); })->name('dashboard');
});

// ─── Admin ───────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () { return view('admin.dashboard'); })->name('dashboard');
});

// ─── Panitia ─────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:panitia'])->prefix('panitia')->name('panitia.')->group(function () {
    Route::get('/dashboard', function () { return view('panitia.dashboard'); })->name('dashboard');
});