@extends('admin.layouts.main')

@section('container')
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div>
                    <a class="btn btn-primary btn-sm" href="/user/create">
                        <i class="fas fa-user-plus">
                        </i>
                        Add User
                    </a>
                </div>
                <br>
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
                                    <th>Nama</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Supervisor</th>
                                    <th>SPV Kode</th>
                                    <th>Sales Manager</th>
                                    <th>Unit Manager</th>
                                    <th>Sales Code</th>
                                    <th>Refferal</th>
                                    <th>Join Date</th>
                                    <th>Status</th>
                                    <th>Resign Date</th>
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

        var hari = "<?php echo date('Y-m-d'); ?>";
        $('#dataTables').DataTable({
            processing: true,
            serverside: true,
            autoWidth: false,
            ajax: {
                type: 'POST',
                url: '/user/ajax',
                data: {
                    _token: '{{ csrf_token() }}',
                }
            },
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            }, {
                data: 'name',
                name: 'name'
            }, {
                data: 'username',
                name: 'username'
            }, {
                data: 'roletext',
                name: 'roleusers.nama'
            }, {
                data: 'spvnama',
                name: 'spv.name'
            }, {
                data: 'spvnickname',
                name: 'spv.nickname'
            }, {
                data: 'smnama',
                name: 'sm.name',
                visible: false
            }, {
                data: 'umnama',
                name: 'um.name',
                visible: false
            }, {
                data: 'salescode',
                name: 'salescode',
                visible: false
            }, {
                data: 'refferal',
                name: 'refferal',
                visible: false
            }, {
                data: 'joindate',
                name: 'join_date',
                visible: false
            }, {
                data: 'statusText',
                name: 'statusText'
            }, {
                data: 'resigndate',
                name: 'resign_date',
                visible: false
            }, {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
            }],
            columnDefs: [{
                targets: 5,
                className: "text-center",
            }],
            deferRender: true,
            lengthMenu: [
                [10, 50, 100, 200, 500, -1],
                [10, 50, 100, 200, 500, "All"]
            ],
            dom: 'lBfrtip',
            buttons: [{
                extend: 'excel',
                text: 'Export Excel',
                filename: 'export_users_' + hari,
                exportOptions: {
                    columns: 'th:not(:last-child)'
                }
            }, ]
        }).buttons().container().appendTo('#dataTables_wrapper .col-md-6:eq(0)');
        // })
    </script>
@endsection
