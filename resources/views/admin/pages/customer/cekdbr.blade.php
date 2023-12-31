@extends('admin.layouts.main')

@section('container')
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="tanggal">Tanggal</label>
                        <input type="date" id="tanggal" name="tanggal"
                            class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal') }}"
                            required>
                        @error('tanggal')
                            <span id="tanggal" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <a class="btn btn-primary btn-block" onclick="proses()">Proses</a>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ $title }} Table </h3>
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
                                    <th>Incoming Date</th>
                                    <th>Proses Tipe</th>
                                    <th>NIK</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>DOB</th>
                                    <th>Perusahaan</th>
                                    <th>Jabatan</th>
                                    <th>Masa Kerja</th>
                                    <th>JMO Asli</th>
                                    <th>Loan Apply</th>
                                    <th>Bank Kartu Kredit</th>
                                    <th>Limit Kartu Kredit</th>
                                    <th>MOB Kartu Kredit</th>
                                    <th>Sales</th>
                                    <th>Team Leader</th>
                                    {{-- <th>Reason</th> --}}
                                    <th></th>
                                </tr>
                            </thead>
                            {{-- <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->email }}</td>
                                        <td><span class="tag tag-success">Approved</span></td>
                                        <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
                                        <td>
                                            <a class="btn btn-success btn-sm" href="/user/{{ $item->username }}">
                                                <i class="fas fa-eye">
                                                </i>
                                                View
                                            </a>
                                            <a class="btn btn-info btn-sm" href="/user/{{ $item->username }}/edit">
                                                <i class="fas fa-pencil-alt">
                                                </i>
                                                Edit
                                            </a>
                                            <a class="btn btn-danger btn-sm" href="#">
                                                <i class="fas fa-trash">
                                                </i>
                                                Delete
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody> --}}
                        </table>
                    </div>
                    <!-- /.card-body -->
                    {{-- <div class="list-data card-footer">
                    </div> --}}
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
    <script>
        // $(document).ready(function() {
        function proses() {
            var hari = "<?php echo date('Y-m-d'); ?>";
            if ($('#tanggal').val() != '') {
                $('#dataTables').DataTable({
                    processing: true,
                    serverside: true,
                    autoWidth: false,
                    bDestroy: true,
                    initComplete: function(settings, json) {
                        fromTabel = this.api().data().length;
                    },
                    ajax: {
                        type: 'POST',
                        url: '/customer/ajax/cekdbr',
                        data: {
                            _token: '{{ csrf_token() }}',
                            tanggal: $('#tanggal').val(),
                        }
                    },
                    columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                    }, {
                        data: 'updated_at',
                        name: 'updated_at'
                    }, {
                        data: 'tipeproses',
                        name: 'tipeproses'
                    }, {
                        data: 'nik',
                        name: 'nik'
                    }, {
                        data: 'namacust',
                        name: 'namacust'
                    }, {
                        data: 'email',
                        name: 'email',
                        visible: false,
                        searchable: false
                    }, {
                        data: 'dob',
                        name: 'dob',
                        visible: false,
                        searchable: false
                    }, {
                        data: 'perusahaan',
                        name: 'perusahaan'
                    }, {
                        data: 'jabatan',
                        name: 'jabatan'
                    }, {
                        data: 'masakerja',
                        name: 'masakerja'
                    }, {
                        data: 'jmoasli',
                        name: 'jmoasli',
                        visible: false,
                        searchable: false
                    }, {
                        data: 'loan_apply',
                        name: 'loan_apply',
                        visible: false,
                        searchable: false
                    }, {
                        data: 'bank_penerbit',
                        name: 'bank_penerbit',
                        visible: false,
                        searchable: false
                    }, {
                        data: 'limit',
                        name: 'limit',
                        visible: false,
                        searchable: false
                    }, {
                        data: 'mob',
                        name: 'mob',
                        visible: false,
                        searchable: false
                    }, {
                        data: 'user.name',
                        name: 'user.name'
                    }, {
                        data: 'parentuser_nama',
                        name: 'parentuser_nama'
                    }],
                    columnDefs: [{
                        targets: 0,
                        className: "text-center",
                    }],
                    dom: 'Bfrtip',
                    buttons: [{
                        extend: 'excel',
                        title: '',
                        text: 'Export Excel',
                        filename: 'export_cekdbr_' + hari
                    }, ]
                }).buttons().container().appendTo('#dataTables_wrapper .col-md-6:eq(0)');
            } else {
                alert('Tidak dapat melakukan proses!');
            }
        }
        // })
    </script>
@endsection
