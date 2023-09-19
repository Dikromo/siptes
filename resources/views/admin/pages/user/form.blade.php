@extends('admin.layouts.main')

@section('container')
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2/css/select2.min.css') }}">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="float-sm-right">
                    <a class="btn btn-warning btn-sm" href="/user">
                        <i class="fas fa-arrow-left">
                        </i>
                        Back
                    </a>
                </div>
            </div>
            <br>
            <br>
            <div class="col-12">
                <form action="/user{{ $data != '' ? '/' . $data->username : '' }}" id="formUser" method="POST">
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
                            <div class="form-group">
                                <label for="nama">Nama</label>
                                <input type="text" id="name" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ $data == '' ? old('name') : old('name', $data->name) }}">
                                @error('name')
                                    <span id="name" class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="email">email</label>
                                <input type="email" id="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ $data == '' ? old('email') : old('email', $data->email) }}">
                                @error('email')
                                    <span id="email" class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="username">username</label>
                                <input type="text" id="username" name="username"
                                    class="form-control @error('username') is-invalid @enderror"
                                    value="{{ $data == '' ? old('username') : old('username', $data->username) }}">
                                @error('username')
                                    <span id="username" class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            @if (auth()->user()->roleuser_id != '2')
                                <div class="form-group">
                                    <label for="nickname">Kode</label>
                                    <input type="text" id="nickname" name="nickname"
                                        class="form-control @error('nickname') is-invalid @enderror"
                                        value="{{ $data == '' ? old('nickname') : old('nickname', $data->nickname) }}">
                                    @error('nickname')
                                        <span id="nickname" class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                            <div class="form-group">
                                <label for="password">password</label>
                                <input type="password" id="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror">
                                @error('password')
                                    <span id="password" class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="refferal">Refferal</label>
                                        <input type="text" id="refferal" name="refferal"
                                            class="form-control @error('refferal') is-invalid @enderror"
                                            value="{{ $data == '' ? old('refferal') : old('refferal', $data->refferal) }}">
                                        @error('refferal')
                                            <span id="refferal" class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="salescode">Sales Code</label>
                                        <input type="text" id="salescode" name="salescode"
                                            class="form-control @error('salescode') is-invalid @enderror"
                                            value="{{ $data == '' ? old('salescode') : old('salescode', $data->salescode) }}">
                                        @error('salescode')
                                            <span id="salescode" class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="join_date">Join Date</label>
                                        <input type="datetime-local" step="1" id="join_date" name="join_date"
                                            class="form-control @error('join_date') is-invalid @enderror"
                                            value="{{ $data == '' ? old('join_date') : old('join_date', $data->join_date) }}">
                                        @error('join_date')
                                            <span id="join_date" class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="resign_date">Resign Date</label>
                                        <input type="datetime-local" step="1" id="resign_date" name="resign_date"
                                            class="form-control @error('resign_date') is-invalid @enderror"
                                            value="{{ $data == '' ? old('resign_date') : old('resign_date', $data->resign_date) }}">
                                        @error('resign_date')
                                            <span id="resign_date" class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <?php
                            $arrayStatus = ['Active', 'Not Active'];
                            ?>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status"
                                    class="form-control select2 @error('status') is-invalid @enderror  " id="status">
                                    <option value="">-- Pilih --</option>
                                    @foreach ($arrayStatus as $item)
                                        @if ($data != '')
                                            <?php
                                            switch ($data->status) {
                                                case '1':
                                                    $statusUser = 'Active';
                                                    break;
                                                default:
                                                    $statusUser = 'Not Active';
                                                    break;
                                            }
                                            ?>
                                            @if (old('status') == $item || $statusUser == $item)
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
                                @error('status')
                                    <span id="status" class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            @if (auth()->user()->cabang_id == '4' || auth()->user()->roleuser_id == '1')
                                <?php
                                $arrayDistri = ['Yes', 'No'];
                                ?>
                                <div class="form-group">
                                    <label for="flagdistri">Distribusi</label>
                                    <select name="flagdistri"
                                        class="form-control select2 @error('flagdistri') is-invalid @enderror  "
                                        id="flagdistri">
                                        <option value="">-- Pilih --</option>
                                        @foreach ($arrayDistri as $item)
                                            @if ($data != '')
                                                <?php
                                                switch ($data->flagdistri) {
                                                    case '1':
                                                        $flagdistriUser = 'Yes';
                                                        break;
                                                    default:
                                                        $flagdistriUser = 'No';
                                                        break;
                                                }
                                                ?>
                                                @if (old('flagdistri') == $item || $flagdistriUser == $item)
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
                                    @error('flagdistri')
                                        <span id="flagdistri" class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                            {{-- hidden input --}}
                            @if (auth()->user()->roleuser_id == '2')
                                <input type="hidden" id="hroleuser_id" name="roleuser_id"
                                    class="form-control @error('roleuser_id') is-invalid @enderror"
                                    value="{{ $data == '' ? old('roleuser_id', '3') : old('roleuser_id', $data->roleuser_id) }}">
                                <input type="hidden" id="hparentuser_id" name="parentuser_id"
                                    class="form-control @error('parentuser_id') is-invalid @enderror"
                                    value="{{ $data == '' ? old('parentuser_id', auth()->user()->id) : old('parentuser_id', $data->parentuser_id) }}">
                                <input type="hidden" id="hsm_id" name="sm_id"
                                    class="form-control @error('sm_id') is-invalid @enderror"
                                    value="{{ $data == '' ? old('sm_id', auth()->user()->sm_id) : old('sm_id', $data->sm_id) }}">
                                <input type="hidden" id="hum_id" name="um_id"
                                    class="form-control @error('um_id') is-invalid @enderror"
                                    value="{{ $data == '' ? old('um_id', auth()->user()->um_id) : old('um_id', $data->um_id) }}">
                                <input type="hidden" id="hproduk_id" name="produk_id"
                                    class="form-control @error('produk_id') is-invalid @enderror"
                                    value="{{ $data == '' ? old('produk_id', auth()->user()->produk_id) : old('produk_id', $data->produk_id) }}">
                                <input type="hidden" id="hcabang_id" name="cabang_id"
                                    class="form-control @error('cabang_id') is-invalid @enderror"
                                    value="{{ $data == '' ? old('cabang_id', auth()->user()->cabang_id) : old('cabang_id', $data->cabang_id) }}">
                            @endif
                            @if (auth()->user()->roleuser_id == '4' || auth()->user()->roleuser_id == '5' || auth()->user()->roleuser_id == '6')
                                <input type="hidden" id="hparentuser_id" name="parentuser_id"
                                    class="form-control @error('parentuser_id') is-invalid @enderror"
                                    value="{{ $data == '' ? old('parentuser_id') : old('parentuser_id', $data->parentuser_id) }}">
                                <input type="hidden" id="hcabang_id" name="cabang_id"
                                    class="form-control @error('cabang_id') is-invalid @enderror"
                                    value="{{ $data == '' ? old('cabang_id', auth()->user()->cabang_id) : old('cabang_id', $data->cabang_id) }}">
                            @endif
                            {{-- End hidden input --}}
                            @if (auth()->user()->roleuser_id == '1' ||
                                    auth()->user()->roleuser_id == '4' ||
                                    auth()->user()->roleuser_id == '5' ||
                                    auth()->user()->roleuser_id == '6')
                                <input type="hidden" id="hsm_id" name="sm_id"
                                    class="form-control @error('sm_id') is-invalid @enderror"
                                    value="{{ $data == '' ? old('sm_id') : old('sm_id', $data->sm_id) }}">
                                <input type="hidden" id="hum_id" name="um_id"
                                    class="form-control @error('um_id') is-invalid @enderror"
                                    value="{{ $data == '' ? old('um_id') : old('um_id', $data->um_id) }}">
                                <input type="hidden" id="hproduk_id" name="produk_id"
                                    class="form-control @error('produk_id') is-invalid @enderror"
                                    value="{{ $data == '' ? old('produk_id') : old('produk_id', $data->produk_id) }}">
                                <div class="form-group">
                                    <label for="roleuser_id">Role User</label>
                                    <select name="roleuser_id"
                                        class="form-control select2 @error('roleuser_id') is-invalid @enderror  "
                                        id="roleuser_id" required
                                        {{ auth()->user()->roleuser_id == '2' ? 'disabled' : '' }}>
                                        <option value="">-- Pilih --</option>

                                        @foreach ($roleSelect as $item)
                                            @if ($data != '')
                                                @if (old('roleuser_id', $data->roleuser_id) == $item->id)
                                                    <option value="{{ $item->id }}" selected>{{ $item->nama }}
                                                    </option>
                                                @else
                                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                                @endif
                                            @else
                                                @if (old('roleuser_id') == $item->id)
                                                    <option value="{{ $item->id }}" selected>{{ $item->nama }}
                                                    </option>
                                                @else
                                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                                @endif
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('roleuser_id')
                                        <span id="roleuser_id" class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="parentuser_id">Supervisor</label>
                                    <select name="parentuser_id"
                                        class="form-control select2 @error('parentuser_id') is-invalid @enderror  "
                                        id="parentuser_id"
                                        {{ ($data == '' ? '' : $data->roleuser_id != '3') ? 'disabled' : '' }}>
                                        <option value="">-- Pilih --</option>
                                        @foreach ($spvSelect as $item)
                                            @if ($data != '')
                                                @if (old('parentuser_id') == $item->id || $data->parentuser_id == $item->id)
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
                                    @error('parentuser_id')
                                        <span id="parentuser_id" class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="sm_id">Sales Manager</label>
                                    <select name="sm_id"
                                        class="form-control select2 @error('sm_id') is-invalid @enderror  "
                                        id="sm_id"
                                        {{ ($data == '' ? '' : $data->roleuser_id != '2') ? 'disabled' : '' }}>
                                        <option value="">-- Pilih --</option>
                                        @foreach ($smSelect as $item)
                                            @if ($data != '')
                                                @if (old('sm_id') == $item->id || $data->sm_id == $item->id)
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
                                    @error('sm_id')
                                        <span id="sm_id" class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="um_id">Unit Manager</label>
                                    <select name="um_id"
                                        class="form-control select2 @error('um_id') is-invalid @enderror  "
                                        id="um_id"
                                        {{ ($data == '' ? '' : $data->roleuser_id != '5') ? 'disabled' : '' }}>
                                        <option value="">-- Pilih --</option>
                                        @foreach ($umSelect as $item)
                                            @if ($data != '')
                                                @if (old('um_id') == $item->id || $data->um_id == $item->id)
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
                                    @error('um_id')
                                        <span id="um_id" class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="produk_id">Produk</label>
                                    <select name="produk_id"
                                        class="form-control select2 @error('produk_id') is-invalid @enderror  "
                                        id="produk_id"
                                        {{ ($data == '' ? '' : $data->roleuser_id != '2') ? 'disabled' : '' }}>
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
                                @if (auth()->user()->roleuser_id == '1')
                                    <div class="form-group">
                                        <label for="cabang_id">Lokasi</label>
                                        <select name="cabang_id"
                                            class="form-control select2 @error('cabang_id') is-invalid @enderror  "
                                            id="cabang_id">
                                            <option value="">-- Pilih --</option>
                                            @foreach ($cabangSelect as $item)
                                                @if ($data != '')
                                                    @if (old('cabang_id') == $item->id || $data->cabang_id == $item->id)
                                                        <option value="{{ $item->id }}" selected>
                                                            {{ $item->nama }}
                                                        </option>
                                                    @else
                                                        <option value="{{ $item->id }}">

                                                            {{ $item->nama }}</option>
                                                    @endif
                                                @else
                                                    <option value="{{ $item->id }}">
                                                        {{ $item->nama }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                        @error('cabang_id')
                                            <span id="cabang_id" class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif
                            @endif
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
            </div>
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
@endsection
@section('addScript')
    <script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $('.select2').select2();
        var user_id = '{{ auth()->user()->id }}';
        var roleuser_id = '{{ auth()->user()->roleuser_id }}';

        function check_form(param, param1) {
            if (param1 != 'awal') {
                $('#parentuser_id').val('').change();
                $('#produk_id').val('').change();
            }
            console.log(param + 'aaa');
            if (param == '3') {
                console.log('aaa');
                $('#parentuser_id').prop('disabled', false);
                $('#sm_id').prop('disabled', true);
                $('#um_id').prop('disabled', true);
                $('#produk_id').prop('disabled', true);
            } else if (param == '2') {
                $('#hparentuser_id').val('');
                $('#hproduk_id').val('');
                $('#parentuser_id').prop('disabled', true);
                $('#um_id').prop('disabled', true);

                $('#sm_id').prop('disabled', true);
                if (roleuser_id != '5') {
                    $('#sm_id').prop('disabled', false);
                } else {

                    $('#hsm_id').val(user_id);
                    $('#sm_id').val(user_id).change();
                    getUM(user_id);
                    $('#sm_id').prop('disabled', true);
                }
                $('#produk_id').prop('disabled', false);
            } else if (param == '5') {
                $('#hparentuser_id').val('');
                $('#hproduk_id').val('');
                $('#parentuser_id').prop('disabled', true);
                $('#um_id').prop('disabled', false);
                $('#produk_id').prop('disabled', false);
            } else {
                $('#hparentuser_id').val('');
                $('#hproduk_id').val('');
                $('#parentuser_id').prop('disabled', true);
                $('#um_id').prop('disabled', true);
                $('#sm_id').prop('disabled', true);
                $('#produk_id').prop('disabled', true);
            }
        }
        if (roleuser_id != '2') {
            if ($('#roleuser_id').val() != '') {
                check_form($('#roleuser_id').val(), 'awal');
            }
        }
        $('#roleuser_id').on('select2:select', function(e) {
            var data = e.params.data.id;

            check_form(data, '');
        });

        $('#parentuser_id').on('select2:select', function(e) {
            var data = e.params.data.id;
            $('#hparentuser_id').val(data);
            getProduk(data);
            getSM(data);
            getUM(data);
        });

        $('#sm_id').on('select2:select', function(e) {
            var data = e.params.data.id;
            $('#hsm_id').val(data);
            getProduk(data);
            getUM(data);
        });
        $('#formUser').submit(function() {
            $('#roleuser_id').prop('disabled', false);
            $('#sm_id').prop('disabled', false);
            $('#um_id').prop('disabled', false);
            $('#produk_id').prop('disabled', false);
            $('#modal-overlay').modal({
                backdrop: 'static',
                keyboard: false
            });
            return true;
        });

        function getSM(param) {
            $.ajax({
                type: 'POST',
                url: "/cek/sm",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: param,
                },
                dataType: "json",
                encode: true,
            }).done(function(data) {
                $('#hsm_id').val(data);
                $('#sm_id').val(data).change();
            });
        }

        function getUM(param) {
            $.ajax({
                type: 'POST',
                url: "/cek/um",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: param,
                },
                dataType: "json",
                encode: true,
            }).done(function(data) {
                $('#hum_id').val(data);
                $('#um_id').val(data).change();
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
                $('#hproduk_id').val(data);
                $('#produk_id').val(data).change();
            });
        }
    </script>
@endsection
