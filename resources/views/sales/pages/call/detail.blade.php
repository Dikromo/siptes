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
                                            <input type="number" id="no_telp" name="no_telp"
                                                class="form-control @error('no_telp') is-invalid @enderror"
                                                value="{{ $data == '' ? old('no_telp') : old('no_telp', $data->customer->no_telp) }}"
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
