@extends('admin.layouts.main')

@section('container')
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <div class="container-fluid pt-3">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mr-2 text-right">
                                <div class="float-sm-right">
                                    <form action="/logout" method="post">
                                        @csrf
                                        <button type="submit" class="btn btn-warning">
                                            <i class="nav-icon fas fa-sign-out"></i>
                                            Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-12  text-center my-3">
                            <div class="text-center">
                                <img class="img-fluid" src="{{ asset('assets/img/logo.png') }}" alt="Photo">
                            </div>

                            <h3 class="profile-username text-center">{{ auth()->user()->name }}</h3>

                            <p class="text-muted text-center">Software Engineer</p>
                            <a class="btn"><i class="nav-icon fas fa-database"></i>
                                Total Data : {{ count($data_total) }}</a>
                            @foreach ($data as $item)
                                <a href="/call/detail/{{ encrypt($item->id) }}"
                                    class="btn btn-primary btn-lg border_white"><i class="nav-icon fas fa-headset"></i>
                                    Call</a>
                            @endforeach
                        </div>
                        <hr>

                        <h3 class="profile-username text-left pl-2"><i class="nav-icon fas fa-headset"></i> List Call Back
                        </h3>
                        <div class="col-12 table-responsive">
                            <table class="table table-head-fixed text-nowrap" id="dataTables1">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                        <th>Nama</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Budi Hartanto</td>
                                        <td>Call Lagi</td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <hr>

                        <h3 class="profile-username text-left pl-2"><i class="nav-icon fas fa-headset"></i> List Apply
                        </h3>
                        <div class="col-12 table-responsive">
                            <table class="table table-head-fixed text-nowrap" id="dataTables2">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                        <th>Nama</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Budi Hartanto</td>
                                        <td>Call Lagi</td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        {{-- <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a> --}}
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->

    <div class="modal fade" id="modal-overlay">
        <div class="overlay modal_loading">
            <button class="btn btn-primary" style="border:1px; color:#fff;" type="button" disabled>
                <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                Loading...
            </button>
        </div>
        <!-- /.modal-dialog -->
    </div>
@endsection
@section('addScript')
    <script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $('#dataTables1').DataTable({
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
                url: '/call/ajax',
                data: {
                    _token: '{{ csrf_token() }}',
                    user_id: '{{ auth()->user()->id }}',
                    status: '2',
                }
            },
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
            }, {
                data: 'customer.nama',
                name: 'customer.nama'
            }, {
                data: 'statusText',
                name: 'statusText'
            }, {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
            }],
            columnDefs: [{
                targets: 3,
                className: "text-center",
            }]
        });

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
                url: '/call/ajax',
                data: {
                    _token: '{{ csrf_token() }}',
                    user_id: '{{ auth()->user()->id }}',
                    status: '1',
                }
            },
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
            }, {
                data: 'customer.nama',
                name: 'customer.nama'
            }, {
                data: 'statusText',
                name: 'statusText'
            }, {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
            }],
            columnDefs: [{
                targets: 3,
                className: "text-center",
            }]
        });
        $('#formImport').submit(function() {
            $('#modal-overlay').modal({
                backdrop: 'static',
                keyboard: false
            });
            return true;
        });
    </script>
@endsection
