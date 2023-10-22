@extends('admin.layouts.main')

@section('container')
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="group_fileexcels_id">Group Campaign</label>
                                    <select name="group_fileexcels_id"
                                        class="form-control select2 @error('group_fileexcels_id') is-invalid @enderror  "
                                        id="group_fileexcels_id">
                                        <option value="">-- Pilih --</option>
                                        @foreach ($groupfileexcelsdata as $item)
                                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                    @error('group_fileexcels_id')
                                        <span id="group_fileexcels_id" class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="fileexcel_id">Campaign</label>
                                    <select name="fileexcel_id"
                                        class="form-control select2 @error('fileexcel_id') is-invalid @enderror  "
                                        id="fileexcel_id">
                                        <option value="">-- Pilih --</option>
                                        @foreach ($fileSelect as $item)
                                            <option value="{{ $item->id }}">{{ $item->kode }}</option>
                                        @endforeach
                                    </select>
                                    @error('fileexcel_id')
                                        <span id="produk_id" class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <!-- /.col -->
                            <div class="col-8">
                            </div>
                            <div class="col-4">
                                <a class="btn btn-primary btn-block" onclick="renderData()">Proses</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ $title }} Table </h3>
                                <div class="card-tools">
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center border-bottom mb-1 mt-3">
                                    <p class="text-success text-medium">
                                        TOTAL DATA
                                    </p>
                                    <p class="d-flex flex-column text-right">
                                        <span class="font-weight-bold totData ajaxLoading">
                                            <i class="ion ion-android-arrow-up text-success"></i> 0
                                        </span>
                                    </p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-bottom mb-1 mt-3">
                                    <p class="text-success text-medium">
                                        Sudah Distribusi
                                    </p>
                                    <p class="d-flex flex-column text-right">
                                        <span class="font-weight-bold totDistribusi ajaxLoading">
                                            <i class="ion ion-android-arrow-up text-success"></i> 0
                                        </span>
                                    </p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-bottom mb-1 mt-3">
                                    <p class="text-success text-medium">
                                        Belum Distribusi
                                    </p>
                                    <p class="d-flex flex-column text-right">
                                        <span class="font-weight-bold totNodistribusi ajaxLoading">
                                            <i class="ion ion-android-arrow-up text-success"></i> 0
                                        </span>
                                    </p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-bottom mb-1 mt-3">
                                    <p class="text-success text-medium">
                                        Sudah Ditelepon
                                    </p>
                                    <p class="d-flex flex-column text-right">
                                        <span class="font-weight-bold totCall ajaxLoading">
                                            <i class="ion ion-android-arrow-up text-success"></i> 0
                                        </span>
                                    </p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-bottom mb-1 mt-3">
                                    <p class="text-success text-medium">
                                        Belum Ditelepon
                                    </p>
                                    <p class="d-flex flex-column text-right">
                                        <span class="font-weight-bold totNocall ajaxLoading">
                                            <i class="ion ion-android-arrow-up text-success"></i> 0
                                        </span>
                                    </p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-bottom mb-1 mt-3">
                                    <p class="text-success text-medium">
                                        Reload
                                    </p>
                                    <p class="d-flex flex-column text-right">
                                        <span class="font-weight-bold totReload ajaxLoading">
                                            <i class="ion ion-android-arrow-up text-success"></i> 0
                                        </span>
                                    </p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-bottom mb-3 mt-5">
                                    <p class="text-danger text-medium">
                                        THREE
                                    </p>
                                    <p class="d-flex flex-column text-right">
                                        <span class="font-weight-bold totProvthree ajaxLoading">
                                            <i class="ion ion-android-arrow-up text-success"></i> 0
                                        </span>
                                    </p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-bottom mb-1 mt-3">
                                    <p class="text-danger text-medium">
                                        SIMPATI
                                    </p>
                                    <p class="d-flex flex-column text-right">
                                        <span class="font-weight-bold totProvsimpati ajaxLoading">
                                            <i class="ion ion-android-arrow-up text-success"></i> 0
                                        </span>
                                    </p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-bottom mb-1 mt-3">
                                    <p class="text-danger text-medium">
                                        INDOSAT
                                    </p>
                                    <p class="d-flex flex-column text-right">
                                        <span class="font-weight-bold totProvindosat ajaxLoading">
                                            <i class="ion ion-android-arrow-up text-success"></i> 0
                                        </span>
                                    </p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-bottom mb-1 mt-3">
                                    <p class="text-danger text-medium">
                                        XL
                                    </p>
                                    <p class="d-flex flex-column text-right">
                                        <span class="font-weight-bold totProvxl ajaxLoading">
                                            <i class="ion ion-android-arrow-up text-success"></i> 0
                                        </span>
                                    </p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-bottom mb-1 mt-3">
                                    <p class="text-danger text-medium">
                                        SMART
                                    </p>
                                    <p class="d-flex flex-column text-right">
                                        <span class="font-weight-bold totProvsmart ajaxLoading">
                                            <i class="ion ion-android-arrow-up text-success"></i> 0
                                        </span>
                                    </p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-bottom mb-1 mt-3">
                                    <p class="text-danger text-medium">
                                        TIDAK DITEMUKAN
                                    </p>
                                    <p class="d-flex flex-column text-right">
                                        <span class="font-weight-bold totNoprov ajaxLoading">
                                            <i class="ion ion-android-arrow-up text-success"></i> 0
                                        </span>
                                    </p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-bottom mb-1 mt-3">
                                    <p class="text-danger text-medium">
                                        NON SIMPATI
                                    </p>
                                    <p class="d-flex flex-column text-right">
                                        <span class="font-weight-bold totNosimpati ajaxLoading">
                                            <i class="ion ion-android-arrow-up text-success"></i> 0
                                        </span>
                                    </p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-bottom mb-1 mt-3">
                                    <p class="text-danger text-medium">
                                        ALL PROVIDER
                                    </p>
                                    <p class="d-flex flex-column text-right">
                                        <span class="font-weight-bold totAllprov ajaxLoading">
                                            <i class="ion ion-android-arrow-up text-success"></i> 0
                                        </span>
                                    </p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-bottom mb-1 mt-3">
                                    <p class="text-info text-medium">
                                        TOTAL
                                    </p>
                                    <p class="d-flex flex-column text-right">
                                        <span class="font-weight-bold totProv ajaxLoading">
                                            <i class="ion ion-android-arrow-up text-success"></i> 0
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            {{-- <div class="list-data card-footer">
                    </div> --}}
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ $title }} Table </h3>
                                <div class="card-tools">
                                    <div class="row">
                                        <div class="icheck-primary">
                                            <input type="checkbox" id="checkAll" />
                                            <label for="checkAll">Check ALL</label>
                                        </div>
                                        <a class="btn btn-primary btn-block mr-4" onclick="refreshTable()">Refresh</a>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <?php
                                $jenisParam = 0;
                                $parentstatusParam = 0;
                                ?>
                                @foreach ($statusSelect as $item)
                                    <?php
                                    if ($jenisParam != $item->jenis) {
                                        $jenisParam = $item->jenis;
                                        if ($item->jenis == '1') {
                                            echo '<hr><h5>TERHUBUNG</h5>';
                                        } else {
                                            $parentstatusParam = 0;
                                            echo '<h5>TIDAK TERHUBUNG</h5>';
                                        }
                                    } else {
                                        if ($parentstatusParam != $item->parentstatus_id) {
                                            $parentstatusParam = $item->parentstatus_id;
                                            echo '<hr><h5>TERHUBUNG NO CRITERIA</h5>';
                                        }
                                    }
                                    ?>
                                    <div class="form-group row">
                                        <label for="inputbox_{{ $item->id }}"
                                            class="col-sm-6 col-form-label">{{ $item->nama }}</label>
                                        <div class="col-sm-3">
                                            <input type="text" id="inputbox_{{ $item->id }}"
                                                name="inputbox_{{ $item->id }}"
                                                class="form-control @error('inputbox_{{ $item->id }}') is-invalid @enderror"
                                                value="{{ $data == '' ? old('inputbox_' . $item->id) : old('inputbox_' . $item->id, $data->teleponkembali) }}"readonly>
                                        </div>
                                        @error('inputbox_{{ $item->id }}')
                                            <span id="inputbox_{{ $item->id }}"
                                                class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        <div class="col-sm-3">
                                            {{-- <?php
                                            //$arrayYesno = ['YES', 'NO'];
                                            ?>
                                            <select name="yesno"
                                                class="selbox form-control select2 @error('yesno_{{ $item->id }}') is-invalid @enderror  "
                                                id="yesno_{{ $item->id }}">
                                                <option value="">-- Pilih --</option>
                                                @foreach ($arrayYesno as $item)
                                                    <option value="{{ $item }}">
                                                        {{ $item }}
                                                    </option>
                                                @endforeach
                                            </select> --}}
                                            <div class="icheck-primary">
                                                <input type="checkbox" class="selbox" id="yesno_{{ $item->id }}" />
                                                <label for="yesno_{{ $item->id }}">Yes / No</label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <!-- /.card-body -->
                            <div class="list-data card-footer">
                                <div class="row">
                                    <!-- /.col -->
                                    <div class="col-8">
                                    </div>
                                    <div class="col-4">
                                        <!--a class="btn btn-primary btn-block" onclick="proses()">Proses</a-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
    <script src="{{ asset('adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/inputmask/jquery.inputmask.min.js') }}"></script>

    <script type="text/javascript">
        $('.select2').select2();
        var arrayInputbox = [];
        var cekSelect = 0;
        $("#group_fileexcels_id").change(function() {
            if ($("#group_fileexcels_id").val() != '') {
                $("#fileexcel_id").prop("disabled", true);
            } else {
                $("#fileexcel_id").prop("disabled", false);
            }
        });

        $("#fileexcel_id").change(function() {
            if ($("#fileexcel_id").val() != '') {
                $("#group_fileexcels_id").prop("disabled", true);
            } else {
                $("#group_fileexcels_id").prop("disabled", false);
            }
        });

        function renderData() {
            cekSelect = 0;
            var div = $('<div/>')
                .addClass('loading')
                .text('Loading...');
            $('.ajaxLoading').html(div);
            $.ajax({
                type: 'POST',
                url: "/setting/reload/ajax",
                data: {
                    _token: '{{ csrf_token() }}',
                    fileexcel_id: $("#fileexcel_id").val(),
                    group_fileexcels_id: $("#group_fileexcels_id").val(),
                },
                dataType: "json",
                encode: true,
            }).done(function(data) {
                $('.totData').html(data.provider['0'].total_data);
                $('.totDistribusi').html(data.provider['0'].total_data1);
                $('.totNodistribusi').html(data.provider['0'].total_nodistribusi);
                $('.totCall').html(data.provider['0'].total_call);
                $('.totNocall').html(data.provider['0'].total_nocall);
                $('.totReload').html(data.provider['0'].total_reload);


                $('.totProvthree').html(data.provider['0'].total_three_reload + ' / ' + data.provider['0']
                    .total_three);
                $('.totProvsimpati').html(data.provider['0'].total_simpati_reload + ' / ' + data.provider['0']
                    .total_simpati);
                $('.totProvindosat').html(data.provider['0'].total_indosat_reload + ' / ' + data.provider['0']
                    .total_indosat);
                $('.totProvxl').html(data.provider['0'].total_xl_reload + ' / ' + data.provider['0']
                    .total_xl);
                $('.totProvaxis').html(data.provider['0'].total_axis_reload + ' / ' + data.provider['0']
                    .total_axis);
                $('.totProvsmart').html(data.provider['0'].total_smart_reload + ' / ' + data.provider['0']
                    .total_smart);
                $('.totNoprov').html(data.provider['0'].total_noprovider);
                $('.totNosimpati').html(data.provider['0'].total_nosimpati_reload + ' / ' + data.provider['0']
                    .total_nosimpati);
                $('.totAllprov').html(data.provider['0'].total_reload + ' / ' + data.provider['0']
                    .total_data);
                $('.totProv').html(data.provider['0'].total_data);
                var palangSelect = 1;
                console.log(data.status.length);
                $.each(data.status, function(index, obj) {
                    arrayInputbox = data.status;
                    $('#inputbox_' + obj.id).val(obj.total_data);
                    if (obj.status == '0') {
                        //var statusyesno = 'NO';
                        $('#yesno_' + obj.id).prop('checked', false);
                    } else {
                        //var statusyesno = 'YES';
                        $('#yesno_' + obj.id).prop('checked', true);
                    }
                    //$('#yesno_' + obj.id).val(statusyesno).change();
                    if (palangSelect == data.status.length) {
                        cekSelect = 1;
                        console.log(cekSelect);
                    }
                    console.log('palangselect = ' + palangSelect);
                    palangSelect++;
                });
            });
        }
        $("#checkAll").click(function() {
            $('input:checkbox.selbox').not(this).prop('checked', this.checked).change();
        });
        $(".selbox").change(function(event) {
            if (cekSelect != 0) {
                if ($("#fileexcel_id").val() != '' || $("#group_fileexcels_id").val() != '') {
                    var id = (event.target.id);
                    var statuscall_id = id.replace('yesno_', '');
                    if (this.checked) {
                        var inp_box = "YES";
                    } else {
                        var inp_box = "NO";
                    }
                    $.ajax({
                        type: 'PUT',
                        url: "/setting/reload/save",
                        data: {
                            _token: '{{ csrf_token() }}',
                            fileexcel_id: $("#fileexcel_id").val(),
                            group_fileexcels_id: $("#group_fileexcels_id").val(),
                            inputbox: inp_box,
                            statuscall_id: statuscall_id,
                        },
                        dataType: "json",
                        encode: true,
                    }).done(function(data) {
                        if (data == 'done') {
                            console.log('okelah');
                        }
                    });
                }
            }
        });

        function refreshTable() {
            renderData();
        }

        function saveData() {
            console.log(this.id);
            console.log(this.value);
            // $.ajax({
            //     type: 'POST',
            //     url: "/setting/reload/ajax",
            //     data: {
            //         _token: '{{ csrf_token() }}',
            //         fileexcel_id: $("#fileexcel_id").val(),
            //         statuscall_id: $("#fileexcel_id").val(),
            //     },
            //     dataType: "json",
            //     encode: true,
            // }).done(function(data) {

            // });
        }
    </script>
@endsection
