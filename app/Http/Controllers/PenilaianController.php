<?php

namespace App\Http\Controllers;

use App\Models\Penilaian;
use App\Models\Penilaiandb;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Menyaring data penilaian berdasarkan tanggal
        $penilaian = Penilaiandb::orderBy('tgl_penilaian')->get();

        // Mengelompokkan data berdasarkan tanggal penilaian
        $groupedPenilaian = $penilaian->groupBy('tgl_penilaian');

        // Mengambil satu entri pertama dari setiap grup
        $uniquePenilaian = $groupedPenilaian->map(function ($group) {
            return $group->first();
        });

        // Mengembalikan view dengan data yang telah disaring
        return view('penilaian.index', compact('uniquePenilaian'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('penilaian.penilaian');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($tgl_penilaian)
    {
        // if (auth()->user()->role == 'Karyawan') {
        //     $penilaian = PenilaianDb::where('tgl_penilaian', $tgl_penilaian)
        //         ->orderByDesc('data->total_nilai')
        //         ->take(3)
        //         ->get();
        // } else {
        // }
        $penilaian = PenilaianDb::where('tgl_penilaian', $tgl_penilaian)
            ->orderByDesc('data->total_nilai')
            ->get();
        // dd($penilaian);
        return view('penilaian.show', compact('penilaian'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($tgl_penilaian)
    {
        $penilaian = PenilaianDb::where('tgl_penilaian', $tgl_penilaian)
            ->orderByDesc('data->total_nilai')
            ->get();

        foreach ($penilaian as $item) {
            $item->delete();
        }

        return redirect()->route('penilaian.index')->with('error', 'Penilaian berhasil dihapus');
    }
}
