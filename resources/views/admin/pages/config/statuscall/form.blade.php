@extends('admin.layouts.main')

@section('container')
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2/css/select2.min.css') }}">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="float-sm-right">
                    <a class="btn btn-warning btn-sm" href="/statuscall">
                        <i class="fas fa-arrow-left">
                        </i>
                        Back
                    </a>
                </div>
            </div>
            <br>
            <br>
            <div class="col-12">
                <form action="/statuscall{{ $data != '' ? '/' . encrypt($data->id) : '' }}" id="formStatus" method="POST">
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
                                <input type="text" id="nama" name="nama"
                                    class="form-control @error('nama') is-invalid @enderror"
                                    value="{{ $data == '' ? old('nama') : old('nama', $data->nama) }}">
                                @error('nama')
                                    <span id="nama" class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="cabang_id">Site</label>
                                <select name="cabang_id"
                                    class="form-control select2 @error('cabang_id') is-invalid @enderror  " id="cabang_id"
                                    required>
                                    <option value="">-- Pilih --</option>
                                    @foreach ($cabangSelect as $item)
                                        @if ($data != '')
                                            @if (old('cabang_id') == $item->id || $data->cabang_id == $item->id)
                                                <option value="{{ $item->id }}" selected>
                                                    {{ $item->nama }}
                                                </option>
                                            @else
                                                <option value="{{ $item->id }}">{{ $item->nama }}
                                                </option>
                                            @endif
                                        @else
                                            <option value="{{ $item->id }}">{{ $item->nama }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('cabang_id')
                                    <span id="cabang_id" class="error invalid-feedback">{{ $message }}</span>
                                @enderror
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
        $('.select2').select2()
        $('#formStatus').submit(function() {
            $('#roleuser_id').prop('disabled', false);
            $('#modal-overlay').modal({
                backdrop: 'static',
                keyboard: false
            });
            return true;
        });
    </script>
@endsection
