@extends('admin.layouts.main')

@section('container')
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div>
                    <a class="btn btn-primary btn-sm" href="/campaign/group/create">
                        <i class="fas fa-user-plus">
                        </i>
                        Add Group
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
                                    <th>Nama Group</th>
                                    <th>Total Data (Terdistribusi | Belum Terdistribusi)(Reload)</th>
                                    <th>(Telepon | Belum Ditelepon)</th>
                                    <th>(Contact | Not Contact)</th>
                                    <th>(Prospek | Closing)</th>
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
    <script>
        // $(document).ready(function() {
        $('#dataTables').DataTable({
            processing: true,
            serverside: true,
            autoWidth: false,
            ajax: {
                type: 'POST',
                url: '/campaign/ajaxgroup',
                data: {
                    _token: '{{ csrf_token() }}',
                }
            },
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
            }, {
                data: 'groupnama',
                name: 'group_fileexcels.nama AS groupnama'
            }, {
                data: null,
                name: null,
                render: {
                    _: "total_data1",
                    filter: "total_data1",
                    display: "all1"
                }
            }, {
                data: null,
                name: null,
                render: {
                    _: "total_call",
                    filter: "total_call",
                    display: "all2"
                }
            }, {
                data: null,
                name: null,
                render: {
                    _: "total_callout",
                    filter: "total_callout",
                    display: "all3"
                }
            }, {
                data: null,
                name: null,
                render: {
                    _: "total_closing",
                    filter: "total_closing",
                    display: "all4"
                }
            }, {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
            }],
            columnDefs: [{
                targets: '_all',
                className: "text-center",
            }]
        })
        // })
    </script>
@endsection
