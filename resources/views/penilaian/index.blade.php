@extends('master')

@section('styles')
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif
    <div class="col-md-12 col-sm-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Penilaian</h2>
                <ul class="nav navbar-right panel_toolbox">
                    @if (Auth::user()->role != 'Karyawan')
                        <li>
                            <a href="{{ route('penilaian.create') }}"
                                style="text-decoration: none; transition: color 0.3s; color: rgb(76, 75, 75);">
                                <i class="fa fa-plus"></i> Tambah
                            </a>
                        </li>
                    @endif

                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap"
                                cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Tanggal Penilaian</th>
                                        <th style="width: 20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($uniquePenilaian as $item)
                                        <tr>
                                            <td>
                                                {{ $item->tgl_penilaian }}
                                            </td>
                                            <td>
                                                <a href="{{ route('penilaian.show', $item->created_at) }}"
                                                    class="btn btn-block btn-outline-info">Cek</a>
                                                @if (Auth::user()->role != 'Karyawan')
                                                    <form action="{{ route('penilaian.destroy', $item->created_at) }}"
                                                        method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-block btn-outline-danger">Hapus</button>
                                                    </form>
                                                @endif

                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
