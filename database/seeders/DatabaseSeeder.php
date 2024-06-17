<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Data;
use App\Models\Karyawan;
use App\Models\Kriteria;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        User::create([
            'name' => 'Admin',
            'nip' => 'Admin',
            'role' => 'Admin',
            'username' => 'admin',
            'password' => Hash::make('123'),
        ]);

        $karyawan = [
            [
                'nama' => 'Anwar Zemmi',
                'jabatan' => 'Kantor'
            ],
            [
                'nama' => 'Faisal Riza',
                'jabatan' => 'Lapangan'
            ],
            [
                'nama' => 'M. Lendra',
                'jabatan' => 'Kantor'
            ],
            [
                'nama' => 'Nicolas Alex',
                'jabatan' => 'Lapangan'
            ],
        ];

        foreach ($karyawan as $k) {
            Karyawan::create([
                'nama' => $k['nama'],
                'jabatan' => $k['jabatan'],
            ]);
        }

        $kriteria = [
            [
                'nama' => 'Absen',
                'bobot' => '25',
                'keterangan' => 'Benefit'
            ],
            [
                'nama' => 'Tanggung Jawab',
                'bobot' => '20',
                'keterangan' => 'Benefit'
            ],
            [
                'nama' => 'Kepemimpinan',
                'bobot' => '15',
                'keterangan' => 'Benefit'
            ],
            [
                'nama' => 'Produktivitas',
                'bobot' => '15',
                'keterangan' => 'Benefit'
            ],
            [
                'nama' => 'Laporan Kegiatan',
                'bobot' => '10',
                'keterangan' => 'Benefit'
            ],
            [
                'nama' => 'Sikap',
                'bobot' => '10',
                'keterangan' => 'Benefit'
            ],
            [
                'nama' => 'Gotong Royong',
                'bobot' => '5',
                'keterangan' => 'Benefit'
            ],
        ];

        foreach ($kriteria as $k) {
            Kriteria::create([
                'nama' => $k['nama'],
                'bobot' => $k['bobot'],
                'keterangan' => $k['keterangan'],
            ]);
        }
    }
}
