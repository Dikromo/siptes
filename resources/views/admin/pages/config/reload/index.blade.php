@extends('admin.layouts.main')

@section('container')
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2/css/select2.min.css') }}">

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="fileexcel_id">Campaign</label>
                                    <select name="fileexcel_id"
                                        class="form-control select2 @error('fileexcel_id') is-invalid @enderror  "
                                        id="fileexcel_id">
                                        <option value="">-- Pilih --</option>
                                        @foreach ($fileSelect as $item)
                                            <option value="{{ $item->id }}">{{ $item->kode }}</option>
                                        @endforeach
                                    </select>
                                    @error('fileexcel_id')
                                        <span id="produk_id" class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <!-- /.col -->
                            <div class="col-8">
                            </div>
                            <div class="col-4">
                                <a class="btn btn-primary btn-block" onclick="proses()">Proses</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ $title }} Table </h3>
                                <div class="card-tools">
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center border-bottom mb-1 mt-3">
                                    <p class="text-success text-medium">
                                        TOTAL DATA
                                    </p>
                                    <p class="d-flex flex-column text-right">
                                        <span class="font-weight-bold">
                                            <i class="ion ion-android-arrow-up text-success"></i> 27654
                                        </span>
                                    </p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-bottom mb-1 mt-3">
                                    <p class="text-success text-medium">
                                        Sudah Ditelepon
                                    </p>
                                    <p class="d-flex flex-column text-right">
                                        <span class="font-weight-bold">
                                            <i class="ion ion-android-arrow-up text-success"></i> 27654
                                        </span>
                                    </p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-bottom mb-1 mt-3">
                                    <p class="text-success text-medium">
                                        Belum Ditelepon
                                    </p>
                                    <p class="d-flex flex-column text-right">
                                        <span class="font-weight-bold">
                                            <i class="ion ion-android-arrow-up text-success"></i> 27654
                                        </span>
                                    </p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-bottom mb-1 mt-3">
                                    <p class="text-success text-medium">
                                        Reload
                                    </p>
                                    <p class="d-flex flex-column text-right">
                                        <span class="font-weight-bold">
                                            <i class="ion ion-android-arrow-up text-success"></i> 27654
                                        </span>
                                    </p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-bottom mb-3 mt-5">
                                    <p class="text-danger text-medium">
                                        THREE
                                    </p>
                                    <p class="d-flex flex-column text-right">
                                        <span class="font-weight-bold">
                                            <i class="ion ion-android-arrow-up text-success"></i> 27654
                                        </span>
                                    </p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-bottom mb-1 mt-3">
                                    <p class="text-danger text-medium">
                                        SIMPATI
                                    </p>
                                    <p class="d-flex flex-column text-right">
                                        <span class="font-weight-bold">
                                            <i class="ion ion-android-arrow-up text-success"></i> 27654
                                        </span>
                                    </p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-bottom mb-1 mt-3">
                                    <p class="text-danger text-medium">
                                        INDOSAT
                                    </p>
                                    <p class="d-flex flex-column text-right">
                                        <span class="font-weight-bold">
                                            <i class="ion ion-android-arrow-up text-success"></i> 27654
                                        </span>
                                    </p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-bottom mb-1 mt-3">
                                    <p class="text-danger text-medium">
                                        XL
                                    </p>
                                    <p class="d-flex flex-column text-right">
                                        <span class="font-weight-bold">
                                            <i class="ion ion-android-arrow-up text-success"></i> 27654
                                        </span>
                                    </p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-bottom mb-1 mt-3">
                                    <p class="text-danger text-medium">
                                        SMART
                                    </p>
                                    <p class="d-flex flex-column text-right">
                                        <span class="font-weight-bold">
                                            <i class="ion ion-android-arrow-up text-success"></i> 27654
                                        </span>
                                    </p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-bottom mb-1 mt-3">
                                    <p class="text-danger text-medium">
                                        TIDAK DITEMUKAN
                                    </p>
                                    <p class="d-flex flex-column text-right">
                                        <span class="font-weight-bold">
                                            <i class="ion ion-android-arrow-up text-success"></i> 27654
                                        </span>
                                    </p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-bottom mb-1 mt-3">
                                    <p class="text-info text-medium">
                                        TOTAL
                                    </p>
                                    <p class="d-flex flex-column text-right">
                                        <span class="font-weight-bold">
                                            <i class="ion ion-android-arrow-up text-success"></i> 27654
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            {{-- <div class="list-data card-footer">
                    </div> --}}
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ $title }} Table </h3>
                                <div class="card-tools">
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                {{-- looping --}}
                                <div class="form-group row">
                                    <label for="teleponkembali" class="col-sm-6 col-form-label">Telepon Kembali</label>
                                    <div class="col-sm-3">
                                        <input type="text" id="teleponkembali" name="teleponkembali"
                                            class="form-control @error('teleponkembali') is-invalid @enderror"
                                            value="{{ $data == '' ? old('teleponkembali') : old('teleponkembali', $data->teleponkembali) }}"readonly>
                                    </div>
                                    @error('teleponkembali')
                                        <span id="username" class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <div class="col-sm-3">
                                        <?php
                                        $arrayYesno = ['YES', 'NO'];
                                        ?>
                                        <select name="yesno"
                                            class="form-control select2 @error('yesno') is-invalid @enderror  "
                                            id="yesno">
                                            <option value="">-- Pilih --</option>
                                            @foreach ($arrayYesno as $item)
                                                <option value="{{ $item }}">
                                                    {{ $item }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="teleponkembali" class="col-sm-6 col-form-label">Telepon Kembali</label>
                                    <div class="col-sm-3">
                                        <input type="text" id="teleponkembali" name="teleponkembali"
                                            class="form-control @error('teleponkembali') is-invalid @enderror"
                                            value="{{ $data == '' ? old('teleponkembali') : old('teleponkembali', $data->teleponkembali) }}"readonly>
                                    </div>
                                    @error('teleponkembali')
                                        <span id="username" class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <div class="col-sm-3">
                                        <?php
                                        $arrayYesno = ['YES', 'NO'];
                                        ?>
                                        <select name="yesno"
                                            class="form-control select2 @error('yesno') is-invalid @enderror  "
                                            id="yesno">
                                            <option value="">-- Pilih --</option>
                                            @foreach ($arrayYesno as $item)
                                                <option value="{{ $item }}">
                                                    {{ $item }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="teleponkembali" class="col-sm-6 col-form-label">Telepon Kembali</label>
                                    <div class="col-sm-3">
                                        <input type="text" id="teleponkembali" name="teleponkembali"
                                            class="form-control @error('teleponkembali') is-invalid @enderror"
                                            value="{{ $data == '' ? old('teleponkembali') : old('teleponkembali', $data->teleponkembali) }}"readonly>
                                    </div>
                                    @error('teleponkembali')
                                        <span id="username" class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <div class="col-sm-3">
                                        <?php
                                        $arrayYesno = ['YES', 'NO'];
                                        ?>
                                        <select name="yesno"
                                            class="form-control select2 @error('yesno') is-invalid @enderror  "
                                            id="yesno">
                                            <option value="">-- Pilih --</option>
                                            @foreach ($arrayYesno as $item)
                                                <option value="{{ $item }}">
                                                    {{ $item }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="teleponkembali" class="col-sm-6 col-form-label">Telepon Kembali</label>
                                    <div class="col-sm-3">
                                        <input type="text" id="teleponkembali" name="teleponkembali"
                                            class="form-control @error('teleponkembali') is-invalid @enderror"
                                            value="{{ $data == '' ? old('teleponkembali') : old('teleponkembali', $data->teleponkembali) }}"readonly>
                                    </div>
                                    @error('teleponkembali')
                                        <span id="username" class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <div class="col-sm-3">
                                        <?php
                                        $arrayYesno = ['YES', 'NO'];
                                        ?>
                                        <select name="yesno"
                                            class="form-control select2 @error('yesno') is-invalid @enderror  "
                                            id="yesno">
                                            <option value="">-- Pilih --</option>
                                            @foreach ($arrayYesno as $item)
                                                <option value="{{ $item }}">
                                                    {{ $item }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="teleponkembali" class="col-sm-6 col-form-label">Telepon Kembali</label>
                                    <div class="col-sm-3">
                                        <input type="text" id="teleponkembali" name="teleponkembali"
                                            class="form-control @error('teleponkembali') is-invalid @enderror"
                                            value="{{ $data == '' ? old('teleponkembali') : old('teleponkembali', $data->teleponkembali) }}"
                                            readonly>
                                    </div>
                                    @error('teleponkembali')
                                        <span id="username" class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <div class="col-sm-3">
                                        <?php
                                        $arrayYesno = ['YES', 'NO'];
                                        ?>
                                        <select name="yesno"
                                            class="form-control select2 @error('yesno') is-invalid @enderror  "
                                            id="yesno">
                                            <option value="">-- Pilih --</option>
                                            @foreach ($arrayYesno as $item)
                                                <option value="{{ $item }}">
                                                    {{ $item }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="list-data card-footer">
                                <div class="row">
                                    <!-- /.col -->
                                    <div class="col-8">
                                    </div>
                                    <div class="col-4">
                                        <a class="btn btn-primary btn-block" onclick="proses()">Proses</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
@endsection
@section('addScript')
    <script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/inputmask/jquery.inputmask.min.js') }}"></script>

    <script type="text/javascript">
        $('.select2').select2();
    </script>
@endsection
