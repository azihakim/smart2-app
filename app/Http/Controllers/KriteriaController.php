<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use Illuminate\Http\Request;

class KriteriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Kriteria::all();
        return view('kriteria.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('kriteria.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data yang diterima dari form
        $request->validate([
            'nama' => 'required|string|max:255',
            'bobot' => 'required|numeric|min:0|max:100', // Bobot harus numerik dan maksimum 100
            'keterangan' => 'nullable|string|max:255',
        ]);

        // Hitung total bobot yang sudah ada di database
        $totalBobot = Kriteria::sum('bobot');

        // Hitung total bobot setelah menambahkan bobot baru
        $newBobot = $request->bobot;
        $totalBobotSaatIni = $totalBobot + $newBobot;

        // Validasi jika total bobot melebihi 100
        if ($totalBobotSaatIni > 100) {
            return redirect()->back()->with('error', 'Bobot melebihi 100%. Sisa bobot yang tersedia: ' . (100 - $totalBobot) . '%.');
        }

        // Simpan data kriteria
        Kriteria::create([
            'nama' => $request->nama,
            'bobot' => $request->bobot,
            'keterangan' => $request->keterangan,
        ]);

        // Redirect atau tampilkan pesan sukses jika berhasil disimpan
        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil ditambahkan.');
    }


    /**
     * Display the specified resource.
     */
    public function show(Kriteria $kriteria)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = Kriteria::find($id);
        return view('kriteria.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = Kriteria::find($id);
        $data->nama = $request->nama;
        $data->bobot = $request->bobot;
        $data->keterangan = $request->keterangan;
        $data->save();
        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = Kriteria::find($id);
        $data->delete();
        return redirect()->route('kriteria.index')->with('error', 'Kriteria berhasil dihapus');
    }
}
