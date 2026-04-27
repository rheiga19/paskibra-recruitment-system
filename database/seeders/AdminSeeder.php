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
            'email'    => 'admin@paskibra-compreng.my.id',
            'password' => Hash::make('123PASkibra.'),
            'role'     => 'admin',
        ]);

        // ── Panitia ───────────────────────────────────────────────
        User::create([
            'name'     => 'Panitia Seleksi',
            'email'    => 'panitia@paskibra-compreng.my.id',
            'password' => Hash::make('123PANitia.'),
            'role'     => 'panitia',
        ]);

        $this->command->info('✅ AdminSeeder selesai — 1 admin, 1 panitia');
    }
}