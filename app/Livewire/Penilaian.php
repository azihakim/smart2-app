<?php

namespace App\Livewire;

use App\Models\Karyawan;
use App\Models\Kriteria;
use App\Models\Penilaiandb;
use App\Models\SubKriteria;
use Livewire\Component;

class Penilaian extends Component
{
    public $karyawans;
    public $id_karyawan = [];
    public $nama_karyawan = [];
    public $jabatan_karyawan = [];
    public $kriteria = [];
    public $subkriteria = [];
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
        $this->subkriteria = SubKriteria::all();
    }

    // public function mount()
    // {
    //     $this->karyawans = Karyawan::all();

    //     // Initialize arrays to store data
    //     $this->id_karyawan = [];
    //     $this->nama_karyawan = [];
    //     $this->jabatan_karyawan = [];
    //     $this->nilai = []; // Adjust to fit your structure

    //     // Initialize karyawan data
    //     foreach ($this->karyawans as $karyawan) {
    //         $this->id_karyawan[$karyawan->id] = $karyawan->id;
    //         $this->nama_karyawan[$karyawan->id] = $karyawan->nama;
    //         $this->jabatan_karyawan[$karyawan->id] = $karyawan->jabatan;

    //         // Initialize nilai for each karyawan and subkriteria
    //         $this->nilai[$karyawan->id] = [];

    //         // Fetch subkriteria for each kriteria
    //         $kriteria = Kriteria::with('subkriteria')->get();

    //         foreach ($kriteria as $item) {
    //             // Ensure the relationship method 'subkriteria' is correctly defined in Kriteria model
    //             foreach ($item->subkriteria as $subitem) {
    //                 // Initialize nilai for each subkriteria
    //                 $this->nilai[$karyawan->id][$subitem->id][$subitem->nama] = 0;
    //             }
    //         }
    //     }
    // }


    public function render()
    {
        return view('livewire.penilaian');
    }

    public function addNilai()
    {
        $mappingKriteria = [];
        $subs = SubKriteria::select('nama', 'kriteria_id', 'bobot')->get();
        foreach ($subs as $sub) {
            // $sub->nama adalah nama sub sub kriteria
            // $sub->kriteria->nama adalah nama kriteria yang sesuai
            $mappingKriteria[$sub->nama] = [
                'kriteria' => $sub->kriteria->nama,
                'bobotSub' => $sub->bobot, // Bobot dari sub kriteria
                'bobotKriteria' => $sub->kriteria->bobot, // Bobot dari kriteria yang terkait
            ];
        }

        // Iterasi untuk setiap karyawan
        foreach ($this->nilai as $karyawan => $subkriteria) {
            // Inisialisasi total nilai kriteria untuk karyawan ini
            $totalNilaiKriteria = [];

            // Inisialisasi total nilai kriteria berdasarkan mapping
            foreach ($mappingKriteria as $subSubKriteria => $info) {
                $kriteriaNama = $info['kriteria'];
                $totalNilaiKriteria[$kriteriaNama] = 0;
            }

            // Iterasi untuk setiap sub sub kriteria dari karyawan ini
            foreach ($subkriteria as $no_subkriteria => $nilai) {
                // Jika nilai sub sub kriteria tidak kosong, tambahkan nilainya ke total kriteria yang sesuai
                if (!empty($nilai)) {
                    // Ambil nilai dari array pertama yang ada (seharusnya hanya ada satu nilai per array)
                    $nilai = reset($nilai);
                    $subSubKriteria = key($this->nilai[$karyawan][$no_subkriteria]);
                    $info = $mappingKriteria[$subSubKriteria];
                    $kriteria = $info['kriteria'];
                    $bobotSub = $info['bobotSub'];
                    $bobotKriteria = $info['bobotKriteria'];

                    // Hitung nilai akhir dengan mengalikan nilai dengan bobot sub kriteria dan faktor 0.01,
                    // kemudian dikalikan dengan bobot kriteria dan faktor 0.01
                    $jumlah = (int) ($nilai * $bobotSub) * 0.01;
                    $nilaiAkhir = ($jumlah / 3) * ($bobotKriteria * 0.01);
                    // $nilaiAkhir = (int) ($nilai * $bobotSub * 0.01) * ($bobotKriteria * 0.01);

                    // Tambahkan nilai akhir ke total kriteria yang sesuai
                    $totalNilaiKriteria[$kriteria] += $nilaiAkhir;
                }
            }

            // Simpan hasil perhitungan untuk karyawan ini ke dalam array total nilai karyawan
            $totalNilaiKaryawan[$karyawan] = $totalNilaiKriteria;
        }

        // Simpan hasil perhitungan ke dalam properti khusus
        // dd($totalNilaiKaryawan);
        return $totalNilaiKaryawan;
    }

    public function normalizeData()
    {
        $totalNilaiKaryawan = $this->addNilai();

        $normalizedData = [];

        foreach ($totalNilaiKaryawan as $karyawanId => $subkriteria) {
            foreach ($subkriteria as $namaKriteria => $nilai) {
                // Normalisasi nilai dengan mengalikan dengan 0.01
                $normalizedData[$karyawanId][$namaKriteria] = $nilai * 0.01;
            }
        }

        // dd($normalizedData);
        return $normalizedData;
    }

    public function getMaxMinValues()
    {
        $normalizedData = $this->normalizeData();

        $maxValues = [];
        $minValues = [];

        // Inisialisasi maxValues dan minValues dengan nilai awal yang sesuai
        foreach ($normalizedData as $set) {
            foreach ($set as $namaKriteria => $nilai) {
                $maxValues[$namaKriteria] = $nilai;
                $minValues[$namaKriteria] = $nilai;
            }
            // Kita hanya perlu inisialisasi sekali karena nilai sudah pasti ada setelah di normalize
            break;
        }

        // Iterasi untuk mencari nilai maksimum dan minimum
        foreach ($normalizedData as $set) {
            foreach ($set as $namaKriteria => $nilai) {
                if ($nilai > $maxValues[$namaKriteria]) {
                    $maxValues[$namaKriteria] = $nilai;
                }
                if ($nilai < $minValues[$namaKriteria]) {
                    $minValues[$namaKriteria] = $nilai;
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

        foreach ($normalizedData as $karyawanId => $kriteriaSet) {
            foreach ($kriteriaSet as $namaKriteria => $nilai) {
                // Perhitungan utility value
                if ($this->isBenefitKriteria($namaKriteria)) {
                    // Jika kriteria adalah "Benefit", hitung utility sesuai rumus
                    $nilaiMin = $minValues[$namaKriteria];
                    $nilaiMax = $maxValues[$namaKriteria];

                    // Handle division by zero case if nilaiMax == nilaiMin
                    if ($nilaiMax == $nilaiMin) {
                        $utilityValues[$karyawanId][$namaKriteria] = 0; // or handle as needed
                    } else {
                        $utilityValues[$karyawanId][$namaKriteria] = ($nilai - $nilaiMin) / ($nilaiMax - $nilaiMin);
                    }
                } else {
                    // Jika kriteria bukan "Benefit", normalisasi nilai dengan mengalikan dengan 0.01
                    $utilityValues[$karyawanId][$namaKriteria] = $nilai * 0.01;
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
