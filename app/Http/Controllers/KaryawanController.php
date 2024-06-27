<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $karyawan = Karyawan::all();
        return view('karyawan.index', compact('karyawan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('karyawan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = new Karyawan();
        $data->nama = $request->nama;
        $data->jabatan = $request->jabatan;
        $data->save();

        $user = new User();
        $user->name = $request->nama;
        $user->username = $request->username;
        $user->password = Hash::make($request->password);
        $user->role = 'Karyawan';
        $user->save();

        return redirect()->route('karyawan.index')->with('success', 'Karyawan Berhasil di Tambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Karyawan $karyawan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = Karyawan::find($id);
        return view('karyawan.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = Karyawan::find($id);
        $data->nama = $request->nama;
        $data->jabatan = $request->jabatan;
        $data->save();

        return redirect()->route('karyawan.index')->with('success', 'Karyawan Berhasil di Update.');
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $karyawan = Karyawan::find($id);

        if ($karyawan->penilaian()->exists()) {
            return redirect()->route('karyawan.index')->with('error', 'Karyawan tidak dapat dihapus karena memiliki penilaian.');
        }

        $karyawan->delete();

        return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil dihapus');
    }
}
