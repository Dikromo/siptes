@extends('admin.layouts.main')

@section('container')
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-rowgroup/css/rowGroup.bootstrap4.min.css') }}"> --}}

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline">
                    <div class="card-body">
                        <?php
                        $arrayTipe = ['Total Data', 'Sisah Data Sebelumnya', 'Distribusi Hari Ini', 'Sudah di Telepon', 'Sisah Data', 'Telepon Di Angkat', 'Prospek', 'Closing', 'Closing 3 hari'];
                        ?>
                        {{-- <div class="form-group">
                            <label for="tipe">Sort By</label>
                            <select name="tipe" class="form-control select2 @error('tipe') is-invalid @enderror  "
                                id="tipe">
                                <option value="">-- Pilih --</option>
                                @foreach ($arrayTipe as $item)
                                    @if (session('oldData') != '')
                                        @if (session('oldData')['tipe'] == $item)
                                            <option value="{{ $item }}" selected>{{ $item }}</option>
                                        @else
                                            <option value="{{ $item }}">
                                                {{ $item }}
                                            </option>
                                        @endif
                                    @else
                                        <option value="{{ $item }}">
                                            {{ $item }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('tipe')
                                <span id="tipe" class="error invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div> --}}

                        <div class="row">
                            <div class="col-md-6">
                                <?php
                                $arrayTipe = ['Contact Rate'];
                                ?>
                                <div class="form-group">
                                    <label for="tipe">Sort By</label>
                                    <select name="tipe"
                                        class="form-control select2 @error('tipe') is-invalid @enderror  " id="tipe">
                                        <option value="">-- Pilih --</option>
                                        @foreach ($arrayTipe as $item)
                                            @if (session('oldData') != '')
                                                @if (session('oldData')['tipe'] == $item)
                                                    <option value="{{ $item }}" selected>{{ $item }}
                                                    </option>
                                                @else
                                                    <option value="{{ $item }}">
                                                        {{ $item }}
                                                    </option>
                                                @endif
                                            @else
                                                <option value="{{ $item }}">
                                                    {{ $item }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('tipe')
                                        <span id="tipe" class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?php
                                $arrayTipe = ['All Site', 'Your Site', 'Today'];
                                ?>
                                <div class="form-group">
                                    <label for="jenis">jenis</label>
                                    <select name="jenis"
                                        class="form-control select2 @error('jenis') is-invalid @enderror  " id="jenis">
                                        <option value="">-- Pilih --</option>
                                        @foreach ($arrayTipe as $item)
                                            @if (session('oldData') != '')
                                                @if (session('oldData')['jenis'] == $item)
                                                    <option value="{{ $item }}" selected>{{ $item }}
                                                    </option>
                                                @else
                                                    <option value="{{ $item }}">
                                                        {{ $item }}
                                                    </option>
                                                @endif
                                            @else
                                                <option value="{{ $item }}">
                                                    {{ $item }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('jenis')
                                        <span id="jenis" class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal">Tanggal</label>
                                    <input type="date" id="tanggal" name="tanggal"
                                        class="form-control @error('tanggal') is-invalid @enderror"
                                        value="{{ old('tanggal', date('Y-m-d')) }}" required>
                                    @error('tanggal')
                                        <span id="tanggal" class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        {{--                         
                        <?php
                        $date_by = [['nama' => 'Distribusi', 'field' => 'distribusi_at'], ['nama' => 'Sales Call', 'field' => 'updated_at']];
                        
                        ?>
                        <div class="form-group">
                            <label for="date_by">Berdasarkan Tanggal</label>
                            <select name="date_by" class="form-control select2 @error('date_by') is-invalid @enderror  "
                                id="date_by">
                                <option value="">-- Pilih --</option>
                                @foreach ($date_by as $key => $val)
                                    @if ($val['nama'] == 'Distribusi')
                                        <option value="{{ $val['field'] }}" selected>{{ $val['nama'] }}</option>
                                    @else
                                        <option value="{{ $val['field'] }}">{{ $val['nama'] }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('date_by')
                                <span id="date_by" class="error invalid-feedback">{{ $message }}</span>
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
                        </div> --}}
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
                    <div class="card-body table-responsive">
                        <table class="table table-head-fixed text-nowrap dataTables" id="dataTables">
                            <thead>
                                <tr>
                                    {{-- <th></th>
                                    <th>NO</th>
                                    <th>Nama</th>
                                    <th>Tanggal Data</th>
                                    <th>Tanggal Sudah Di Telepon</th>
                                    <th>Tanggal Belum Di Telepon</th>
                                    <th>Tanggal Call Out</th>
                                    <th>H-1 Data</th>
                                    <th>H-1 Call Out</th>
                                    <th>H-2 Data</th>
                                    <th>H-2 Call Out</th> --}}
                                    {{-- <th></th> --}}
                                    <th>NO</th>
                                    <th>Nama</th>
                                    {{-- <th>SM</th>
                                    <th>supervisor</th> --}}
                                    <th>Distribusi</th>
                                    <th>Sisah Data</th>
                                    <th>All Data</th>
                                    <th>All Call</th>
                                    <th>All Sisah Data</th>
                                    <th>All Call Out</th>
                                    <th>All Not Call OUT</th>
                                    <th>All Prospek</th>
                                    <th>All Closing</th>
                                    <th>All Reload</th>
                                    <th>Total Data (Terdistribusi | Belum Terdistribusi)(Reload)</th>
                                    <th>(Telepon | Belum Ditelepon)</th>
                                    <th>(Contact | Not Contact)</th>
                                    <th>(Prospek | Closing)</th>
                                    <th>Distribusi</th>
                                    <th>Sisah Data</th>
                                    <th>Total Data</th>
                                    <th>Today Call</th>
                                    <th>Today Sisah Data</th>
                                    <th>Today Call Out</th>
                                    <th>Today Prospek</th>
                                    <th>Today Prospek</th>
                                    <th>Today Closing</th>
                                    <th>Tanggal Data</th>
                                    <th>H-1 Data</th>
                                    <th>H-1 Call</th>
                                    <th>H-1 Call Out</th>
                                    <th>H-1 Prospek</th>
                                    <th>H-1 closing</th>
                                    <th>H-2 Data</th>
                                    <th>H-2 Call</th>
                                    <th>H-2 Call Out</th>
                                    <th>H-2 Prospek</th>
                                    <th>H-2 closing</th>
                                    <th>ALL contact Rate</th>
                                    <th>H-3 Data</th>
                                    <th>H-4 Data</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    {{-- <th></th> --}}
                                    <th></th>
                                    {{-- <th></th>
                                    <th></th> --}}
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
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
                    <h4 class="modal-title">Edit Campaign Prioritas</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="prioritas">Prioritas</label>
                        <input type="number" id="prioritas" name="prioritas"
                            class="form-control @error('prioritas') is-invalid @enderror"
                            value="{{ $data == '' ? old('prioritas') : old('prioritas', $data->prioritas) }}">
                        @error('prioritas')
                            <span id="prioritas" class="error invalid-feedback">{{ $message }}</span>
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
    <div class="modal fade" id="modalEdit">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Campaign Kode</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="kodeedit">Kode</label>
                        <input type="text" id="kodeedit" name="kodeedit"
                            class="form-control @error('kodeedit') is-invalid @enderror"
                            value="{{ $data == '' ? old('kodeedit') : old('kodeedit', $data->kodeedit) }}">
                        @error('kodeedit')
                            <span id="kodeedit" class="error invalid-feedback">{{ $message }}</span>
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
    <script src="{{ asset('adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
    {{-- <script src="{{ asset('adminlte/plugins/datatables-rowgroup/js/dataTables.rowGroup.min.js') }}"></script> --}}
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
    <script src="https://cdn.datatables.net/plug-ins/1.10.20/api/sum().js"></script>

    <script>
        $('.select2').select2();
        var roleuser_id = "<?php echo auth()->user()->roleuser_id; ?>";
        var cabang_id = "<?php echo auth()->user()->cabang_id; ?>";
        var hari = "<?php echo date('Y-m-d'); ?>";
        renderTable(hari);

        function tarikData(id, judul) {
            let text = "Apakah anda yakin, Akan Menarik Data " + judul + "?";
            if (confirm(text) == true) {
                $('#modal-overlay').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $.ajax({
                    type: 'POST',
                    url: "/dashboard/tarikcampaign",
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: id,
                    },
                    dataType: "json",
                    encode: true,
                }).done(function(data) {
                    setTimeout(() => {
                        $('#modal-overlay').hide();
                        $('body').removeClass('modal-open');
                        $('.modal-backdrop').remove();
                        toastAlert(data);
                        renderTable(hari);
                    }, 1000);
                });
            }
        }

        function modalEditkode(param) {
            linkid = '';
            tipe = '';
            $('#prioritas').val('');
            $('#kodeedit').val('');
            if (param != '') {
                $.ajax({
                    type: 'POST',
                    url: "/dashboard/fileexcel/detail",
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: param,
                    },
                    dataType: "json",
                    encode: true,
                }).done(function(data) {
                    console.log('aaaa');
                    tipe = param == '' ? 'POST' : 'PUT';
                    linkid = param == '' ? '' : '/' + param;
                    $('#kodeedit').val(data.kodeedit);
                });
            } else {
                tipe = param == '' ? 'POST' : 'PUT';
                linkid = param == '' ? '' : '/' + param;
            }
            $('#modalEdit').modal({
                backdrop: 'static',
            });
        }

        function modalEdit(param) {
            linkid = '';
            tipe = '';
            $('#prioritas').val('');
            if (param != '') {
                $.ajax({
                    type: 'POST',
                    url: "/dashboard/fileexcel/detail",
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: param,
                    },
                    dataType: "json",
                    encode: true,
                }).done(function(data) {
                    console.log('aaaa');
                    tipe = param == '' ? 'POST' : 'PUT';
                    linkid = param == '' ? '' : '/' + param;
                    if (data.prioritas_date == hari) {
                        console.log(data.prioritas);
                        $('#prioritas').val(data.prioritas);
                    }
                });
            } else {
                tipe = param == '' ? 'POST' : 'PUT';
                linkid = param == '' ? '' : '/' + param;
            }
            $('#modalAdd').modal({
                backdrop: 'static',
            });
        }

        function alertAdmin() {
            alert('Please contact your administrator website.');
        }

        function saveEditcallhistory() {
            console.log('aaaa');
            $.ajax({
                type: tipe,
                url: "/dashboard/fileexcel" + linkid,
                data: {
                    _token: '{{ csrf_token() }}',
                    prioritas: $('#prioritas').val(),
                    kodeedit: $('#kodeedit').val(),
                },
                dataType: "json",
                encode: true,
            }).done(function(data) {
                $('#modalAdd').modal('hide');
                $('#modalEdit').modal('hide');
                renderTable(hari);
                toastAlert(data);
                $('#prioritas').val('').change();
                $('#prioritkodeeditas').val('').change();
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
            if ($('#tanggal').val() != '') {
                renderTable($('#tanggal').val(), $('#jenis').val());
            } else {
                alert('Tidak dapat melakukan proses!');
            }
        }


        function renderTable(param, param1) {
            switch ($('#tipe').val()) {
                case 'Contact Rate':
                    paramSort = [
                        [36, 'desc']
                    ]
                    break;
                case 'Sisah Data Sebelumnya':
                    paramSort = [
                        [6, 'desc']
                    ]
                    break;
                case 'Distribusi Hari Ini':
                    paramSort = [
                        [7, 'desc']
                    ]
                    break;
                case 'Sudah di Telepon':
                    paramSort = [
                        [8, 'desc']
                    ]
                    break;
                case 'Sisah Data':
                    paramSort = [
                        [9, 'desc']
                    ]
                    break;
                case 'Telepon Di Angkat':
                    paramSort = [
                        [10, 'desc']
                    ]
                    break;
                case 'Prospek':
                    paramSort = [
                        [11, 'desc']
                    ]
                    break;
                case 'Closing':
                    paramSort = [
                        [12, 'desc']
                    ]
                    break;
                case 'Closing 3 hari':
                    paramSort = [
                        [12, 'desc'],
                        [18, 'desc'],
                        [23, 'desc'],
                    ]
                    break;
                default:
                    paramSort = [
                        [0, 'asc'],
                    ]
                    break;
            }
            paramColumn = [
                //     {
                //     className: 'dt-control',
                //     orderable: false,
                //     data: null,
                //     defaultContent: ''
                // },
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                }, {
                    data: null,
                    name: null,
                    render: {
                        _: "prioritas",
                        filter: "kode",
                        display: "campaign"
                    }
                }, {
                    data: 'total_data',
                    name: 'total_data',
                    visible: false,
                    searchable: false
                }, {
                    data: 'total_data1',
                    name: 'total_data1',
                    visible: false,
                    searchable: false
                }, {
                    data: 'total_nodistribusi',
                    name: 'total_nodistribusi',
                    visible: false,
                    searchable: false
                }, {
                    data: 'total_call',
                    name: 'total_call',
                    visible: false,
                    searchable: false
                }, {
                    data: 'total_nocall',
                    name: 'total_nocall',
                    visible: false,
                    searchable: false
                }, {
                    data: 'total_callout',
                    name: 'total_callout',
                    visible: false,
                    searchable: false
                }, {
                    data: 'total_nocallout',
                    name: 'total_nocallout',
                    visible: false,
                    searchable: false
                }, {
                    data: 'total_prospek',
                    name: 'total_prospek',
                    visible: false,
                    searchable: false
                }, {
                    data: 'total_closing',
                    name: 'total_closing',
                    visible: false,
                    searchable: false
                }, {
                    data: 'total_reload',
                    name: 'total_reload',
                    visible: false,
                    searchable: false
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
                    data: 'totData',
                    name: 'totData',
                    visible: false,
                    searchable: false
                }, {
                    data: 'totSisah',
                    name: 'totSisah',
                    visible: false,
                    searchable: false
                }, {
                    data: 'totToday',
                    name: 'totToday',
                    visible: false,
                    searchable: false
                }, {
                    data: 'total_call_today',
                    name: 'total_call_today',
                    visible: false,
                    searchable: false
                }, {
                    data: 'total_nocall',
                    name: 'total_nocall',
                    visible: false,
                    searchable: false
                }, {
                    data: 'total_callout_today',
                    name: 'total_callout_today',
                    visible: false,
                    searchable: false
                }, {
                    data: 'total_nocallout_today',
                    name: 'total_nocallout_today',
                    visible: false,
                    searchable: false
                }, {
                    data: 'total_prospek_today',
                    name: 'total_prospek_today',
                    visible: false,
                    searchable: false
                }, {
                    data: 'total_closing_today',
                    name: 'total_closing_today',
                    visible: false,
                    searchable: false
                }, {
                    data: null,
                    name: null,
                    render: {
                        _: "total_data_today",
                        filter: "total_data_today",
                        display: "today"
                    }
                }, {
                    data: null,
                    name: null,
                    render: {
                        _: "total_data_2",
                        filter: "total_data_2",
                        display: "h2"
                    }
                }, {
                    data: 'total_call_2',
                    name: 'total_call_2',
                    visible: false,
                    searchable: false
                }, {
                    data: 'total_callout_2',
                    name: 'total_callout_2',
                    visible: false,
                    searchable: false
                }, {
                    data: 'total_prospek_2',
                    name: 'total_prospek_2',
                    visible: false,
                    searchable: false
                }, {
                    data: 'total_closing_2',
                    name: 'total_closing_2',
                    visible: false,
                    searchable: false
                }, {
                    data: null,
                    name: null,
                    render: {
                        _: "total_data_3",
                        filter: "total_data_3",
                        display: "h3"
                    }
                }, {
                    data: 'total_call_3',
                    name: 'total_call_3',
                    visible: false,
                    searchable: false
                }, {
                    data: 'total_callout_3',
                    name: 'total_callout_3',
                    visible: false,
                    searchable: false
                }, {
                    data: 'total_prospek_3',
                    name: 'total_prospek_3',
                    visible: false,
                    searchable: false
                }, {
                    data: 'total_closing_3',
                    name: 'total_closing_3',
                    visible: false,
                    searchable: false
                }, {
                    data: 'contactRate',
                    name: 'contactRate',
                    visible: false,
                    searchable: false
                }, {
                    data: null,
                    name: null,
                    render: {
                        _: "total_data_4",
                        filter: "total_data_4",
                        display: "h4"
                    }
                }, {
                    data: null,
                    name: null,
                    render: {
                        _: "total_data_5",
                        filter: "total_data_5",
                        display: "h5"
                    }
                }
            ];
            var tables1 = new $('#dataTables').DataTable({
                autoWidth: true,
                bDestroy: true,
                initComplete: function(settings, json) {
                    fromTabel = this.api().data().length;
                },
                ajax: {
                    type: 'POST',
                    url: '/dashboard/ajaxcampaigncall',
                    data: {
                        _token: '{{ csrf_token() }}',
                        tanggal: param,
                        jenis: param1,
                    }
                },
                lengthMenu: [
                    [-1],
                    ["All"]
                ],
                columns: paramColumn,
                columnDefs: [{
                    targets: '_all',
                    className: "text-center",
                }, {
                    targets: 1,
                    className: "text-left",
                }],
                // dom: 'lBfrtip',
                // buttons: [{
                //     extend: 'excel',
                //     text: 'Export Excel',
                //     filename: 'export_callhistory_' + hari
                // }, ],
                footerCallback: function(row, data, start, end, display) {
                    let api = this.api();
                    let vLink = '';
                    let vLink1 = '';
                    let vLinkprospek1 = '';
                    let vLinkclosing1 = '';
                    // if (typeof data[0] === "undefined") {} else {
                    //     vLink = data[0]['linkTotal'];
                    //     vLink1 = data[0]['linkTotal1'];
                    //     vLinkprospek1 = data[0]['linkTotalprospek1'];
                    //     vLinkclosing1 = data[0]['linkTotalclosing1'];
                    // }


                    let sBox = $('.dataTables_filter input').val();

                    // Remove the formatting to get integer data for summation
                    let intVal = function(i) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '') * 1 :
                            typeof i === 'number' ?
                            i :
                            0;
                    };

                    // Total over all pages
                    total = api
                        .column(6)
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);

                    // Total over this page
                    gtotalData = api
                        .column(2, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    gtotalDistribusi = api
                        .column(3, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    gtotalnoDistribusi = api
                        .column(4, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    gtotalCall = api
                        .column(5, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    gtotalnoCall = api
                        .column(6, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    gtotalCallout = api
                        .column(7, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    gtotalnoCallout = api
                        .column(8, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    gtotalProspek = api
                        .column(9, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    gtotalClosing = api
                        .column(10, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    gtotalReload = api
                        .column(11, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    // Update footer
                    gpersendistribusi = Math.round(parseFloat(gtotalDistribusi) / parseFloat(gtotalData) * 100);
                    gpersennodistribusi = Math.round(parseFloat(gtotalnoDistribusi) / parseFloat(gtotalData) *
                        100);
                    gpersencall = Math.round(parseFloat(gtotalCall) / parseFloat(gtotalDistribusi) *
                        100);
                    gpersennocall = Math.round(parseFloat(gtotalnoCall) / parseFloat(gtotalDistribusi) *
                        100);
                    gpersencallout = Math.round(parseFloat(gtotalCallout) / parseFloat(gtotalCall) *
                        100);
                    gpersennocallout = Math.round(parseFloat(gtotalnoCallout) / parseFloat(gtotalCall) *
                        100);
                    api.column(12).footer().innerHTML =
                        '<span style="color:#009b9b"><span title="total data">' + gtotalData + '</span>' +
                        ' (' +
                        '<span style="color:#eb7904" title="total data terdistribusi">' + gtotalDistribusi +
                        '</span>' + ' | ' +
                        '<span style="color:#eb0424" title="total belum terdistribusi">' + gtotalnoDistribusi +
                        '</span>' + ')' +
                        ' (' +
                        '<span style="color:#eb7904" title="total reload">' + gtotalReload +
                        '</span>' + ')';

                    api.column(13).footer().innerHTML =
                        ' (' +
                        '<span style="color:#eb7904" title="total telepon">' + gtotalCall +
                        '</span>' + ' | ' +
                        '<span style="color:#eb0424" title="total belum telepon">' + gtotalnoCall +
                        '</span>' + ')';

                    api.column(14).footer().innerHTML =
                        ' (' +
                        '<span style="color:#009b05" title="total contact">' + gtotalCallout +
                        '(' + gpersencallout + '%)' + '</span>' + ' | ' +
                        '<span style="color:#eb0424" title="total not contact">' + gtotalnoCallout +
                        '(' + gpersennocallout + '%)' + '</span>' + ')';

                    api.column(15).footer().innerHTML =
                        ' (' +
                        '<span style="color:#eb7904" title="total contact">' + gtotalProspek +
                        '</span>' + ' | ' +
                        '<span style="color:#009b05" title="total not contact">' + gtotalClosing +
                        '</span>' + ')';

                    // api.column(12).footer().innerHTML =
                    //     '<span style="color:#009b9b"><span title="total data">' + gtotalData + '</span>' +
                    //     ' (' +
                    //     '<span style="color:#eb7904" title="total data terdistribusi">' + gtotalDistribusi +
                    //     '(' + gpersendistribusi + '%)' + '</span>' + ' | ' +
                    //     '<span style="color:#eb0424" title="total belum terdistribusi">' + gtotalnoDistribusi +
                    //     '(' + gpersennodistribusi + '%)' + '</span>' + ')' +
                    //     ' (' +
                    //     '<span style="color:#eb7904" title="total telepon">' + gtotalCall +
                    //     '(' + gpersencall + '%)' + '</span>' + ' | ' +
                    //     '<span style="color:#eb0424" title="total belum telepon">' + gtotalnoCall +
                    //     '(' + gpersennocall + '%)' + '</span>' + ')' +
                    //     ' (' +
                    //     '<span style="color:#009b05" title="total contact">' + gtotalCallout +
                    //     '(' + gpersencallout + '%)' + '</span>' + ' | ' +
                    //     '<span style="color:#eb0424" title="total not contact">' + gtotalnoCallout +
                    //     '(' + gpersennocallout + '%)' + '</span>' + ')';
                    // gtotalDistribusi + '(' + gpersendistribusi + '%)' + ' | ' +
                    // gtotalnoDistribusi + '(' + gpersennodistribusi + '%)' + ')(' +
                    // gtotalCall + '(' + gpersencall + '%)' + ' | ' +
                    // gtotalnoCall + '(' + gpersennocall + '%)' + ')(' +
                    // gtotalCallout + '(' + gpersencallout + '%)' + ' | ' +
                    // gtotalnoCallout + '(' + gpersennocallout + '%)' + ')';


                    gtotalDatatoday = api
                        .column(16, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    gtotalDistribusitoday = api
                        .column(18, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    gtotalnoDistribusitoday = api
                        .column(17, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    gtotalCalltoday = api
                        .column(19, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    gtotalnoCalltoday = api
                        .column(20, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    gtotalCallouttoday = api
                        .column(21, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    gtotalnoCallouttoday = api
                        .column(22, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    gtotalProspektoday = api
                        .column(23, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    gtotalClosingtoday = api
                        .column(24, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    // Update footer
                    gpersendistribusitoday = Math.round(parseFloat(gtotalDistribusitoday) / parseFloat(
                        gtotalDatatoday) * 100);
                    gpersennodistribusitoday = Math.round(parseFloat(gtotalnoDistribusitoday) / parseFloat(
                        gtotalDatatoday) * 100);
                    gpersencalltoday = Math.round(parseFloat(gtotalCalltoday) / parseFloat(
                            gtotalDatatoday) *
                        100);
                    gpersennocalltoday = Math.round(parseFloat(gtotalnoCalltoday) / parseFloat(
                            gtotalDatatoday) *
                        100);
                    gpersencallouttoday = Math.round(parseFloat(gtotalCallouttoday) / parseFloat(
                            gtotalCalltoday) *
                        100);
                    gpersennocallouttoday = Math.round(parseFloat(gtotalnoCallouttoday) / parseFloat(
                            gtotalCalltoday) *
                        100);
                    api.column(25).footer().innerHTML =
                        '<span style="color:#009b9b"><span title="total data">' + gtotalDatatoday + '</span>' +
                        ' (' +
                        '<span style="color:#009b9b" title="total data sisah kemarin">' +
                        gtotalnoDistribusitoday +
                        '</span>' + ' | ' +
                        '<span style="color:#009b9b" title="total distribusi hari ini">' +
                        gtotalDistribusitoday +
                        '</span>' + ')' +
                        ' (' +
                        '<span style="color:#eb7904" title="total telepon">' + gtotalCalltoday +
                        '</span>' + ' | ' +
                        '<span style="color:#eb0424" title="total belum telepon">' + gtotalnoCalltoday +
                        '</span>' + ')' +
                        ' (' +
                        '<span style="color:#009b05" title="total contact">' + gtotalCallouttoday +
                        '(' + gpersencallouttoday + '%)' + '</span>' + ' | ' +
                        '<span style="color:#eb0424" title="total not contact">' +
                        gtotalnoCallouttoday +
                        '(' + gpersennocallouttoday + '%)' + '</span>' + ')' +
                        ' (' +
                        '<span style="color:#eb7904" title="total telepon">' + gtotalProspektoday +
                        '</span>' + ' | ' +
                        '<span style="color:#009b05" title="total belum telepon">' + gtotalClosingtoday +
                        '</span>' + ')';

                    // gtotalDatatoday + ' (' +
                    // gtotalnoDistribusitoday + '(' + gpersennodistribusitoday + '%)' + ' + ' +
                    // gtotalDistribusitoday + '(' + gpersendistribusitoday + '%)' + ')(' +
                    // gtotalCalltoday + '(' + gpersencalltoday + '%)' + ' | ' +
                    // gtotalnoCalltoday + '(' + gpersennocalltoday + '%)' + ')(' +
                    // gtotalCallouttoday + '(' + gpersencallouttoday + '%)' + ' | ' +
                    // gtotalnoCallouttoday + '(' + gpersennocallouttoday + '%)' + ')';
                    // gtotalSisahdatakemarin +
                    // '</a> + ' +
                    // gtotalDishariini +
                    // ') <a href="' + vLink + '&search=' + sBox + '" target="_blank"> ' + gtotalCallhariini +
                    // '</a> | ' + gtotalSisahdatahariini +
                    // ' | <a href="' + vLink1 + '&search=' + sBox + '" target="_blank"> ' +
                    // gtotalCallouthariini + '</a>' +
                    // ' | <a href="' + vLinkprospek1 + '&search=' + sBox + '" target="_blank"> ' +
                    // gtotalprospek + '</a>' +
                    // ' | <a href="' + vLinkclosing1 + '&search=' + sBox + '" target="_blank"> ' +
                    // gtotalclosing + '</a>';

                    // api.column(14).footer().innerHTML =
                    //     gtotalCall2 +
                    //     ' | ' +
                    //     gtotalCallout2 +
                    //     ' | ' +
                    //     gtotalprospek2 +
                    //     ' | ' +
                    //     gtotalclosing2;
                    // api.column(19).footer().innerHTML =
                    //     gtotalCall3 +
                    //     ' | ' +
                    //     gtotalCallout3 +
                    //     ' | ' +
                    //     gtotalprospek3 +
                    //     ' | ' +
                    //     gtotalclosing3;
                },
                processing: false,
                serverSide: false,
                order: paramSort,
            });

            tables1.on('click', 'td.dt-control', function() {
                var tr = $(this).closest('tr');
                var row = tables1.row(tr);

                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Open this row
                    if (roleuser_id == '1' || roleuser_id == '4' || roleuser_id == '6' || (roleuser_id == '2' &&
                            cabang_id == '4')) {
                        row.child(format(row.data())).show();
                        tr.addClass('shown');
                    }
                }
            });
            tables1.buttons().container().appendTo('#dataTables_wrapper .col-md-6:eq(0)');

            function format(d) {
                console.log(d);
                var div = $('<div/>')
                    .addClass('loading')
                    .text('Loading...');

                $.ajax({
                    type: "POST",
                    url: '/dashboard/ajaxcampaigncall/detail',
                    data: {
                        _token: '{{ csrf_token() }}',
                        user_id: d.id,
                        tanggal: param,
                    },
                    dataType: 'json',
                    success: function(json) {
                        div
                            .html(json)
                            .removeClass('loading');
                    }
                });

                return div;
                // console.log(d);
                // var hasil = '';
                // $.ajax({
                //     type: "POST",
                //     url: "/dashboard/ajaxsalescall2/detail",
                //     data: {
                //         _token: '{{ csrf_token() }}',
                //         user_id: '150'
                //     },
                //     encode: true,
                // }).done(function(data) {
                //     hasil = data;
                //     console.log(data);
                // });

                // setTimeout(function() {
                //     return ('<div class="details" id="details">' + hasil + '</div>');
                // }, 2000);

                // `d` is the original data object for the row
                // return (
                //     '<tr>' +
                //     '<td></td>' +
                //     '<td>1</td>' +
                //     '<td>' +
                //     d.nama +
                //     '</td>' +
                //     '<td>Extension number:</td>' +
                //     '<td>' +
                //     d.provider +
                //     '</td>' +
                //     '<td>Extra info:</td>' +
                //     '<td>And any further details here (images etc)...</td>' +
                //     '</tr>'
                // );
            }
        }
        $('#dataTables').on('search.dt', function() {
            var value = $('.dataTables_filter input').val();
            $('.linkD').html(value);
            // console.log(value); // <-- the value
        });
    </script>
@endsection
