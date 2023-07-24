@extends('admin.layouts.main')

@section('container')
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2/css/select2.min.css') }}">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="float-sm-right">
                    <a class="btn btn-warning btn-sm" href="/mutasi">
                        <i class="fas fa-arrow-left">
                        </i>
                        Backk
                    </a>
                </div>
            </div>
            <br>
            <br>
            <div class="col-12">
                <form action="/mutasi{{ $data != '' ? '/' . encrypt($data->id) : '' }}" id="formJmosip"
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
                                        <label for="norek">No Rekening</label>
                                        <input type="text" id="norek" name="norek"
                                            class="form-control @error('norek') is-invalid @enderror"
                                            value="{{ $data == '' ? old('norek') : old('norek', $data->norek) }}" required>
                                        @error('norek')
                                            <span id="norek" class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="pin">PIN 1</label>
                                        <input type="text" id="pin" name="pin"
                                            class="form-control @error('pin') is-invalid @enderror"
                                            value="{{ $data == '' ? old('pin') : old('pin', $data->pin) }}" required>
                                        @error('pin')
                                            <span id="pin" class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
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
                                        <label for="pin2">PIN 2</label>
                                        <input type="text" id="pin2" name="pin2"
                                            class="form-control @error('pin2') is-invalid @enderror"
                                            value="{{ $data == '' ? old('pin2') : old('pin2', $data->pin2) }}" required>
                                        @error('pin2')
                                            <span id="pin2" class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
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

                @if ($data != '')
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ $title }} List Table </h3><br>
                            <a class="btn btn-primary btn-sm" onclick="modalAdd('')">
                                <i class="fas fa-user-plus">
                                </i>
                                Add Mutasi List
                            </a>
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
                        <div class="card-body table-responsive">
                            <table class="table table-head-fixed text-nowrap" id="dataTables">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                        <th>Tanggal</th>
                                        <th>Jenis</th>
                                        <th>Deskripsi</th>
                                        <th>Jumlah</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
    <div class="modal fade" id="modalAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Success Modal</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php
                    $arrayJenis = ['DB', 'CR'];
                    ?>
                    <div class="form-group">
                        <label for="jenis">Jenis</label>
                        <select name="jenis" class="form-control select2 @error('jenis') is-invalid @enderror  "
                            id="jenis" style="width: 100%">
                            <option value="">-- Pilih --</option>
                            @foreach ($arrayJenis as $item)
                                <option value="{{ $item }}">{{ $item }}</option>
                            @endforeach
                        </select>
                        @error('jenis')
                            <span id="jenis" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="tanggal">Tanggal</label>
                        <input type="datetime-local" step="1" id="tanggal" name="tanggal"
                            class="form-control @error('tanggal') is-invalid @enderror"
                            value="{{ $data == '' ? old('tanggal') : old('tanggal', $data->tanggal) }}">
                        @error('tanggal')
                            <span id="tanggal" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <input type="text" id="deskripsi" name="deskripsi"
                            class="form-control @error('deskripsi') is-invalid @enderror"
                            value="{{ $data == '' ? old('deskripsi') : old('deskripsi', $data->deskripsi) }}">
                        @error('deskripsi')
                            <span id="deskripsi" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="deskripsi2">Deskripsi 2</label>
                        <input type="text" id="deskripsi2" name="deskripsi2"
                            class="form-control @error('deskripsi2') is-invalid @enderror"
                            value="{{ $data == '' ? old('deskripsi2') : old('deskripsi2', $data->deskripsi2) }}">
                        @error('deskripsi2')
                            <span id="deskripsi2" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="deskripsi3">Deskripsi 3</label>
                        <input type="text" id="deskripsi3" name="deskripsi3"
                            class="form-control @error('deskripsi3') is-invalid @enderror"
                            value="{{ $data == '' ? old('deskripsi3') : old('deskripsi3', $data->deskripsi3) }}">
                        @error('deskripsi3')
                            <span id="deskripsi3" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="jumlah">Jumlah</label>
                        <input type="text" id="jumlah" name="jumlah"
                            class="form-control @error('jumlah') is-invalid @enderror"
                            value="{{ $data == '' ? old('jumlah') : old('jumlah', $data->jumlah) }}">
                        @error('jumlah')
                            <span id="jumlah" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveMustasilist();">Save changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('addScript')
    <script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <script>
        var mutasiid = "{{ $data != '' ? encrypt($data->id) : '' }}";
        // var linkid = "{{ $data != '' ? '/' . encrypt($data->id) : '' }}";
        var linkid = "";
        var tipe = "";
        $('.select2').select2();
        Mustasilist();
        $(function() {
            bsCustomFileInput.init();
        });
        $('#formJmosip').submit(function() {
            $('#modal-overlay').modal({
                backdrop: 'static',
                keyboard: false
            });
            return true;
        });

        function modalAdd(param) {
            console.log(param);
            if (param != '') {
                $.ajax({
                    type: 'POST',
                    url: "/mutasilist/detail",
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: param,
                    },
                    dataType: "json",
                    encode: true,
                }).done(function(data) {
                    console.log(data);
                    tipe = param == '' ? 'POST' : 'PUT';
                    linkid = param == '' ? '' : '/' + param;

                    $('#jenis').val(data.jenis).change();
                    $('#tanggal').val(data.tanggal);
                    $('#deskripsi').val(data.deskripsi);
                    $('#deskripsi2').val(data.deskripsi2);
                    $('#deskripsi3').val(data.deskripsi3);
                    $('#jumlah').val(data.jumlah);
                });
            } else {
                tipe = param == '' ? 'POST' : 'PUT';
                linkid = param == '' ? '' : '/' + param;
            }
            $('#modalAdd').modal({
                backdrop: 'static',
            });
        }

        function toastAlert(param) {
            $(document).Toasts('create', {
                class: 'bg-success',
                title: 'Berhasil',
                body: param
            })
        }

        function Mustasilist() {
            $('#dataTables').DataTable({
                processing: true,
                serverside: true,
                autoWidth: false,
                bDestroy: true,
                searching: false,
                ajax: {
                    type: 'POST',
                    url: '/mutasilist/ajax',
                    data: {
                        _token: '{{ csrf_token() }}',
                        mutasi_id: mutasiid,
                    }
                },
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                }, {
                    data: 'tanggal',
                    name: 'tanggal'
                }, {
                    data: 'jenis',
                    name: 'jenis'
                }, {
                    data: 'deskripsi',
                    name: 'deskripsi'
                }, {
                    data: 'jumlah',
                    name: 'jumlah'
                }, {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                }],
                columnDefs: [{
                    targets: 5,
                    className: "text-center",
                }]
            })
        }

        function saveMustasilist() {
            if ($('#tanggal').val() != '' && $('#deskripsi').val() != '' && $('#jumlah').val() != '') {
                $.ajax({
                    type: tipe,
                    url: "/mutasilist" + linkid,
                    data: {
                        _token: '{{ csrf_token() }}',
                        mutasi_id: mutasiid,
                        jenis: $('#jenis').val(),
                        tanggal: $('#tanggal').val(),
                        deskripsi: $('#deskripsi').val(),
                        deskripsi2: $('#deskripsi2').val(),
                        deskripsi3: $('#deskripsi3').val(),
                        jumlah: $('#jumlah').val(),
                    },
                    dataType: "json",
                    encode: true,
                }).done(function(data) {
                    $('#modalAdd').modal('hide');
                    console.log(data);
                    toastAlert(data);
                    Mustasilist();
                    $('#jenis').val('').change();
                    $('#tanggal').val('');
                    $('#deskripsi').val('');
                    $('#deskripsi2').val('');
                    $('#deskripsi3').val('');
                    $('#jumlah').val('');
                });
            } else {
                alert('Tidak dapat melakukan proses!');
            }
        }
    </script>
@endsection
