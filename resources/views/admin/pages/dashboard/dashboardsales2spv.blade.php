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
                        <div class="form-group">
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
                        </div>
                        <div class="form-group">
                            <label for="tanggal">Tanggal</label>
                            <input type="date" id="tanggal" name="tanggal"
                                class="form-control @error('tanggal') is-invalid @enderror"
                                value="{{ old('tanggal', date('Y-m-d')) }}" required>
                            @error('tanggal')
                                <span id="tanggal" class="error invalid-feedback">{{ $message }}</span>
                            @enderror
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
                                    <th></th>
                                    <th>NO</th>
                                    <th>Nama</th>
                                    <th>SM</th>
                                    <th>supervisor</th>
                                    <th>Distribusi</th>
                                    <th>Sisah Data</th>
                                    <th>Total Data</th>
                                    <th>Today Call</th>
                                    <th>Today Sisah Data</th>
                                    <th>Today Call Out</th>
                                    <th>Today Prospek</th>
                                    <th>Today closing</th>
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
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
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
    <div class="modal fade" id="modalDetail">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Dashboard Telemarketing</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body tableDetails" style="overflow:auto;">
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

        function proses() {
            $("#dataTables").DataTable().off('click');
            $("#dataTables").DataTable().clear().destroy();
            if ($('#tanggal').val() != '') {
                renderTable($('#tanggal').val());
            } else {
                alert('Tidak dapat melakukan proses!');
            }
        }


        function renderTable(param) {
            switch ($('#tipe').val()) {
                case 'Total Data':
                    paramSort = [
                        [5, 'desc']
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
                        [5, 'desc']
                    ]
                    break;
            }
            paramColumn = [{
                className: 'dt-control',
                orderable: false,
                data: null,
                defaultContent: ''
            }, {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            }, {
                data: 'name',
                name: 'name'
            }, {
                data: 'smname',
                name: 'sm.name as smname',
                visible: false,
            }, {
                data: 'smname',
                name: 'sm.name as smname',
                visible: false,
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
            }];
            var tables1 = new $('#dataTables').DataTable({
                autoWidth: false,
                bDestroy: true,
                initComplete: function(settings, json) {
                    fromTabel = this.api().data().length;
                },
                ajax: {
                    type: 'POST',
                    url: '/dashboard/ajaxsalescall2sm',
                    data: {
                        _token: '{{ csrf_token() }}',
                        tanggal: param,
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
                    if (typeof data[0] === "undefined") {} else {
                        vLink = data[0]['linkTotal'];
                        vLink1 = data[0]['linkTotal1'];
                        vLinkprospek1 = data[0]['linkTotalprospek1'];
                        vLinkclosing1 = data[0]['linkTotalclosing1'];
                    }


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
                    gtotalDistribusi = api
                        .column(5, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    gtotalSisahdatakemarin = api
                        .column(6, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    gtotalDishariini = api
                        .column(7, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    gtotalCallhariini = api
                        .column(8, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    gtotalSisahdatahariini = api
                        .column(9, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    gtotalCallouthariini = api
                        .column(10, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    gtotalprospek = api
                        .column(11, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    gtotalclosing = api
                        .column(12, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);


                    gtotalCall2 = api
                        .column(15, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    gtotalCallout2 = api
                        .column(16, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    gtotalprospek2 = api
                        .column(17, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    gtotalclosing2 = api
                        .column(18, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);


                    gtotalCall3 = api
                        .column(20, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    gtotalCallout3 = api
                        .column(21, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    gtotalprospek3 = api
                        .column(22, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    gtotalclosing3 = api
                        .column(23, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    // Update footer
                    api.column(13).footer().innerHTML =
                        gtotalDistribusi + ' (' +
                        gtotalSisahdatakemarin +
                        '</a> + ' +
                        gtotalDishariini +
                        ') <a href="' + vLink + '&search=' + sBox + '" target="_blank"> ' + gtotalCallhariini +
                        '</a> | ' + gtotalSisahdatahariini +
                        ' | <a href="' + vLink1 + '&search=' + sBox + '" target="_blank"> ' +
                        gtotalCallouthariini + '</a>' +
                        ' | <a href="' + vLinkprospek1 + '&search=' + sBox + '" target="_blank"> ' +
                        gtotalprospek + '</a>' +
                        ' | <a href="' + vLinkclosing1 + '&search=' + sBox + '" target="_blank"> ' +
                        gtotalclosing + '</a>';

                    api.column(14).footer().innerHTML =
                        gtotalCall2 +
                        ' | ' +
                        gtotalCallout2 +
                        ' | ' +
                        gtotalprospek2 +
                        ' | ' +
                        gtotalclosing2;
                    api.column(19).footer().innerHTML =
                        gtotalCall3 +
                        ' | ' +
                        gtotalCallout3 +
                        ' | ' +
                        gtotalprospek3 +
                        ' | ' +
                        gtotalclosing3;
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
                    if (roleuser_id == '1' || roleuser_id == '4' || roleuser_id == '5' || roleuser_id == '6' || (
                            roleuser_id == '2' &&
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
                    url: '/dashboard/ajaxsalescall2spv',
                    data: {
                        _token: '{{ csrf_token() }}',
                        user_id: d.temp_id,
                        tipe: 'spv',
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

        function tablesDetail(tempid) {
            $('#modalDetail').modal({
                backdrop: 'static',
            });
            var div = $('<div/>')
                .addClass('loading')
                .text('Loading...');
            $('.tableDetails').html(div);
            $.ajax({
                type: "POST",
                url: '/dashboard/ajaxsalescall2spv/detail',
                data: {
                    _token: '{{ csrf_token() }}',
                    user_id: tempid,
                    tipe: 'spv',
                    tanggal: $('#tanggal').val(),
                },
                dataType: 'json',
                success: function(json) {
                    $('.tableDetails').html(json);
                    // div
                    //     .html(json)
                    //     .removeClass('loading')
                    //     .addClass('row');
                    // $('.tableDetails').html(div);
                }
            });

        }
        $('#dataTables').on('search.dt', function() {
            var value = $('.dataTables_filter input').val();
            $('.linkD').html(value);
            // console.log(value); // <-- the value
        });
    </script>
@endsection
