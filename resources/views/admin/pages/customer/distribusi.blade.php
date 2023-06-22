@extends('admin.layouts.main')

@section('container')
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">

    <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2/css/select2.min.css') }}">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @error('msg')
                    <div class="alert alert-info alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h5><i class="icon fas fa-info"></i> Alert!</h5>
                        {!! $message !!}
                    </div>
                @enderror()
            </div>
        </div>
        <div class="row">
            {{-- <div class="col-12">
                <div class="float-sm-right">
                    <a class="btn btn-warning btn-sm" href="/user">
                        <i class="fas fa-arrow-left">
                        </i>
                        Back
                    </a>
                </div>
            </div>
            <br>
            <br> --}}
            {{-- Table From --}}
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ $title }} Dari</h3>
                        <div class="card-tools">
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body ">
                        <div class="card-body table-responsive">
                            <table class="table table-head-fixed text-nowrap" id="dataTables1">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                        <th>Nama</th>
                                        <th>Telp</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                    </div>
                </div>
            </div>
            {{-- Form Filter --}}
            <div class="col-lg-2">
                <div class="card card-primary card-outline">
                    <form action="/customer/distribusi/proses" id="formDistribusi" method="POST">
                        @csrf
                        <div class="card-header">
                            <h3 class="card-title">Aksi {{ $title }}</h3>
                            <div class="card-tools">
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body ">
                            <div class="form-group">
                                <label for="kode">Kode</label>
                                <select name="fileexcel_id"
                                    class="form-control select2 @error('fileexcel_id') is-invalid @enderror  "
                                    id="fileexcel_id">
                                    <option value="">-- Pilih --</option>
                                    @foreach ($fileExceldata as $item)
                                        @if ($data != '')
                                            @if ($data->fileexcel_id == $item->id)
                                                <option value="{{ $item->id }}" selected>{{ $item->kode }}</option>
                                            @endif
                                        @else
                                            <option value="{{ $item->id }}">{{ $item->kode }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('fileexcel_id')
                                    <span id="fileexcel_id" class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="user_id">Sales</label>
                                <select name="user_id" class="form-control select2 @error('user_id') is-invalid @enderror  "
                                    id="user_id">
                                    <option value="">-- Pilih --</option>
                                    @foreach ($userData as $item)
                                        @if ($data != '')
                                            @if ($data->user_id == $item->id)
                                                <option value="{{ $item->id }}" selected>{{ $item->name }}</option>
                                            @endif
                                        @else
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <span id="user_id" class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="total">Total</label>
                                <input type="number" id="total" name="total"
                                    class="form-control @error('total') is-invalid @enderror" value="{{ old('total') }}"
                                    required>
                                @error('total')
                                    <span id="total" class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <div class="row">
                                <!-- /.col -->
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-block">Simpan</button>
                                </div>
                                <!-- /.col -->
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            {{-- Table To --}}
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ $title }} Ke</h3>
                        <div class="card-tools">
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body ">
                        <div class="card-body table-responsive">
                            <table class="table table-head-fixed text-nowrap" id="dataTables2">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                        <th>Nama</th>
                                        <th>Telp</th>
                                        <th>Perusahaan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                    </div>
                </div>
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
        var fromTabel = 0;
        $('.select2').select2()
        $('#formDistribusi').submit(function() {
            if (fromTabel != 0 && $('#total').val() != '0' && $('#total').val() <= fromTabel) {
                $('#modal-overlay').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                return true;
            }
            alert('Tidak dapat melakukan proses!');
            return false;
        });
        $(document).ready(function() {
            $("#fileexcel_id").change(function() {
                dataTablesfrom(this.value);
            });
            $("#user_id").change(function() {
                dataTablesto(this.value);
            });
            $('#dataTables1').DataTable({
                processing: true,
                serverside: true,
                autoWidth: false,
                bDestroy: true,
                searching: false,
            });
            $('#dataTables2').DataTable({
                processing: true,
                serverside: true,
                autoWidth: false,
                bDestroy: true,
                searching: false,
            });
        })

        function dataTablesfrom(select) {
            $('#dataTables1').DataTable({
                processing: true,
                serverside: true,
                autoWidth: false,
                bDestroy: true,
                searching: false,
                initComplete: function(settings, json) {
                    fromTabel = this.api().data().length;
                },
                ajax: {
                    type: 'POST',
                    url: '/customer/ajax/from',
                    data: {
                        _token: '{{ csrf_token() }}',
                        fileexcel_id: select,
                    }
                },
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                }, {
                    data: 'nama',
                    name: 'nama'
                }, {
                    data: 'no_telp',
                    name: 'no_telp'
                }, {
                    data: 'perusahaan',
                    name: 'perusahaan'
                }]
            });
        }

        function dataTablesto(select) {
            $('#dataTables2').DataTable({
                processing: true,
                serverside: true,
                autoWidth: false,
                bDestroy: true,
                searching: false,
                initComplete: function(settings, json) {
                    //fromTabel = this.api().data().length;
                },
                ajax: {
                    type: 'POST',
                    url: '/customer/ajax/to',
                    data: {
                        _token: '{{ csrf_token() }}',
                        user_id: select,
                    }
                },
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                }, {
                    data: 'customer.nama',
                    name: 'customer.nama'
                }, {
                    data: 'customer.no_telp',
                    name: 'customer.no_telp'
                }, {
                    data: 'customer.perusahaan',
                    name: 'customer.perusahaan'
                }]
            });
        }
    </script>
@endsection
