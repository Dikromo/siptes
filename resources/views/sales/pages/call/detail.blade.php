@extends('admin.layouts.main')

@section('container')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline">
                    <div class="card-body">
                        <div class="col-12  text-center my-3">
                            <div class="text-center">
                                <img class="img-fluid" src="{{ asset('assets/img/logo.png') }}" alt="Photo">
                            </div>
                            <br>
                            <a href="tel:{{ $data->customer->no_telp }}" class="btn btn-info btn-lg mx-2"> <i
                                    class="fa fa-mobile-alt"></i>
                                Call</a>
                            <a href="https://wa.me/+62{!! substr(strip_tags($data->customer->no_telp), 1, 20) !!}" class="btn btn-success btn-lg mx-2"> <i
                                    class="fab fa-whatsapp" aria-hidden="true"></i>
                                Whatsapp</a>
                            {{-- <button type="button" class="btn btn-primary btn-lg mx-2"> <i
                                    class="nav-icon fas fa-headset"></i>
                                Call</button> --}}
                        </div>
                        <div class="row">
                            <div class="col-12 mr-2 text-right">
                                <div class="float-sm-right">
                                    <a class="btn btn-warning" href="/call">
                                        <i class="fas fa-arrow-left">
                                        </i>
                                        Back
                                    </a>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="col-12">
                            <form action="/call/detail/{{ encrypt($data->id) }}" method="POST">
                                @if ($data != '')
                                    @method('put')
                                @endif
                                @csrf
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Form {{ $title }}</h3>
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
                                            <input type="text" id="name" name="nama"
                                                class="form-control @error('name') is-invalid @enderror"
                                                value="{{ $data == '' ? old('name') : old('name', $data->customer->nama) }}"
                                                disabled>
                                            @error('name')
                                                <span id="name" class="error invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="no_telp">No Telp</label>
                                            <input type="text" id="no_telp" name="no_telp"
                                                class="form-control @error('no_telp') is-invalid @enderror"
                                                value="{{ $data == '' ? old('no_telp') : old('no_telp', substr($data->customer->no_telp, 0, 6) . 'xx-xxxx') }}"
                                                disabled>
                                            @error('no_telp')
                                                <span id="no_telp" class="error invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="status">Status Data</label>
                                            <select name="status"
                                                class="form-control select2 @error('status') is-invalid @enderror  "
                                                id="status" required>
                                                <option value="">-- Pilih --</option>
                                                @foreach ($statusSelect as $item)
                                                    @if ($data != '')
                                                        @if (old('roleuser_id') == $item->id || $data->status == $item->id)
                                                            <option value="{{ $item->id }}" selected>{{ $item->nama }}
                                                            </option>
                                                        @else
                                                            <option value="{{ $item->id }}">{{ $item->nama }}
                                                            </option>
                                                        @endif
                                                    @else
                                                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            @error('status')
                                                <span id="status" class="error invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="deskripsi">Remarks</label>
                                            <input type="text" id="deskripsi" name="deskripsi"
                                                class="form-control @error('deskripsi') is-invalid @enderror"
                                                value="{{ $data == '' ? old('deskripsi') : old('deskripsi', $data->deskripsi) }}"
                                                required>
                                            @error('deskripsi')
                                                <span id="deskripsi" class="error invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        @if ($data != '')
                                            @if ($data->status == '1')
                                                <?php
                                                $prosesNasabah = ['VIP', 'REGULER'];
                                                ?>
                                                <div class="form-group">
                                                    <label for="tipeproses">Proses Cek</label>
                                                    <select name="tipeproses"
                                                        class="form-control select2 @error('tipeproses') is-invalid @enderror  "
                                                        id="tipeproses">
                                                        <option value="">-- Pilih --</option>
                                                        @foreach ($prosesNasabah as $item)
                                                            @if (old('tipeproses') == $item || $data->tipeproses == $item)
                                                                <option value="{{ $item }}" selected>
                                                                    {{ $item }}
                                                                </option>
                                                            @else
                                                                <option value="{{ $item }}">
                                                                    {{ $item }}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                    @error('prosesnasabah')
                                                        <span id="provider"
                                                            class="error invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="nik">NIK</label>
                                                    <input type="hidden" id="tipe" name="tipe" value="apply">
                                                    <input type="text" id="nik" name="nik"
                                                        class="form-control @error('nik') is-invalid @enderror"
                                                        value="{{ $data == '' ? old('nik') : old('nik', $data->nik) }}">
                                                    @error('nik')
                                                        <span id="nik"
                                                            class="error invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="dob">DOB</label>
                                                    <input type="date" id="dob" name="dob"
                                                        class="form-control @error('dob') is-invalid @enderror"
                                                        value="{{ $data == '' ? old('dob') : old('dob', $data->dob) }}">
                                                    @error('dob')
                                                        <span id="dob"
                                                            class="error invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="perusahaan">Perusahaan</label>
                                                    <input type="text" id="perusahaan" name="perusahaan"
                                                        class="form-control @error('perusahaan') is-invalid @enderror"
                                                        value="{{ $data == '' ? old('perusahaan') : old('perusahaan', $data->perusahaan) }}">
                                                    @error('perusahaan')
                                                        <span id="perusahaan"
                                                            class="error invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="jabatan">Jabatan</label>
                                                    <input type="text" id="jabatan" name="jabatan"
                                                        class="form-control @error('jabatan') is-invalid @enderror"
                                                        value="{{ $data == '' ? old('jabatan') : old('jabatan', $data->jabatan) }}">
                                                    @error('jabatan')
                                                        <span id="jabatan"
                                                            class="error invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="jmoasli">JMO Asli</label>
                                                    <input type="number" id="jmoasli" name="jmoasli"
                                                        class="form-control @error('jmoasli') is-invalid @enderror"
                                                        value="{{ $data == '' ? old('jmoasli') : old('jmoasli', $data->jmoasli) }}">
                                                    @error('jmoasli')
                                                        <span id="jmoasli"
                                                            class="error invalid-feedback">{{ $message }}</span>
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
                        {{-- <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a> --}}
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->

    <div class="modal fade" id="modal-overlay">
        <div class="overlay modal_loading">
            <button class="btn btn-primary" style="border:1px; color:#fff;" type="button" disabled>
                <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                Loading...
            </button>
        </div>
        <!-- /.modal-dialog -->
    </div>
@endsection
@section('addScript')
    <script>
        $('#formImport').submit(function() {
            $('#modal-overlay').modal({
                backdrop: 'static',
                keyboard: false
            });
            return true;
        });
    </script>
@endsection
