@extends('admin.layouts.main')

@section('container')
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2/css/select2.min.css') }}">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="float-sm-right">
                    <a class="btn btn-warning btn-sm" href="/campaign/group">
                        <i class="fas fa-arrow-left">
                        </i>
                        Backk
                    </a>
                </div>
            </div>
            <br>
            <br>
            <div class="col-12">
                <form action="/campaign/group{{ $data != '' ? '/' . encrypt($data->id) : '' }}" id="formJmosip"
                    enctype="multipart/form-data" method="POST">
                    @if ($data != '')
                        @method('put')
                    @endif
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Form {{ $data != '' ? 'Edit ' . $title : 'Tambah ' . $title }}</h3>
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
                        <div class="card-body ">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="nama">Nama Group</label>
                                        <input type="text" id="nama" name="nama"
                                            class="form-control @error('nama') is-invalid @enderror"
                                            value="{{ $data == '' ? old('nama') : old('nama', $data->nama) }}" required>
                                        @error('nama')
                                            <span id="nama" class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <div class="row">
                                <!-- /.col -->
                                <div class="col-8">
                                </div>
                                <div class="col-4">
                                    <button type="submit" class="btn btn-primary btn-block">Simpan</button>
                                </div>
                                <!-- /.col -->
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </form>

                @if ($data != '')
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ $title }} List Table </h3><br>
                            <a class="btn btn-primary btn-sm" onclick="modalAdd('')">
                                <i class="fas fa-user-plus">
                                </i>
                                Add Group List
                            </a>
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
                                        <th>Campaign Kode</th>
                                        <th></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
    <div class="modal fade" id="modalAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Group List Modal</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="campaign">campaign</label>
                        <select name="campaign" class="form-control select2 @error('campaign') is-invalid @enderror  "
                            id="campaign" style="width: 100%">
                            <option value="">-- Pilih --</option>
                        </select>
                        @error('campaign')
                            <span id="campaign" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveGrouplist('','add');">Save changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('addScript')
    <script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <script>
        var groupid = "{{ $data != '' ? encrypt($data->id) : '' }}";
        // var linkid = "{{ $data != '' ? '/' . encrypt($data->id) : '' }}";
        var linkid = "{{ $data != '' ? $data->id : '' }}";
        var tipe = "POST";
        $('.select2').select2();
        groupfileexcel_list();
        $(function() {
            bsCustomFileInput.init();
        });
        $('#formJmosip').submit(function() {
            $('#modal-overlay').modal({
                backdrop: 'static',
                keyboard: false
            });
            return true;
        });

        function modalAdd(param) {
            $('#campaign').val('').trigger('change');
            $("#campaign").select2({
                minimumInputLength: 2,
                allowClear: true,
                placeholder: 'pilih nama campaign',
                ajax: {
                    url: "/campaign/group/list",
                    dataType: 'json',
                    type: "GET",
                    quietMillis: 50,
                    data: function(term) {
                        return {
                            id: term.term
                        };
                    },
                    processResults: function(data, page) {
                        return {
                            results: data
                        };
                    }
                }
            });
            // $.ajax({
            //     type: 'POST',
            //     url: "/mutasilist/detail",
            //     data: {
            //         _token: '{{ csrf_token() }}',
            //         id: param,
            //     },
            //     dataType: "json",
            //     encode: true,
            // }).done(function(data) {
            //     console.log(data);
            // });
            $('#modalAdd').modal({
                backdrop: 'static',
            });
        }

        function toastAlert(param) {
            $(document).Toasts('create', {
                class: 'bg-success',
                title: 'Berhasil',
                body: param
            })
        }

        function groupfileexcel_list() {
            $('#dataTables').DataTable({
                processing: true,
                serverside: true,
                autoWidth: false,
                bDestroy: true,
                searching: false,
                ajax: {
                    type: 'POST',
                    url: '/campaign/ajaxgroup/list',
                    data: {
                        _token: '{{ csrf_token() }}',
                        groupid: groupid,
                    }
                },
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                }, {
                    data: 'kode',
                    name: 'kode'
                }, {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                }],
                columnDefs: [{
                    targets: 2,
                    className: "text-center",
                }]
            })
        }

        $(document).on('select2:open', e => {
            const select2 = $(e.target).data('select2');
            if (!select2.options.get('multiple')) {
                select2.dropdown.$search.get(0).focus();
            }
        });

        function saveGrouplist(id, param) {
            fileexcel_id = id == '' ? $('#campaign').val() : id;
            if (fileexcel_id != '') {
                $.ajax({
                    type: tipe,
                    url: "/campaign/group/list",
                    data: {
                        _token: '{{ csrf_token() }}',
                        tipe: param,
                        group_id: groupid,
                        fileexcel_id: fileexcel_id,
                    },
                    dataType: "json",
                    encode: true,
                }).done(function(data) {
                    $('#modalAdd').modal('hide');
                    console.log(data);
                    toastAlert(data);
                    groupfileexcel_list();
                });
            } else {
                alert('Tidak dapat melakukan proses!');
            }
        }
    </script>
@endsection
