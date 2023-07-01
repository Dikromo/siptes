@extends('admin.layouts.main')

@section('container')
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2/css/select2.min.css') }}">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="float-sm-right">
                    <a class="btn btn-warning btn-sm" href="/jmosip">
                        <i class="fas fa-arrow-left">
                        </i>
                        Back
                    </a>
                </div>
            </div>
            <br>
            <br>
            <div class="col-12">
                <form action="/jmosip{{ $data != '' ? '/' . encrypt($data->id) : '' }}" id="formJmosip"
                    enctype="multipart/form-data" method="POST">
                    @if ($data != '')
                        @method('put')
                    @endif
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Form {{ $data != '' ? 'Edit ' . $title : 'Tambah ' . $title }}</h3>
                            <div class="card-tools">
                                {{-- <div class="input-group input-group-sm" style="width: 150px;">
                                <input type="text" name="table_search" class="form-control float-right"
                                    placeholder="Search">

                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div> --}}
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body ">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nama">Nama</label>
                                        <input type="text" id="nama" name="nama"
                                            class="form-control @error('nama') is-invalid @enderror"
                                            value="{{ $data == '' ? old('nama') : old('nama', $data->nama) }}" required>
                                        @error('nama')
                                            <span id="nama" class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" id="email" name="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            value="{{ $data == '' ? old('email') : old('email', $data->email) }}" required>
                                        @error('email')
                                            <span id="email" class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="nokartu">No Kartu</label>
                                        <input type="text" id="nokartu" name="nokartu"
                                            class="form-control @error('nokartu') is-invalid @enderror"
                                            value="{{ $data == '' ? old('nokartu') : old('nokartu', $data->nokartu) }}"
                                            required>
                                        @error('nokartu')
                                            <span id="nokartu" class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="perusahaan">Perusahaan</label>
                                        <input type="text" id="perusahaan" name="perusahaan"
                                            class="form-control @error('perusahaan') is-invalid @enderror"
                                            value="{{ $data == '' ? old('perusahaan') : old('perusahaan', $data->perusahaan) }}"
                                            required>
                                        @error('perusahaan')
                                            <span id="perusahaan" class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="lastUpah">Upah Terakhir</label>
                                        <input type="number" id="lastUpah" name="lastUpah"
                                            class="form-control @error('lastUpah') is-invalid @enderror"
                                            value="{{ $data == '' ? old('lastUpah') : old('lastUpah', $data->lastUpah) }}"
                                            required>
                                        @error('lastUpah')
                                            <span id="lastUpah" class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input type="password" id="password" name="password"
                                            class="form-control @error('password') is-invalid @enderror" required>
                                        @error('password')
                                            <span id="password" class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="segmenPeserta">Segmen Peserta</label>
                                        <input type="text" id="segmenPeserta" name="segmenPeserta"
                                            class="form-control @error('segmenPeserta') is-invalid @enderror"
                                            value="{{ $data == '' ? old('segmenPeserta') : old('segmenPeserta', $data->segmenPeserta) }}"
                                            required>
                                        @error('segmenPeserta')
                                            <span id="segmenPeserta" class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="lastIuranDate">Pembayaran Iuran Terakhir</label>
                                                <input type="text" id="lastIuranDate" name="lastIuranDate"
                                                    class="form-control @error('lastIuranDate') is-invalid @enderror"
                                                    value="{{ $data == '' ? old('lastIuranDate') : old('lastIuranDate', $data->lastIuranDate) }}"
                                                    required>
                                                @error('lastIuranDate')
                                                    <span id="lastIuranDate"
                                                        class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="pensiunanDate">Tanggal Usia Pensiun</label>
                                                <input type="text" id="pensiunanDate" name="pensiunanDate"
                                                    class="form-control @error('pensiunanDate') is-invalid @enderror"
                                                    value="{{ $data == '' ? old('pensiunanDate') : old('pensiunanDate', $data->pensiunanDate) }}"
                                                    required>
                                                @error('pensiunanDate')
                                                    <span id="pensiunanDate"
                                                        class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="masaIuranjp">Masa Iuran Jaminan Pensiun</label>
                                        <input type="text" id="masaIuranjp" name="masaIuranjp"
                                            class="form-control @error('masaIuranjp') is-invalid @enderror"
                                            value="{{ $data == '' ? old('masaIuranjp') : old('masaIuranjp', $data->masaIuranjp) }}"
                                            required>
                                        @error('masaIuranjp')
                                            <span id="masaIuranjp" class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="kepesertaanDate">Tanggal Kepesertaan Awal JKP</label>
                                                <input type="text" id="kepesertaanDate" name="kepesertaanDate"
                                                    class="form-control @error('kepesertaanDate') is-invalid @enderror"
                                                    value="{{ $data == '' ? old('kepesertaanDate') : old('kepesertaanDate', $data->kepesertaanDate) }}"
                                                    required>
                                                @error('kepesertaanDate')
                                                    <span id="kepesertaanDate"
                                                        class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="masaIuranjkp">Masa Iuran JKP</label>
                                                <input type="text" id="masaIuranjkp" name="masaIuranjkp"
                                                    class="form-control @error('masaIuranjkp') is-invalid @enderror"
                                                    value="{{ $data == '' ? old('masaIuranjkp') : old('masaIuranjkp', $data->masaIuranjkp) }}"
                                                    required>
                                                @error('masaIuranjkp')
                                                    <span id="masaIuranjkp"
                                                        class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="cardpath">Gambar</label>
                                        <div class="custom-file">
                                            <input type="file" id="cardpath" name="cardpath"
                                                class="custom-file-input @error('cardpath') is-invalid @enderror"
                                                value="{{ old('cardpath') }}">
                                            <label class="custom-file-label" for="cardpath">Choose file</label>
                                            @error('cardpath')
                                                <span id="cardpath"
                                                    class="error invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <?php
                                    
                                    //{{ ($data == '' ? (old('masaIuranjkp') ? '' : 'checked') : $data->jkm == '1') ? 'checked' : '' }}>{{ $data->jkm }}
                                    if ($data != '') {
                                        $cJKM = $data->jkm;
                                        $cJKK = $data->jkk;
                                        $cJHT = $data->jht;
                                        $cJP = $data->jp;
                                        $cJKP = $data->jkp;
                                    } else {
                                        $cJKM = old('jkm');
                                        $cJKK = old('jkk');
                                        $cJHT = old('jht');
                                        $cJP = old('jp');
                                        $cJKP = old('jkp');
                                    }
                                    
                                    switch ($cJKM) {
                                        case '1':
                                            $jkm = 'checked';
                                            break;
                                    
                                        default:
                                            # code...
                                            $jkm = '';
                                            break;
                                    }
                                    switch ($cJKK) {
                                        case '1':
                                            $jkk = 'checked';
                                            break;
                                    
                                        default:
                                            # code...
                                            $jkk = '';
                                            break;
                                    }
                                    switch ($cJHT) {
                                        case '1':
                                            $jht = 'checked';
                                            break;
                                    
                                        default:
                                            # code...
                                            $jht = '';
                                            break;
                                    }
                                    switch ($cJP) {
                                        case '1':
                                            $jp = 'checked';
                                            break;
                                    
                                        default:
                                            # code...
                                            $jp = '';
                                            break;
                                    }
                                    switch ($cJKP) {
                                        case '1':
                                            $jkp = 'checked';
                                            break;
                                    
                                        default:
                                            # code...
                                            $jkp = '';
                                            break;
                                    }
                                    ?>
                                    <div class="form-group">
                                        <label>Program Yang Diikuti</label>
                                        <div class="row">
                                            <div class="col-md-1"></div>
                                            <div class="col-md-2">
                                                <div class="custom-control custom-checkbox">
                                                    <input name="jkm" class="custom-control-input" type="checkbox"
                                                        id="jkm" value="1" {{ $jkm }}>
                                                    <label for="jkm" class="custom-control-label">JKM</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="custom-control custom-checkbox">
                                                    <input name="jkk" class="custom-control-input" type="checkbox"
                                                        id="jkk" value="1" {{ $jkk }}>
                                                    <label for="jkk" class="custom-control-label">JKK</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="custom-control custom-checkbox">
                                                    <input name="jht" class="custom-control-input" type="checkbox"
                                                        id="jht" value="1" {{ $jht }}>
                                                    <label for="jht" class="custom-control-label">JHT</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="custom-control custom-checkbox">
                                                    <input name="jp" class="custom-control-input" type="checkbox"
                                                        id="jp" value="1" {{ $jp }}>
                                                    <label for="jp" class="custom-control-label">JP</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="custom-control custom-checkbox">
                                                    <input name="jkp" class="custom-control-input" type="checkbox"
                                                        id="jkp" value="1" {{ $jkp }}>
                                                    <label for="jkp" class="custom-control-label">JKP</label>
                                                </div>
                                            </div>
                                            <div class="col-md-1"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <div class="row">
                                <!-- /.col -->
                                <div class="col-8">
                                </div>
                                <div class="col-4">
                                    <button type="submit" class="btn btn-primary btn-block">Simpan</button>
                                </div>
                                <!-- /.col -->
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </form>
            </div>
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
@endsection
@section('addScript')
    <script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $('.select2').select2()
        $('#formJmosip').submit(function() {
            // $('#modal-overlay').modal({
            //     backdrop: 'static',
            //     keyboard: false
            // });
            return true;
        });
    </script>
@endsection
