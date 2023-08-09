@extends('admin.layouts.main')

@section('container')
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">

    <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2/css/select2.min.css') }}">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @if (session('msg'))
                    <div class="alert alert-info alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h5><i class="icon fas fa-info"></i> Alert!</h5>
                        {!! session('msg') !!}
                    </div>
                @endif
            </div>
        </div>
        <div class="row">
            {{-- <div class="col-12">
                <div class="float-sm-right">
                    <a class="btn btn-warning btn-sm" href="/user">
                        <i class="fas fa-arrow-left">
                        </i>
                        Back
                    </a>
                </div>
            </div>
            <br>
            <br> --}}
            {{-- Table From --}}
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ $title }} Dari</h3>
                        <div class="card-tools">
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body ">
                        <div class="card-body table-responsive">
                            <table class="table table-head-fixed text-nowrap" id="dataTables1">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                        <th>Nama</th>
                                        <th>Telp</th>
                                        <th>Provider</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                    </div>
                </div>
            </div>
            {{-- Form Filter --}}
            <div class="col-lg-2">
                <div class="card card-primary card-outline">
                    <form action="/customer/distribusi/proses" id="formDistribusi" method="POST">
                        @csrf
                        <div class="card-header">
                            <h3 class="card-title">Aksi {{ $title }}</h3>
                            <div class="card-tools">
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body ">
                            @if (auth()->user()->roleuser_id != '1' && auth()->user()->roleuser_id != '2')
                                <input type="hidden" id="produk_id" name="produk_id"
                                    class="form-control @error('produk_id') is-invalid @enderror"
                                    value="{{ $data == '' ? old('produk_id', auth()->user()->produk_id) : old('produk_id', $data->produk_id) }}">
                            @endif
                            @if (auth()->user()->roleuser_id == '1')
                                <div class="form-group">
                                    <label for="produk_id">Produk</label>
                                    <select name="produk_id"
                                        class="form-control select2 @error('produk_id') is-invalid @enderror  "
                                        id="produk_id">
                                        <option value="">-- Pilih --</option>
                                        @foreach ($produkSelect as $item)
                                            @if ($data != '')
                                                @if (old('produk_id') == $item->id || $data->produk_id == $item->id)
                                                    <option value="{{ $item->id }}" selected>
                                                        {{ $item->tipe . ' - ' . $item->nama }}
                                                    </option>
                                                @else
                                                    <option value="{{ $item->id }}">
                                                        {{ $item->tipe . ' - ' . $item->nama }}</option>
                                                @endif
                                            @else
                                                <option value="{{ $item->id }}">
                                                    {{ $item->tipe . ' - ' . $item->nama }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('produk_id')
                                        <span id="produk_id" class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                            <?php
                            if (count($fileExceldata) == '0') {
                                $arrayTipe = ['DISTRIBUSI', 'TARIK DATA'];
                            } else {
                                $arrayTipe = ['DISTRIBUSI', 'TARIK DATA', 'RELOAD'];
                            }
                            ?>
                            <div class="form-group">
                                <label for="tipe">Tipe</label>
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
                                <label for="kode">Kode</label>
                                <select name="fileexcel_id"
                                    class="form-control select2 @error('fileexcel_id') is-invalid @enderror  "
                                    id="fileexcel_id">
                                    <option value="">-- Pilih --</option>

                                    @if (session('oldData') != '')
                                        @if (session('oldData')['fileexcel_id'] == 'today')
                                            <option value="today" selected>Hari Ini</option>
                                        @else
                                            <option value="today">Hari Ini</option>
                                        @endif
                                    @else
                                        <option value="today">Hari Ini</option>
                                    @endif

                                    @foreach ($fileExceldata as $item)
                                        @if (session('oldData') != '')
                                            @if (session('oldData')['fileexcel_id'] == $item->id)
                                                <option value="{{ $item->id }}" selected>{{ $item->kode }}</option>
                                            @else
                                                <option value="{{ $item->id }}">{{ $item->kode }}</option>
                                            @endif
                                        @else
                                            <option value="{{ $item->id }}">{{ $item->kode }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('fileexcel_id')
                                    <span id="fileexcel_id" class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <?php
                            if (count($fileExceldata) == '0') {
                                $arrayProvider = ['SIMPATI', 'INDOSAT', 'XL', 'AXIS', 'THREE', 'SMART'];
                            } else {
                                $arrayProvider = ['SIMPATI', 'NON-SIMPATI', 'ALL-PROVIDER', 'INDOSAT', 'XL', 'AXIS', 'THREE', 'SMART'];
                            }
                            ?>
                            <div class="form-group">
                                <label for="provider">Provider</label>
                                <select name="provider"
                                    class="form-control select2 @error('provider') is-invalid @enderror  " id="provider">
                                    <option value="">-- Pilih --</option>
                                    @foreach ($arrayProvider as $item)
                                        @if (session('oldData') != '')
                                            @if (session('oldData')['provider'] == $item)
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
                                @error('provider')
                                    <span id="provider" class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="user_id">Sales</label>
                                <select name="user_id" class="form-control select2 @error('user_id') is-invalid @enderror  "
                                    id="user_id" required>
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
                            </div>
                            <div class="form-group">
                                <label for="total">Total</label>
                                <input type="number" id="total" name="total"
                                    class="form-control @error('total') is-invalid @enderror" value="{{ old('total') }}"
                                    required>
                                @error('total')
                                    <span id="total" class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <div class="row">
                                <!-- /.col -->
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-block">Simpan</button>
                                </div>
                                <!-- /.col -->
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            {{-- Table To --}}
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ $title }} Ke</h3>
                        <div class="card-tools">
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body ">
                        <div class="card-body table-responsive">
                            <table class="table table-head-fixed text-nowrap" id="dataTables2">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                        <th>Nama</th>
                                        <th>Telp</th>
                                        <th>Provider</th>
                                        @if (count($fileExceldata) != '0')
                                            <th>Kode</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
@endsection
@section('addScript')
    <script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/select2/js/select2.full.min.js') }}"></script>

    {{-- <script src="{{ asset('adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script> --}}
    <script>
        var totalFileexcel = '{{ count($fileExceldata) }}';
        var fromTabel = 0;
        var toTabel = 0;
        var rolecek = '{{ auth()->user()->roleuser_id }}';
        $('.select2').select2()
        $('#formDistribusi').submit(function() {
            if ($('#tipe').val() == 'DISTRIBUSI' || $('#tipe').val() == 'RELOAD') {
                if (fromTabel != 0 && $('#total').val() != '0' && $('#total').val() <= fromTabel) {
                    $('#modal-overlay').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    return true;
                }
            }

            if ($('#tipe').val() == 'TARIK DATA') {
                if (toTabel != 0 && $('#total').val() != '0' && $('#total').val() <= toTabel) {
                    $('#modal-overlay').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    return true;
                }
            }
            alert('Tidak dapat melakukan proses!');
            return false;
        });
        $(document).ready(function() {
            $(document).on('select2:open', () => {
                document.querySelector('.select2-search__field').focus();
            });
            setTimeout(() => {
                if ($("#produk_id").val() != '' && $("#fileexcel_id").val() != '' && $("#provider").val() !=
                    '' && $("#tipe").val() != '') {
                    dataTablesfrom();
                }
            }, 2000);
            $("#produk_id").change(function() {
                if ($("#produk_id").val() != '' && $("#fileexcel_id").val() != '' && $("#provider").val() !=
                    '' && $("#tipe").val() != '') {
                    dataTablesfrom();
                }
            });
            $("#fileexcel_id").change(function() {
                if ($("#produk_id").val() != '' && $("#fileexcel_id").val() != '' && $("#provider").val() !=
                    '' && $("#tipe").val() != '') {
                    dataTablesfrom();
                }
            });
            $("#provider").change(function() {
                if ($("#produk_id").val() != '' && $("#fileexcel_id").val() != '' && $("#provider").val() !=
                    '' && $("#tipe").val() != '') {
                    dataTablesfrom();
                }
            });

            $("#tipe").change(function() {
                if ($("#produk_id").val() != '' && $("#fileexcel_id").val() != '' && $("#provider").val() !=
                    '' && $("#tipe").val() != '') {
                    dataTablesfrom();
                }
            });
            $("#user_id").change(function() {
                if (rolecek != '2') {
                    getProduk($("#user_id").val());
                }
                dataTablesto(this.value);
            });
            $('#dataTables1').DataTable({
                processing: true,
                serverside: true,
                autoWidth: false,
                bDestroy: true,
            });
            $('#dataTables2').DataTable({
                processing: true,
                serverside: true,
                autoWidth: false,
                bDestroy: true,
                searching: false,
            });
        })

        function dataTablesfrom(select) {
            var hari = "<?php echo date('Y-m-d'); ?>";
            $('#dataTables1').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                bDestroy: true,
                initComplete: function(settings, json) {
                    fromTabel = this.api().page.info().recordsTotal;

                    console.log(fromTabel);
                },
                ajax: {
                    type: 'POST',
                    url: '/customer/ajax/from',
                    data: {
                        _token: '{{ csrf_token() }}',
                        produk_id: $("#produk_id").val(),
                        fileexcel_id: $("#fileexcel_id").val(),
                        provider: $("#provider").val(),
                        tipe: $("#tipe").val(),
                    }
                },
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'nama',
                    name: 'customers.nama'
                }, {
                    data: 'no_telp',
                    name: 'customers.no_telp'
                }, {
                    data: 'provider',
                    name: 'customers.provider'
                }]
            });
            // Untuk export datatables
            //     dom: 'Bfrtip',
            //     buttons: [{
            //         extend: 'excel',
            //         filename: 'export_' + hari
            //     }, ]
            // }).buttons().container().appendTo('#dataTables1_wrapper .col-md-6:eq(0)');
        }
        if (totalFileexcel != 0) {
            $paramColumnto = [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            }, {
                data: 'nama',
                name: 'nama'
            }, {
                data: 'no_telp',
                name: 'no_telp'
            }, {
                data: 'provider',
                name: 'provider'
            }, {
                data: 'kode',
                name: 'kode'
            }, ];
        } else {
            $paramColumnto = [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            }, {
                data: 'nama',
                name: 'nama'
            }, {
                data: 'no_telp',
                name: 'no_telp'
            }, {
                data: 'provider',
                name: 'provider'
            }, ];
        }

        function dataTablesto(select) {
            $('#dataTables2').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                bDestroy: true,
                searching: false,
                initComplete: function(settings, json) {
                    toTabel = this.api().page.info().recordsTotal;
                },
                ajax: {
                    type: 'POST',
                    url: '/customer/ajax/to',
                    data: {
                        _token: '{{ csrf_token() }}',
                        user_id: select,
                    }
                },
                columns: $paramColumnto
            });
        }

        function getProduk(param) {
            $.ajax({
                type: 'POST',
                url: "/cek/produkspv",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: param,
                },
                dataType: "json",
                encode: true,
            }).done(function(data) {
                $('#produk_id').val(data);
                $('#produk_id').val(data).change();
                // if (rolecek != '1' && rolecek != '2') {
                //     if ($("#produk_id").val() != '' && $("#fileexcel_id").val() != '' && $("#provider")
                //         .val()) {
                //         dataTablesfrom();
                //     }
                // }
            });
        }
    </script>
@endsection
