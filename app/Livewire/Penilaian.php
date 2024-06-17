<?php

namespace App\Livewire;

use App\Models\Karyawan;
use App\Models\Kriteria;
use App\Models\Penilaiandb;
use Livewire\Component;

class Penilaian extends Component
{
    public $karyawans;
    public $id_karyawan = [];
    public $nama_karyawan = [];
    public $jabatan_karyawan = [];
    public $kriteria = [];
    public $nilai = [];

    public function mount()
    {
        $this->karyawans = Karyawan::all();

        foreach ($this->karyawans as $karyawan) {
            $this->id_karyawan[$karyawan->id] = $karyawan->id;
            $this->nama_karyawan[$karyawan->id] = $karyawan->nama;
            $this->jabatan_karyawan[$karyawan->id] = $karyawan->jabatan;
        }

        $this->kriteria = Kriteria::all();
    }

    public function render()
    {
        return view('livewire.penilaian');
    }

    public function normalizeData()
    {
        $normalizedData = [];

        foreach ($this->nilai as $setKey => $set) {
            foreach ($set as $kriteriaKey => $kriteria) {
                foreach ($kriteria as $namaKriteria => $nilai) {
                    // Normalisasi nilai dengan mengalikan dengan 0.01
                    $normalizedData[$setKey][$kriteriaKey][$namaKriteria] = $nilai * 0.01;
                }
            }
        }
        return $normalizedData;
        // dd($normalizedData);
    }
    public function getMaxMinValues()
    {
        $normalizedData = $this->normalizeData();

        $maxValues = [];
        foreach ($normalizedData as $set) {
            foreach ($set as $kriteria) {
                foreach ($kriteria as $namaKriteria => $nilai) {
                    if (!isset($maxValues[$namaKriteria])) {
                        $maxValues[$namaKriteria] = $nilai;
                    } else {
                        if ($nilai > $maxValues[$namaKriteria]) {
                            $maxValues[$namaKriteria] = $nilai;
                        }
                    }
                }
            }
        }
        $minValues = [];
        foreach ($normalizedData as $set) {
            foreach ($set as $kriteria) {
                foreach ($kriteria as $namaKriteria => $nilai) {
                    if (!isset($minValues[$namaKriteria])) {
                        $minValues[$namaKriteria] = $nilai;
                    } else {
                        if ($nilai < $minValues[$namaKriteria]) {
                            $minValues[$namaKriteria] = $nilai;
                        }
                    }
                }
            }
        }
        // dd($maxValues, $minValues);
        return [$maxValues, $minValues];
    }

    private function isBenefitKriteria($namaKriteria)
    {
        // Dapatkan kriteria berdasarkan nama
        $kriteria = $this->kriteria->firstWhere('nama', $namaKriteria);

        // Periksa apakah kriteria memiliki keterangan "Benefit"
        return $kriteria && $kriteria->keterangan == 'Benefit';
    }
    public function getUtilityValues()
    {
        list($maxValues, $minValues) = $this->getMaxMinValues();
        $normalizedData = $this->normalizeData();
        $utilityValues = [];

        foreach ($normalizedData as $setKey => $set) {
            foreach ($set as $kriteriaKey => $kriteria) {
                foreach ($kriteria as $namaKriteria => $nilai) {
                    // Perhitungan utility value
                    if ($this->isBenefitKriteria($namaKriteria)) {
                        // Jika kriteria adalah "Benefit", hitung utility sesuai rumus
                        $nilaiMin = $minValues[$namaKriteria];
                        $nilaiMax = $maxValues[$namaKriteria];

                        // Handle division by zero case if nilaiMax == nilaiMin
                        if ($nilaiMax == $nilaiMin) {
                            $utilityValues[$setKey][$namaKriteria] = 0; // or handle as needed
                        } else {
                            $utilityValues[$setKey][$namaKriteria] = ($nilai - $nilaiMin) / ($nilaiMax - $nilaiMin);
                        }
                    } else {
                        // Jika kriteria bukan "Benefit", normalisasi nilai agar bisa menerima nilai minus
                        // Misalnya, jika ingin memberikan nilai minus dengan cara lain, sesuaikan di sini
                        $utilityValues[$setKey][$namaKriteria] = $nilai * 0.01; // Contoh normalisasi untuk nilai minus
                    }
                }
            }
        }

        // dd($utilityValues);
        return $utilityValues;
    }

    // public function hasilAkhir()
    // {
    //     $utilityValues = $this->getUtilityValues();
    //     $hasilAkhir = [];
    //     $totalSum = 0;

    //     foreach ($utilityValues as $setKey => $set) {
    //         $totalNilai = 0;
    //         $perKriteria = [];

    //         foreach ($set as $namaKriteria => $utility) {
    //             $kriteria = $this->kriteria->firstWhere('nama', $namaKriteria);

    //             if ($kriteria) {
    //                 $bobot = (float) $kriteria->bobot;
    //                 $nilaiPerKriteria = $utility / ($bobot * 0.01);

    //                 $perKriteria[$namaKriteria] = $nilaiPerKriteria;
    //                 $totalNilai += $nilaiPerKriteria;
    //             }
    //         }

    //         $hasilAkhir[$setKey]['nilai_per_kriteria'] = $perKriteria;
    //         $hasilAkhir[$setKey]['total_nilai'] = $totalNilai;
    //     }

    //     // Urutkan hasil akhir berdasarkan total_nilai dari yang tertinggi ke terendah
    //     usort($hasilAkhir, function ($a, $b) {
    //         return $b['total_nilai'] <=> $a['total_nilai']; // Mengurutkan dari nilai total_nilai tertinggi ke terendah
    //     });

    //     // Berikan peringkat pada hasil akhir
    //     $ranking = 1;
    //     foreach ($hasilAkhir as $key => &$item) {
    //         $item['rank'] = $ranking;
    //         $ranking++;
    //     }
    //     dd($hasilAkhir);
    // }
    public function hasilAkhir()
    {
        $utilityValues = $this->getUtilityValues();
        $hasilAkhir = [];
        $totalSum = 0;

        foreach ($utilityValues as $setKey => $set) {
            $totalNilai = 0;
            $perKriteria = [];

            $idKaryawan = $this->id_karyawan[$setKey];
            $namaKaryawan = $this->nama_karyawan[$setKey];

            foreach ($set as $namaKriteria => $utility) {
                $kriteria = $this->kriteria->firstWhere('nama', $namaKriteria);

                if ($kriteria) {
                    $bobot = (float) $kriteria->bobot;
                    $nilaiPerKriteria = $utility / ($bobot * 0.01);

                    $perKriteria[$namaKriteria] = $nilaiPerKriteria;
                    $totalNilai += $nilaiPerKriteria;
                }
            }

            $hasilAkhir[$setKey]['id_karyawan'] = $idKaryawan;
            $hasilAkhir[$setKey]['tgl_penilaian'] = now()->toDateString(); // Tanggal penilaian saat ini
            $hasilAkhir[$setKey]['data'] = [
                'nama_karyawan' => $namaKaryawan,
                'nilai_per_kriteria' => $perKriteria,
                'total_nilai' => $totalNilai,
            ];

            // Simpan ke dalam tabel penilaian menggunakan model Penilaian
            Penilaiandb::create([
                'karyawan_id' => $idKaryawan,
                'tgl_penilaian' => now()->toDateString(),
                'data' => json_encode($hasilAkhir[$setKey]['data']),
            ]);
        }
        // dd($hasilAkhir);

        // Urutkan dan beri peringkat seperti sebelumnya jika diperlukan
        return redirect()->route('penilaian.index');
    }
}
