@extends('master')
{{-- @section('title', 'Penilaian') --}}
@section('content')
    <div class="x_panel">
        <div class="x_title">
            <h2>Form Tambah Kriteria</h2>
            <ul class="nav navbar-right panel_toolbox">
                <li>
                    <a onclick="goBack()">
                        <i class="fa fa-close"></i>
                    </a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <br>
            <form action="{{ route('kriteria.store') }}" method="POST" enctype="multipart/form-data"
                class="form-label-left input_mask">
                @csrf
                <div class="form-group row">
                    <div class="col-md-4 col-sm-4  form-group has-feedback">
                        <input name="nama" type="text" class="form-control has-feedback-left" id="inputSuccess2"
                            placeholder="Nama">
                        <span class="fa fa-bar-chart form-control-feedback left" aria-hidden="true"></span>
                    </div>

                    <div class="col-md-4 col-sm-4  form-group has-feedback">
                        <input name="bobot" type="text" class="form-control" id="inputSuccess3" placeholder="Bobot">
                        <span class="fa fa-calculator form-control-feedback
                            right"
                            aria-hidden="true"></span>
                    </div>

                    <div class="col-md-4 col-sm-4  form-group has-feedback">
                        <input name="keterangan" type="text" class="form-control" id="inputSuccess3"
                            placeholder="Keterangan">
                        <span class="fa fa-info form-control-feedback
                            right"
                            aria-hidden="true"></span>
                    </div>
                </div>
                <div class="ln_solid"></div>
                <div class="form-group row">
                    <div class="col-md-12 col-sm-12  offset-md-5">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        function goBack() {
            window.history.back();
        }
    </script>
@endsection
