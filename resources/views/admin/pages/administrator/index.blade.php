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
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="produk_id">Produk</label>
                                    <select name="produk_id"
                                        class="form-control select2 @error('produk_id') is-invalid @enderror  "
                                        id="produk_id">
                                        <option value="">-- Pilih --</option>
                                        @foreach ($produkSelect as $item)
                                            <option value="{{ $item->id }}">{{ $item->tipe . ' - ' . $item->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('produk_id')
                                        <span id="produk_id" class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?php
                                $vUserid = $get->id != '' ? decrypt($get->id) : '';
                                $vTanggal = $get->tanggal != '' ? decrypt($get->tanggal) : '';
                                ?>
                                <div class="form-group">
                                    <label for="user_id">Sales</label>
                                    <select name="user_id"
                                        class="form-control select2 @error('user_id') is-invalid @enderror  "
                                        id="user_id">
                                        <option value="">-- Pilih --</option>
                                        @foreach ($userSelect as $item)
                                            @if ($get != '')
                                                @if ($vUserid == $item->id)
                                                    <option value="{{ $item->id }}" selected>{{ $item->name }}
                                                    </option>
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
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status"
                                        class="form-control select2 @error('status') is-invalid @enderror  " id="status">
                                        <option value="">-- Pilih --</option>
                                        @foreach ($statusSelect as $item)
                                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <span id="status" class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fromTanggal">Dari Tanggal</label>
                                    <input type="date" id="fromTanggal" name="fromTanggal"
                                        class="form-control @error('fromTanggal') is-invalid @enderror"
                                        value="{{ $vTanggal == '' ? old('fromTanggal', date('Y-m-d', strtotime('-7 days'))) : $vTanggal }}"
                                        required>
                                    @error('fromTanggal')
                                        <span id="fromTanggal" class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="toTanggal">Sampai Tanggal</label>
                                <input type="date" id="toTanggal" name="toTanggal"
                                    class="form-control @error('toTanggal') is-invalid @enderror"
                                    value="{{ $vTanggal == '' ? old('toTanggal', date('Y-m-d')) : $vTanggal }}" required>
                                @error('toTanggal')
                                    <span id="toTanggal" class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
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
                                    <th>4 Digit No Telp</th>
                                    <th>Provider</th>
                                    <th>Status</th>
                                    <th>Status Date</th>
                                    <th>Limit</th>
                                    <th>MOB</th>
                                    <th>Bank CC</th>
                                    <th>Loan Apply</th>
                                    <th>Subproduk</th>
                                    <th>Deskripsi</th>
                                    <th>End Call</th>
                                    <th>Status Admin</th>
                                    <th>Tanggal Status Admin </th>
                                    <th>Sales</th>
                                    <th>SPV</th>
                                    <th>SM</th>
                                    <th>Site</th>
                                    {{-- <th>Reason</th> --}}
                                    <th></th>
                                </tr>
                            </thead>
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
                    <h4 class="modal-title">Update Status Nasabah</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php
                    $arrayTipe = ['Verifikasi', 'Submit', 'Pending', 'Cancel'];
                    ?>
                    <div class="form-group">
                        <label for="statusadmin">Status Admin</label>
                        <input type="hidden" id="mproduk_id" name="mproduk_id">
                        <input type="hidden" id="msubproduk_id" name="msubproduk_id">
                        <input type="hidden" id="mtahap" name="mtahap">
                        <select name="statusadmin"
                            class="form-control select2 @error('statusadmin') is-invalid @enderror  " id="statusadmin"
                            style="width: 100%">
                            <option value="">-- Pilih --</option>
                            @foreach ($arrayTipe as $item)
                                <option value="{{ $item }}">
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>
                        @error('statusadmin')
                            <span id="statusadmin" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="statusadmin_date">Tanggal Status Admin</label>
                        <input type="date" step="1" id="statusadmin_date" name="statusadmin_date"
                            class="form-control @error('statusadmin_date') is-invalid @enderror"
                            value="{{ $data == '' ? old('statusadmin_date') : old('statusadmin_date', $data->statusadmin_date) }}">
                        @error('statusadmin_date')
                            <span id="statusadmin_date" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="temp_limit">Temporary Limit</label>
                        <input type="text" id="temp_limit" name="temp_limit"
                            class="form-control @error('temp_limit') is-invalid @enderror"
                            value="{{ $data == '' ? old('temp_limit') : old('temp_limit', $data->temp_limit) }}"
                            data-inputmask="'alias': 'currency', 'placeholder': '', 'digits': '0','digitsOptional':'!1', 
                            'rightAlign':'false', 'allowMinus': 'false', 'showMaskOnFocus': 'false', 
                            'showMaskOnHover': 'false','groupSeparator':'.','removeMaskOnSubmit': 'true'"
                            data-mask>
                        @error('temp_limit')
                            <span id="temp_limit" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="remarksadmin">Remarks Admin</label>
                        <input type="text" id="remarksadmin" name="remarksadmin"
                            class="form-control @error('remarksadmin') is-invalid @enderror"
                            value="{{ $data == '' ? old('remarksadmin') : old('remarksadmin', $data->remarksadmin) }}"
                            required>
                        @error('remarksadmin')
                            <span id="remarksadmin" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <?php
                    $arrayTipe = ['Verifikasi', 'Disetujui', 'Ditolak', 'Cancel'];
                    ?>
                    <div class="form-group">
                        <label for="statusbank">Status Bank</label>
                        <select name="statusbank"
                            class="fbank form-control select2 @error('statusbank') is-invalid @enderror  " id="statusbank"
                            style="width: 100%">
                            <option value="">-- Pilih --</option>
                            @foreach ($arrayTipe as $item)
                                <option value="{{ $item }}">
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>
                        @error('statusbank')
                            <span id="statusbank" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="statusbank_date">Tanggal Status</label>
                        <input type="date" step="1" id="statusbank_date" name="statusbank_date"
                            class="fbank form-control @error('statusbank_date') is-invalid @enderror"
                            value="{{ $data == '' ? old('statusbank_date') : old('statusbank_date', $data->statusbank_date) }}">
                        @error('statusbank_date')
                            <span id="statusbank_date" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="disburse_limit">Disbursed Limit</label>
                        <input type="text" id="disburse_limit" name="disburse_limit"
                            class="fbank form-control @error('disburse_limit') is-invalid @enderror"
                            value="{{ $data == '' ? old('disburse_limit') : old('disburse_limit', $data->disburse_limit) }}"
                            data-inputmask="'alias': 'currency', 'placeholder': '', 'digits': '0','digitsOptional':'!1', 
                                                    'rightAlign':'false', 'allowMinus': 'false', 'showMaskOnFocus': 'false', 
                                                    'showMaskOnHover': 'false','groupSeparator':'.','removeMaskOnSubmit': 'true'"
                            data-mask>
                        @error('disburse_limit')
                            <span id="disburse_limit" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="remarksbank">Remarks Bank</label>
                        <input type="text" id="remarksbank" name="remarksbank"
                            class="fbank form-control @error('remarksbank') is-invalid @enderror"
                            value="{{ $data == '' ? old('remarksbank') : old('remarksbank', $data->remarksbank) }}">
                        @error('remarksbank')
                            <span id="remarksbank" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="checkbeforeSave();">Save changes</button>
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
    <script src="{{ asset('adminlte/plugins/inputmask/jquery.inputmask.min.js') }}"></script>

    <script type="text/javascript">
        $('.select2').select2();
        $('[data-mask]').inputmask();
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
            $('#statusadmin').val('').change();
            $('#statusadmin_date').val('');
            $('#remarksadmin').val('');
            $('#statusbank').val('').change();
            $('#statusbank_date').val('');
            $('#remarksbank').val('');
            $('#temp_limit').val('');
            $('#disburse_limit').val('');
            $('#mproduk_id').val('');
            $('#msubproduk_id').val('');
            $('#mtahap').val('');
            if (param != '') {
                $.ajax({
                    type: 'POST',
                    url: "/administrator/calltracking/detail",
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: param,
                    },
                    dataType: "json",
                    encode: true,
                }).done(function(data) {
                    tipe = param == '' ? 'POST' : 'PUT';
                    linkid = param == '' ? '' : '/' + param;
                    $('#statusadmin').val(data.statusadmin).change();
                    $('#statusadmin_date').val(data.admintgl);
                    $('#remarksadmin').val(data.remarksadmin);
                    $('#statusbank').val(data.statusbank).change();
                    $('#statusbank_date').val(data.banktgl);
                    $('#remarksbank').val(data.remarksbank);
                    $('#temp_limit').val(data.temp_limit);
                    $('#disburse_limit').val(data.disburse_limit);
                    $('#mproduk_id').val(data.produk_id);
                    $('#msubproduk_id').val(data.subproduk_id);

                    if (data.statusadmin == 'Submit') {
                        $('#mtahap').val('2');
                        $(".fbank").prop("disabled", false);
                    } else {
                        $(".fbank").prop("disabled", true);
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

        function checkbeforeSave() {
            if ($('#mtahap').val() == '' && $('#statusadmin').val() != '' && $('#statusadmin_date').val() != '') {
                if ($('#statusadmin').val() == 'Submit') {
                    if ($('#mproduk_id').val() == '6' && ($('#temp_limit').val() == '' || $('#temp_limit').val() == null) &&
                        $('#msubproduk_id').val() == '1') {
                        alert('Mohon isikan temporary limit');
                    } else {
                        saveEditcallhistory();
                    }
                } else {
                    if ($('#statusadmin').val() == '' || $('#statusadmin_date').val() == '' || $('#remarksadmin').val() ==
                        '') {
                        alert('Mohon isikan form dengan benar');
                    } else {
                        saveEditcallhistory();
                    }
                }
            } else if ($('#mtahap').val() == '2' && $('#statusbank').val() != '' && $('#statusbank_date').val() != '') {
                if ($('#statusbank').val() == 'Disetujui') {
                    if ($('#mproduk_id').val() == '6' && ($('#disburse_limit').val() == '' || $('#disburse_limit').val() ==
                            null)) {
                        alert('Mohon isikan disburse limit');
                    } else {
                        saveEditcallhistory();
                    }
                } else {
                    if ($('#statusbank').val() == '' || $('#statusbank_date').val() == '' || $('#remarksbank').val() ==
                        '') {
                        alert('Mohon isikan form dengan benar');
                    } else {
                        saveEditcallhistory();
                    }
                }
            } else {
                alert('Mohon isikan form dengan benar');
            }
        }

        function saveEditcallhistory() {
            $.ajax({
                type: tipe,
                url: "/administrator/calltracking" + linkid,
                data: {
                    _token: '{{ csrf_token() }}',
                    statusadmin: $('#statusadmin').val(),
                    statusadmin_date: $('#statusadmin_date').val(),
                    remarksadmin: $('#remarksadmin').val(),
                    statusbank: $('#statusbank').val(),
                    statusbank_date: $('#statusbank_date').val(),
                    remarksbank: $('#remarksbank').val(),
                    temp_limit: $('#temp_limit').inputmask('unmaskedvalue'),
                    disburse_limit: $('#disburse_limit').inputmask('unmaskedvalue'),
                },
                dataType: "json",
                encode: true,
            }).done(function(data) {
                $('#modalAdd').modal('hide');
                toastAlert(data);
                renderTable($('#fromTanggal').val(), $('#toTanggal').val());
                $('#statusadmin').val('').change();
                $('#statusadmin_date').val('');
                $('#remarksadmin').val('');
                $('#statusbank').val('').change();
                $('#statusbank_date').val('');
                $('#remarksbank').val('');
                $('#temp_limit').val('');
                $('#disburse_limit').val('');
                $('#mproduk_id').val('');
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
                data: 'no_telp',
                name: 'customers.no_telp',
            }, {
                data: 'provider',
                name: 'customers.provider',
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
                data: 'updated_at',
                name: 'updated_at'
            }, {
                data: 'statusadmin',
                name: 'statusadmin'
            }, {
                data: 'statusadmin_date',
                name: 'statusadmin_date'
            }, {
                data: 'salesnama',
                name: 'sales.name as salesnama',
                visible: false
            }, {
                data: 'spvnama',
                name: 'parentuser.name as spvnama',
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
            paramLength = [
                [10, 50, 100, 200, 500, -1],
                [10, 50, 100, 200, 500, "All"]
            ];
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
                    url: '/administrator/ajax/calltracking',
                    data: {
                        _token: '{{ csrf_token() }}',
                        fromtanggal: param1,
                        totanggal: param2,
                        user_id: $('#user_id').val(),
                        status: $('#status').val(),
                        produk_id: $('#produk_id').val(),
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
                    text: 'Export Excel',
                    filename: 'export_calltracking_' + hari
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
