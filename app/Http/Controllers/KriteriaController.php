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
        $data = new Kriteria();
        $data->nama = $request->nama;
        $data->bobot = $request->bobot;
        $data->keterangan = $request->keterangan;
        $data->save();
        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil ditambahkan');
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
