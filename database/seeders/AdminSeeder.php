<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ─────────────────────────────────────────────────
        User::create([
            'name'     => 'Administrator',
            'email'    => 'admin@paskibra.id',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        // ── Panitia ───────────────────────────────────────────────
        User::create([
            'name'     => 'Panitia Seleksi',
            'email'    => 'panitia@paskibra.id',
            'password' => Hash::make('panitia123'),
            'role'     => 'panitia',
        ]);

        $this->command->info('✅ AdminSeeder selesai — 1 admin, 1 panitia');
    }
}