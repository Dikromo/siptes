@extends('admin.layouts.main')

@section('container')
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline">
                    <div class="card-body">
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
                                    <th>Tanggal Data</th>
                                    <th>H-1 Data</th>
                                    <th>H-2 Data</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
@endsection
@section('addScript')
    <script src="{{ asset('adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
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
        $('.select2').select2();
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
            // paramColumn = [{
            //     className: 'dt-control',
            //     orderable: false,
            //     data: null,
            //     defaultContent: ''
            // }, {
            //     data: 'DT_RowIndex',
            //     name: 'DT_RowIndex',
            //     orderable: false,
            //     searchable: false
            // }, {
            //     data: 'name',
            //     name: 'name'
            // }, {
            //     data: 'total_data_today',
            //     name: 'total_data_today',
            // }, {
            //     data: 'total_call_today',
            //     name: 'total_call_today',
            // }, {
            //     data: 'total_nocall',
            //     name: 'total_nocall',
            // }, {
            //     data: 'total_callout_today',
            //     name: 'total_callout_today',
            // }, {
            //     data: 'total_data_2',
            //     name: 'total_data_2',
            // }, {
            //     data: 'total_call_2',
            //     name: 'total_call_2',
            // }, {
            //     data: 'total_nocall_2',
            //     name: 'total_nocall_2',
            // }, {
            //     data: 'total_callout_2',
            //     name: 'total_callout_2',
            // }, {
            //     data: 'total_data_3',
            //     name: 'total_data_3',
            // }, {
            //     data: 'total_call_3',
            //     name: 'total_call_3',
            // }, {
            //     data: 'total_nocall_3',
            //     name: 'total_nocall_3',
            // }, {
            //     data: 'total_callout_3',
            //     name: 'total_callout_3',
            // }];
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
                data: null,
                name: null,
                render: {
                    _: "total_data_3",
                    filter: "total_data_3",
                    display: "h3"
                }
            }];
            var tables1 = new $('#dataTables').DataTable({
                autoWidth: false,
                bDestroy: true,
                initComplete: function(settings, json) {
                    fromTabel = this.api().data().length;
                },
                ajax: {
                    type: 'POST',
                    url: '/dashboard/ajaxsalescall2',
                    data: {
                        _token: '{{ csrf_token() }}',
                        tanggal: param,
                    }
                },
                lengthMenu: [
                    [10, 50, 100, 200, 500, -1],
                    [10, 50, 100, 200, 500, "All"]
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
                processing: false,
                serverSide: false,
                order: [
                    [1, 'asc']
                ]
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
                    row.child(format(row.data())).show();
                    tr.addClass('shown');
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
                    url: '/dashboard/ajaxsalescall2/detail',
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
    </script>
@endsection
