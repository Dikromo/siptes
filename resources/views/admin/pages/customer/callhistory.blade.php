@extends('admin.layouts.main')

@section('container')
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">

    <div class="container-fluid">
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
                                    <th>Sales</th>
                                    <th>Nama</th>
                                    @if (auth()->user()->cabang_id == '4')
                                        <th>Campaign</th>
                                        <th>4 Digit No Telp</th>
                                    @endif
                                    <th>Provider</th>
                                    <th>Status</th>
                                    <th>Start Call</th>
                                    <th>End Call</th>
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
    <script type="text/javascript">
        // $(document).ready(function() {
        var hari = "<?php echo date('Y-m-d'); ?>";
        var cabangs = "<?php echo auth()->user()->cabang_id; ?>";
        if (cabangs == '4') {
            $paramColumn = [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            }, {
                data: 'salesnama',
                name: 'sales.name as salesnama'
            }, {
                data: 'nama',
                name: 'customers.nama',
            }, {
                data: 'kode',
                name: 'fileexcels.kode',
            }, {
                data: 'no_telp',
                name: 'customers.no_telp',
            }, {
                data: 'provider',
                name: 'customers.provider',
            }, {
                data: 'statustext',
                name: 'statuscalls.nama as statustext'
            }, {
                data: 'call_time',
                name: 'call_time'
            }, {
                data: 'updated_at',
                name: 'updated_at'
            }];
        } else {
            $paramColumn = [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            }, {
                data: 'salesnama',
                name: 'sales.name as salesnama'
            }, {
                data: 'nama',
                name: 'customers.nama',
            }, {
                data: 'provider',
                name: 'customers.provider',
            }, {
                data: 'statustext',
                name: 'statuscalls.nama as statustext'
            }, {
                data: 'call_time',
                name: 'call_time'
            }, {
                data: 'updated_at',
                name: 'updated_at'
            }];
        }
        $('#dataTables').DataTable({
            autoWidth: false,
            bDestroy: true,
            initComplete: function(settings, json) {
                fromTabel = this.api().data().length;
            },
            ajax: {
                type: 'POST',
                url: '/customer/ajax/callhistory',
                data: {
                    _token: '{{ csrf_token() }}',
                }
            },
            lengthMenu: [
                [10, 50, 100, 200, 500, -1],
                [10, 50, 100, 200, 500, "All"]
            ],
            columns: $paramColumn,
            columnDefs: [{
                targets: 0,
                className: "text-center",
            }],
            dom: 'lBfrtip',
            buttons: [{
                extend: 'excel',
                text: 'Export Excel',
                filename: 'export_callhistory_' + hari
            }, ],
            processing: true,
            serverSide: true
        }).buttons().container().appendTo('#dataTables_wrapper .col-md-6:eq(0)');
        // })
    </script>
@endsection
