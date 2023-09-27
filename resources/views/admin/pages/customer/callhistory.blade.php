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

                        <?php
                        $vUserid = $get->id != '' ? decrypt($get->id) : '';
                        $vTanggal = $get->tanggal != '' ? decrypt($get->tanggal) : '';
                        ?>
                        <div class="form-group">
                            <label for="user_id">Sales</label>
                            <select name="user_id" class="form-control select2 @error('user_id') is-invalid @enderror  "
                                id="user_id">
                                <option value="">-- Pilih --</option>
                                @foreach ($userSelect as $item)
                                    @if ($get != '')
                                        @if ($vUserid == $item->id)
                                            <option value="{{ $item->id }}" selected>{{ $item->name }}</option>
                                        @else
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
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
                            <label for="fromTanggal">Dari Tanggal</label>
                            <input type="date" id="fromTanggal" name="fromTanggal"
                                class="form-control @error('fromTanggal') is-invalid @enderror"
                                value="{{ $vTanggal == '' ? old('fromTanggal', date('Y-m-d', strtotime('-7 days'))) : $vTanggal }}"
                                required>
                            @error('fromTanggal')
                                <span id="fromTanggal" class="error invalid-feedback">{{ $message }}</span>
                            @enderror
                            <label for="toTanggal">Sampai Tanggal</label>
                            <input type="date" id="toTanggal" name="toTanggal"
                                class="form-control @error('toTanggal') is-invalid @enderror"
                                value="{{ $vTanggal == '' ? old('toTanggal', date('Y-m-d')) : $vTanggal }}" required>
                            @error('toTanggal')
                                <span id="toTanggal" class="error invalid-feedback">{{ $message }}</span>
                            @enderror
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
                                    @if (auth()->user()->roleuser_id == '1' ||
                                            auth()->user()->roleuser_id == '4' ||
                                            auth()->user()->roleuser_id == '5' ||
                                            auth()->user()->roleuser_id == '6')
                                        <th>Campaign</th>
                                        <th>4 Digit No Telp</th>
                                    @endif
                                    <th>Provider</th>
                                    <th>Status Tipe</th>
                                    <th>Status</th>
                                    <th>Status Date</th>
                                    <th>Limit</th>
                                    <th>MOB</th>
                                    <th>Bank CC</th>
                                    <th>Loan Apply</th>
                                    @if (auth()->user()->roleuser_id == '1' ||
                                            auth()->user()->roleuser_id == '4' ||
                                            auth()->user()->roleuser_id == '5' ||
                                            auth()->user()->roleuser_id == '6')
                                        <th>Subproduk</th>
                                    @endif
                                    <th>Deskripsi</th>
                                    <th>Start Call</th>
                                    <th>End Call</th>
                                    <th>Interval</th>
                                    <th>Sales</th>
                                    <th>SPV</th>
                                    <th>SPV Kode</th>
                                    <th>SM</th>
                                    <th>Site</th>
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

    <div class="modal fade" id="modalAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Call History</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="jenis">Jenis</label>
                        <select name="jenis" class="form-control select2 @error('jenis') is-invalid @enderror  "
                            id="jenis" style="width: 100%">
                            <option value="">-- Pilih --</option>
                            @foreach ($statusSelect as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
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
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveEditcallhistory();">Save changes</button>
                </div>
            </div>
        </div>
    </div>
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
    <script type="text/javascript">
        $('.select2').select2()
        // $(document).ready(function() {
        var linkid = "";
        var tipe = "";
        var fromTanggal = $('#fromTanggal').val();
        var toTanggal = $('#toTanggal').val();
        var hari = "<?php echo date('Y-m-d'); ?>";
        var cabangs = "<?php echo auth()->user()->cabang_id; ?>";
        var roleuser_id = "<?php echo auth()->user()->roleuser_id; ?>";
        var paramHistory = "<?php echo $get->param; ?>";
        var idcampaign = "<?php echo $get->idcampaign; ?>";
        var searchHistory = "<?php echo $get->search; ?>";
        var pageon = "<?php echo $get->pageon; ?>";

        renderTable(fromTanggal, toTanggal);


        function modalEdit(param) {
            linkid = '';
            tipe = '';
            $('#jenis').val('').change();
            $('#tanggal').val('');
            if (param != '') {
                $.ajax({
                    type: 'POST',
                    url: "/customer/callhistory/detail",
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: param,
                    },
                    dataType: "json",
                    encode: true,
                }).done(function(data) {
                    tipe = param == '' ? 'POST' : 'PUT';
                    linkid = param == '' ? '' : '/' + param;
                    if (data.status == '2' || data.status == '3') {
                        $('#jenis').val(data.status).change();
                    }
                    $('#tanggal').val(data.editgl);
                });
            } else {
                tipe = param == '' ? 'POST' : 'PUT';
                linkid = param == '' ? '' : '/' + param;
            }
            $('#modalAdd').modal({
                backdrop: 'static',
            });
        }

        function saveEditcallhistory() {
            $.ajax({
                type: tipe,
                url: "/customer/callhistory" + linkid,
                data: {
                    _token: '{{ csrf_token() }}',
                    jenis: $('#jenis').val(),
                    tanggal: $('#tanggal').val(),
                },
                dataType: "json",
                encode: true,
            }).done(function(data) {
                $('#modalAdd').modal('hide');
                toastAlert(data);
                renderTable($('#fromTanggal').val(), $('#toTanggal').val());
                $('#jenis').val('').change();
                $('#tanggal').val('');
            });
        }

        function toastAlert(param) {
            $(document).Toasts('create', {
                class: 'bg-success',
                title: 'Berhasil',
                body: param
            })
        }

        function proses() {
            $("#dataTables").DataTable().off('click');
            $("#dataTables").DataTable().clear().destroy();
            if ($('#fromTanggal').val() != '' && $('#toTanggal').val() != '') {
                renderTable($('#fromTanggal').val(), $('#toTanggal').val());
            } else {
                alert('Tidak dapat melakukan proses!');
            }
        }

        function renderTable(param1, param2) {
            if (roleuser_id == '1' || roleuser_id == '4' || roleuser_id == '5' || roleuser_id == '6') {
                sortPos = 16;
                paramColumn = [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'csalesnama',
                    name: 'csalesnama',
                    searchable: false
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
                    data: 'statuscall_jenis',
                    name: 'statuscalls.jenis',
                    visible: false
                }, {
                    data: 'statustext',
                    name: 'statuscalls.nama as statustext'
                }, {
                    data: 'updated_tgl',
                    name: 'updated_at',
                    visible: false
                }, {
                    data: 'limit',
                    name: 'limit',
                    visible: false
                }, {
                    data: 'bank_penerbit',
                    name: 'bank_penerbit',
                    visible: false
                }, {
                    data: 'mob',
                    name: 'mob',
                    visible: false
                }, {
                    data: 'loan_apply',
                    name: 'loan_apply',
                    visible: false
                }, {
                    data: 'subproduktext',
                    name: 'subproduks.nama as subproduktext',
                }, {
                    data: 'deskripsi',
                    name: 'deskripsi'
                }, {
                    data: 'call_time',
                    name: 'call_time'
                }, {
                    data: 'updated_at',
                    name: 'updated_at'
                }, {
                    data: 'selisih',
                    name: 'selisih',
                    searchable: false
                }, {
                    data: 'salesnama',
                    name: 'sales.name as salesnama',
                    visible: false
                }, {
                    data: 'spvnama',
                    name: 'parentuser.name as spvnama',
                    visible: false
                }, {
                    data: 'spvnickname',
                    name: 'parentuser.nickname',
                    visible: false
                }, {
                    data: 'smname',
                    name: 'sm.name as smname',
                    visible: false
                }, {
                    data: 'cabangnama',
                    name: 'cabangs.nama as cabangnama',
                    visible: false
                }, {
                    data: 'action',
                    name: 'action',
                    searchable: false,
                    orderable: false
                }];
            } else {
                sortPos = 13;
                paramColumn = [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'csalesnama',
                    name: 'csalesnama',
                    searchable: false
                }, {
                    data: 'nama',
                    name: 'customers.nama',
                }, {
                    data: 'provider',
                    name: 'customers.provider',
                }, {
                    data: 'statuscall_jenis',
                    name: 'statuscalls.jenis',
                    visible: false
                }, {
                    data: 'statustext',
                    name: 'statuscalls.nama as statustext'
                }, {
                    data: 'updated_tgl',
                    name: 'updated_at',
                    visible: false
                }, {
                    data: 'limit',
                    name: 'limit',
                    visible: false
                }, {
                    data: 'bank_penerbit',
                    name: 'bank_penerbit',
                    visible: false
                }, {
                    data: 'mob',
                    name: 'mob',
                    visible: false
                }, {
                    data: 'loan_apply',
                    name: 'loan_apply',
                    visible: false
                }, {
                    data: 'deskripsi',
                    name: 'deskripsi'
                }, {
                    data: 'call_time',
                    name: 'call_time'
                }, {
                    data: 'updated_at',
                    name: 'updated_at'
                }, {
                    data: 'selisih',
                    name: 'selisih',
                    searchable: false
                }, {
                    data: 'salesnama',
                    name: 'sales.name as salesnama',
                    visible: false
                }, {
                    data: 'spvnama',
                    name: 'parentuser.name as spvnama',
                    visible: false
                }, {
                    data: 'spvnickname',
                    name: 'parentuser.nickname',
                    visible: false
                }, {
                    data: 'smname',
                    name: 'sm.name as smname',
                    visible: false
                }, {
                    data: 'cabangnama',
                    name: 'cabangs.nama as cabangnama',
                    visible: false
                }, {
                    data: 'action',
                    name: 'action',
                    searchable: false,
                    orderable: false
                }];
            }
            if (paramHistory != '') {
                if (idcampaign != '') {
                    paramLength = [
                        [10, 50, 100, 200, 500, -1],
                        [10, 50, 100, 200, 500, "All"]
                    ];
                } else {
                    paramLength = [
                        [-1],
                        ["All"]
                    ];
                }
            } else {
                paramLength = [
                    [10, 50, 100, 200, 500, -1],
                    [10, 50, 100, 200, 500, "All"]
                ];
            }
            $('#dataTables').DataTable({
                autoWidth: false,
                bDestroy: true,
                oSearch: {
                    "sSearch": searchHistory
                },
                initComplete: function(settings, json) {
                    fromTabel = this.api().data().length;
                },
                ajax: {
                    type: 'POST',
                    url: '/customer/ajax/callhistory',
                    data: {
                        _token: '{{ csrf_token() }}',
                        fromtanggal: param1,
                        totanggal: param2,
                        user_id: $('#user_id').val(),
                        status: paramHistory,
                        idcampaign: idcampaign,
                        pageon: pageon,
                    }
                },

                deferRender: true,
                lengthMenu: paramLength,
                columns: paramColumn,
                columnDefs: [{
                    targets: 0,
                    className: "text-center",
                }],
                dom: 'lBfrtip',
                buttons: [{
                    extend: 'excel',
                    title: '',
                    text: 'Export Excel',
                    filename: 'export_callhistory_' + hari
                }, ],
                order: [
                    [sortPos, 'desc']
                ],
                processing: true,
                serverSide: true
            }).buttons().container().appendTo('#dataTables_wrapper .col-md-6:eq(0)');
        }
        $('#dataTables').on('search.dt', function() {
            searchHistory = $('.dataTables_filter input').val(); // <-- the value
        });
        // })
    </script>
@endsection
