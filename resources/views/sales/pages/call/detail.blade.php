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
                            <a href="tel:{{ $data->no_telp }}" onclick="testCall()" class="btn btn-info btn-lg mx-2"> <i
                                    class="fa fa-mobile-alt"></i>
                                Call</a>
                            <a href="https://wa.me/+62{!! substr(strip_tags($data->no_telp), 1, 20) !!}" class="btn btn-success btn-lg mx-2"> <i
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
                            <form action="/call/detail/{{ encrypt($data->id) }}" method="POST" id='formImport'>
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
                                        <div>
                                            <h4 style="font-weight:bold">
                                                {{ $data == '' ? old('name') : old('name', $data->nama) }}</h4>
                                            <h6>{{ $data == '' ? old('no_telp') : old('no_telp', substr($data->no_telp, 0, 6) . 'xx-xxxx') }}
                                                -
                                                {{ $data == '' ? old('perusahaan') : old('perusahaan', $data->perusahaan) }}
                                            </h6>
                                            <h6>
                                                {{ $data == '' ? old('kota') : old('kota', $data->kota) }}
                                            </h6>
                                        </div>
                                        {{-- <div class="form-group">
                                            <label for="nama">Nama</label>
                                            <input type="text" id="name" name="nama"
                                                class="form-control @error('name') is-invalid @enderror"
                                                value="{{ $data == '' ? old('name') : old('name', $data->nama) }}" disabled>
                                            @error('name')
                                                <span id="name" class="error invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="no_telp">No Telp</label>
                                            <input type="text" id="no_telp" name="no_telp"
                                                class="form-control @error('no_telp') is-invalid @enderror"
                                                value="{{ $data == '' ? old('no_telp') : old('no_telp', substr($data->no_telp, 0, 6) . 'xx-xxxx') }}"
                                                disabled>
                                            @error('no_telp')
                                                <span id="no_telp" class="error invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div> --}}
                                        @if (auth()->user()->cabang_id != '4')
                                            <div class="form-group">
                                                <label for="status">Status Data</label>
                                                <select name="status"
                                                    class="form-control select2 @error('status') is-invalid @enderror  "
                                                    id="status" required>
                                                    <option value="">-- Pilih --</option>
                                                    @foreach ($statusSelect as $item)
                                                        @if ($data != '')
                                                            @if (old('status') == $item->id || $data->status == $item->id)
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
                                                @error('status')
                                                    <span id="status"
                                                        class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            @if (auth()->user()->produk_id == '2' || auth()->user()->produk_id == '4')
                                                <div class="form-group">
                                                    <label for="subproduk_id">Produk</label>
                                                    <select name="subproduk_id"
                                                        class="form-control select2 @error('subproduk_id') is-invalid @enderror  "
                                                        id="subproduk_id">
                                                        <option value="">-- Pilih --</option>
                                                        @foreach ($subprodukSelect as $item)
                                                            @if ($data != '')
                                                                @if (old('subproduk_id') == $item->id || $data->subproduk_id == $item->id)
                                                                    <option value="{{ $item->id }}" selected>
                                                                        {{ $item->nama }}
                                                                    </option>
                                                                @else
                                                                    <option value="{{ $item->id }}">
                                                                        {{ $item->nama }}
                                                                    </option>
                                                                @endif
                                                            @else
                                                                <option value="{{ $item->id }}">{{ $item->nama }}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                    @error('subproduk_id')
                                                        <span id="subproduk_id"
                                                            class="error invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            @endif
                                        @endif
                                        @if (auth()->user()->cabang_id == '4')
                                            {{-- <div class="form-group text-center">
                                                <input type="hidden" id="rbutton" name="rbutton"
                                                    class="@error('rbutton') is-invalid @enderror"
                                                    value="{{ $data == '' ? old('rbutton') : old('rbutton', $data->jenisstatus == '0' ? '' : $data->jenisstatus) }}">
                                                <a id="rbutton1" class="btn btn-default btn-sm mx-2 m-2"
                                                    {{ $data == '' ? '' : (old('rbutton', $data->jenisstatus) == '1' ? 'style=background-color:#ff2d2e;color:#fff;' : '') }}
                                                    onclick="rbutton('1');">
                                                    Terhubung
                                                </a>
                                                <a id="rbutton2" class="btn btn-default btn-sm mx-2 m-2"
                                                    {{ $data == '' ? '' : (old('rbutton', $data->jenisstatus) == '2' ? 'style=background-color:#ff2d2e;color:#fff;' : '') }}
                                                    onclick="rbutton('2');">
                                                    Tidak Terhubung
                                                </a>
                                                @error('rbutton')
                                                    <br> <span id="rbutton"
                                                        class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div> --}}
                                            <?php
                                            $rbuttonArray = ['Terhubung', 'Tidak Terhubung'];
                                            
                                            $rbutton = '';
                                            if ($data == '') {
                                            } else {
                                                if ($data->status == '0') {
                                                } else {
                                                    if (old('status') == 'Terhubung' || $data->jenisstatus == '1') {
                                                        $rbutton = 'Terhubung';
                                                    } else {
                                                        if ($data->jenisstatus == '2') {
                                                            $rbutton = 'Tidak Terhubung';
                                                        }
                                                    }
                                                }
                                            }
                                            ?>
                                            <div class="form-group">
                                                <label for="rstatus">Terhubung / Tidak Terhubung</label>
                                                <input type="hidden" id="rbutton" name="rbutton"
                                                    class="@error('rbutton') is-invalid @enderror"
                                                    value="{{ $data == '' ? old('rbutton') : old('rbutton', $data->jenisstatus == '0' ? '' : $data->jenisstatus) }}">
                                                <select name="rstatus"
                                                    class="form-control select2 @error('rstatus') is-invalid @enderror  "
                                                    id="rstatus">
                                                    <option value="">-- Pilih --</option>
                                                    @foreach ($rbuttonArray as $item)
                                                        @if (old('rstatus') == $item || $rbutton == $item)
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
                                                @error('rstatus')
                                                    <span id="rstatus"
                                                        class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <?php
                                            $vpengajuanarray = ['Ya', 'Tidak', 'Pikir - Pikir'];
                                            $vpengajuan = '';
                                            if ($data == '') {
                                            } else {
                                                if ($data->status == '0') {
                                                } else {
                                                    if (old('status') == 'Ya' || (old('status', $data->status) == '15' && $data->jenisstatus == '1')) {
                                                        $vpengajuan = 'Ya';
                                                    } elseif (old('status') == 'Pikir - Pikir' || (old('status', $data->status) == '34' && $data->jenisstatus == '1')) {
                                                        $vpengajuan = 'Pikir - Pikir';
                                                    } else {
                                                        if ($data->jenisstatus == '1') {
                                                            $vpengajuan = 'Tidak';
                                                        }
                                                    }
                                                }
                                            }
                                            //$vpengajuan = (($data == '' ? '' : $data->status == '0') ? '' : old('status', $data->status) == '15') ? 'Ya' : 'Tidak';
                                            ?>
                                            <div class="fstatus form-group"
                                                {{ $data == '' ? 'style=display:none;' : (old('rbutton', $data->jenisstatus) == '1' ? 'style=display:block;' : 'style=display:none;') }}>
                                                <label for="status">Pengajuan?</label>
                                                <input type="hidden" id="tipe" name="tipe"
                                                    value="{{ ($data == '' ? '' : $data->status == '1' || $data->status == '15') ? 'apply' : '' }}">
                                                <select name="status"
                                                    class="form-control select2 @error('status') is-invalid @enderror  "
                                                    id="status">
                                                    <option value="">-- Pilih --</option>
                                                    @foreach ($vpengajuanarray as $item)
                                                        @if (old('status') == $item || $vpengajuan == $item)
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
                                                @error('status')
                                                    <span id="status"
                                                        class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="fsubproduk form-group"
                                                {{ $data == '' ? 'style=display:none;' : (old('status', $vpengajuan) == 'Ya' ? 'style=display:block;' : 'style=display:none;') }}>
                                                <label for="subproduk_id">Produk</label>
                                                <select name="subproduk_id"
                                                    class="form-control select2 @error('subproduk_id') is-invalid @enderror  "
                                                    id="subproduk_id">
                                                    <option value="">-- Pilih --</option>
                                                    @foreach ($subprodukSelect as $item)
                                                        @if ($data != '')
                                                            @if (old('subproduk_id') == $item->id || $data->subproduk_id == $item->id)
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
                                                @error('subproduk_id')
                                                    <span id="subproduk_id"
                                                        class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="fstatus1 form-group"
                                                {{ $data == '' ? 'style=display:none;' : (old('status', $vpengajuan) == 'Tidak' ? 'style=display:block;' : 'style=display:none;') }}>
                                                <label for="status1">Alasan</label>
                                                <select name="status1"
                                                    class="form-control select2 @error('status1') is-invalid @enderror  "
                                                    id="status1">
                                                    <option value="">-- Pilih --</option>
                                                    @foreach ($statusSelect1 as $item)
                                                        @if ($data != '')
                                                            @if (old('status1') == $item->id || $data->parentstatus_id == $item->id || $data->status == $item->id)
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
                                                @error('status1')
                                                    <span id="status1"
                                                        class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="fstatus2 form-group"
                                                {{ $data == '' ? 'style=display:none;' : (old('rbutton', $data->jenisstatus) == '2' ? 'style=display:block;' : 'style=display:none;') }}>
                                                <label for="status2">Alasan</label>
                                                <select name="status2"
                                                    class="form-control select2 @error('status2') is-invalid @enderror  "
                                                    id="status2">
                                                    <option value="">-- Pilih --</option>
                                                    @foreach ($statusSelect2 as $item)
                                                        @if ($data != '')
                                                            @if (old('status2') == $item->id || $data->status == $item->id)
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
                                                @error('status2')
                                                    <span id="status2"
                                                        class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="fstatus3 form-group"
                                                {{ $data == '' ? 'style=display:none;' : (old('status1', $data->parentstatus_id) == '29' ? 'style=display:block;' : 'style=display:none;') }}>
                                                <label for="status3">Detail Alasan</label>
                                                <select name="status3"
                                                    class="form-control select2 @error('status3') is-invalid @enderror  "
                                                    id="status3">
                                                    <option value="">-- Pilih --</option>
                                                    @foreach ($statusSelect3 as $item)
                                                        @if ($data != '')
                                                            @if (old('status3') == $item->id || $data->status == $item->id)
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
                                                @error('status3')
                                                    <span id="status3"
                                                        class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group fLoan_apply fPikir"
                                                {{ $data == '' ? 'style=display:none;' : (old('status', $vpengajuan) == 'Pikir - Pikir' || old('status', $vpengajuan) == 'Ya' ? 'style=display:block;' : 'style=display:none;') }}>
                                                <label for="loan_apply">Pengajuan Pinjaman </label>
                                                <input type="text" id="loan_apply" name="loan_apply"
                                                    class="form-control @error('loan_apply') is-invalid @enderror"
                                                    value="{{ $data == '' ? old('loan_apply') : old('loan_apply', $data->loan_apply) }}"
                                                    data-inputmask="'alias': 'currency', 'placeholder': '', 'digits': '0','digitsOptional':'!1', 
                                                    'rightAlign':'false', 'allowMinus': 'false', 'showMaskOnFocus': 'false', 
                                                    'showMaskOnHover': 'false','groupSeparator':'.','removeMaskOnSubmit': 'true'"
                                                    data-mask>
                                                @error('loan_apply')
                                                    <span id="loan_apply"
                                                        class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group fBank_penerbit fPikir"
                                                {{ $data == ''
                                                    ? 'style=display:none;'
                                                    : (old('status', $vpengajuan) == 'Pikir - Pikir' ||
                                                    old('status', $vpengajuan) == 'Ya' ||
                                                    (old('status3', $data->status) == '21') |
                                                        (old('status3', $data->status) == '22') |
                                                        (old('status3', $data->status) == '38')
                                                        ? 'style=display:block;'
                                                        : 'style=display:none;') }}>
                                                <label for="bank_penerbit">Penerbit Kartu Kredit</label>
                                                <input type="text" id="bank_penerbit" name="bank_penerbit"
                                                    class="form-control @error('bank_penerbit') is-invalid @enderror"
                                                    value="{{ $data == '' ? old('bank_penerbit') : old('bank_penerbit', $data->bank_penerbit) }}">
                                                @error('bank_penerbit')
                                                    <span id="bank_penerbit"
                                                        class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group fLimit fPikir"
                                                {{ $data == ''
                                                    ? 'style=display:none;'
                                                    : (old('status', $vpengajuan) == 'Pikir - Pikir' ||
                                                    old('status', $vpengajuan) == 'Ya' ||
                                                    (old('status3', $data->status) == '21') |
                                                        (old('status3', $data->status) == '22') |
                                                        (old('status3', $data->status) == '38')
                                                        ? 'style=display:block;'
                                                        : 'style=display:none;') }}>
                                                <label for="limit">Limit Kartu Kredit</label>
                                                <input type="text" id="limit" name="limit"
                                                    class="form-control @error('limit') is-invalid @enderror"
                                                    value="{{ $data == '' ? old('limit') : old('limit', $data->limit) }}"
                                                    data-inputmask="'alias': 'currency', 'placeholder': '', 'digits': '0','digitsOptional':'!1', 
                                                    'rightAlign':'false', 'allowMinus': 'false', 'showMaskOnFocus': 'false', 
                                                    'showMaskOnHover': 'false','groupSeparator':'.','removeMaskOnSubmit': 'true'"
                                                    data-mask>
                                                @error('limit')
                                                    <span id="limit"
                                                        class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group fMob fPikir"
                                                {{ $data == ''
                                                    ? 'style=display:none;'
                                                    : (old('status', $vpengajuan) == 'Pikir - Pikir' ||
                                                    old('status', $vpengajuan) == 'Ya' ||
                                                    (old('status3', $data->status) == '21') |
                                                        (old('status3', $data->status) == '22') |
                                                        (old('status3', $data->status) == '38')
                                                        ? 'style=display:block;'
                                                        : 'style=display:none;') }}>
                                                <label for="mob">MOB</label>
                                                <input type="text" id="mob" name="mob"
                                                    class="form-control @error('mob') is-invalid @enderror"
                                                    value="{{ $data == '' ? old('mob') : old('mob', $data->mob) }}"
                                                    data-inputmask="'mask': '99/99'" data-mask>
                                                @error('mob')
                                                    <span id="mob"
                                                        class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        @endif
                                        <div class="form-group">
                                            <label for="deskripsi">Remarks</label>
                                            <input type="text" id="deskripsi" name="deskripsi"
                                                class="form-control @error('deskripsi') is-invalid @enderror"
                                                value="{{ $data == '' ? old('deskripsi') : old('deskripsi', $data->deskripsi) }}">
                                            @error('deskripsi')
                                                <span id="deskripsi"
                                                    class="error invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        @if ($data != '')
                                            @if (auth()->user()->produk_id == '1' && auth()->user()->cabang_id == '1')
                                                <div class="fokbank"
                                                    {{ $data == ''
                                                        ? 'style=display:none;'
                                                        : (old('status', $data->status) == '1'
                                                            ? 'style=display:block;'
                                                            : 'style=display:none;') }}>
                                                    @if (auth()->user()->roleuser_id == '2')
                                                        <?php
                                                        $prosesNasabah = ['VIP', 'REGULER'];
                                                        ?>
                                                        <div class="form-group">
                                                            <label for="tipeproses">Proses Cek</label>
                                                            @if (auth()->user()->roleuser_id == '2' && $data->user_id == auth()->user()->id)
                                                                <input type="hidden" id="tipe" name="tipe"
                                                                    value="apply">
                                                            @endif
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
                                                            @error('tipeproses')
                                                                <span id="tipeproses"
                                                                    class="error invalid-feedback">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    @endif
                                                    <div class="form-group">
                                                        <label for="namaktp">Nama KTP</label>
                                                        <input type="text" id="namaktp" name="namaktp"
                                                            class="form-control @error('namaktp') is-invalid @enderror"
                                                            value="{{ $data == '' ? old('namaktp') : old('namaktp', $data->namaktp) }}">
                                                        @error('namaktp')
                                                            <span id="namaktp"
                                                                class="error invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="nik">NIK</label>
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
                                                        <label for="email">Email</label>
                                                        <input type="email" id="email" name="email"
                                                            class="form-control @error('email') is-invalid @enderror"
                                                            value="{{ $data == '' ? old('email') : old('email', $data->email) }}">
                                                        @error('email')
                                                            <span id="email"
                                                                class="error invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="perusahaan">Perusahaan</label>
                                                        <input type="text" id="perusahaan" name="perusahaan"
                                                            class="form-control @error('perusahaan') is-invalid @enderror"
                                                            value="{{ $data == '' ? old('perusahaan') : old('perusahaan', $data->disperusahaan) }}">
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
                                                        <label for="masakerja">Masa Kerja (Tahun - Bulan)</label>
                                                        <input type="text" id="masakerja" name="masakerja"
                                                            class="form-control @error('masakerja') is-invalid @enderror"
                                                            value="{{ $data == '' ? old('masakerja') : old('masakerja', $data->masakerja) }}"
                                                            data-inputmask="'mask': '99-99'" data-mask>
                                                        @error('masakerja')
                                                            <span id="masakerja"
                                                                class="error invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="nokantor">Nomor Kantor</label>
                                                        <input type="text" id="nokantor" name="nokantor"
                                                            class="form-control @error('nokantor') is-invalid @enderror"
                                                            value="{{ $data == '' ? old('nokantor') : old('nokantor', $data->nokantor) }}"
                                                            data-inputmask="'mask': '999-999999999'" data-mask>
                                                        @error('nokantor')
                                                            <span id="nokantor"
                                                                class="error invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="jmoasli">JMO Asli</label>
                                                        <input type="text" id="jmoasli" name="jmoasli"
                                                            class="form-control @error('jmoasli') is-invalid @enderror"
                                                            value="{{ $data == '' ? old('jmoasli') : old('jmoasli', $data->jmoasli) }}"
                                                            data-inputmask="'alias': 'currency', 'placeholder': '', 'digits': '0','digitsOptional':'!1', 
                                                        'rightAlign':'false', 'allowMinus': 'false', 'showMaskOnFocus': 'false', 
                                                        'showMaskOnHover': 'false','groupSeparator':'.','removeMaskOnSubmit': 'true'"
                                                            data-mask>
                                                        @error('jmoasli')
                                                            <span id="jmoasli"
                                                                class="error invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="loan_apply">Pengajuan Pinjaman </label>
                                                        <input type="text" id="loan_apply" name="loan_apply"
                                                            class="form-control @error('loan_apply') is-invalid @enderror"
                                                            value="{{ $data == '' ? old('loan_apply') : old('loan_apply', $data->loan_apply) }}"
                                                            data-inputmask="'alias': 'currency', 'placeholder': '', 'digits': '0','digitsOptional':'!1', 
                                                        'rightAlign':'false', 'allowMinus': 'false', 'showMaskOnFocus': 'false', 
                                                        'showMaskOnHover': 'false','groupSeparator':'.','removeMaskOnSubmit': 'true'"
                                                            data-mask>
                                                        @error('loan_apply')
                                                            <span id="loan_apply"
                                                                class="error invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="bank_penerbit">Penerbit Kartu Kredit</label>
                                                        <input type="text" id="bank_penerbit" name="bank_penerbit"
                                                            class="form-control @error('bank_penerbit') is-invalid @enderror"
                                                            value="{{ $data == '' ? old('bank_penerbit') : old('bank_penerbit', $data->bank_penerbit) }}">
                                                        @error('bank_penerbit')
                                                            <span id="bank_penerbit"
                                                                class="error invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="limit">Limit Kartu Kredit</label>
                                                        <input type="text" id="limit" name="limit"
                                                            class="form-control @error('limit') is-invalid @enderror"
                                                            value="{{ $data == '' ? old('limit') : old('limit', $data->limit) }}"
                                                            data-inputmask="'alias': 'currency', 'placeholder': '', 'digits': '0','digitsOptional':'!1', 
                                                        'rightAlign':'false', 'allowMinus': 'false', 'showMaskOnFocus': 'false', 
                                                        'showMaskOnHover': 'false','groupSeparator':'.','removeMaskOnSubmit': 'true'"
                                                            data-mask>
                                                        @error('limit')
                                                            <span id="limit"
                                                                class="error invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="mob">MOB</label>
                                                        <input type="text" id="mob" name="mob"
                                                            class="form-control @error('mob') is-invalid @enderror"
                                                            value="{{ $data == '' ? old('mob') : old('mob', $data->mob) }}"
                                                            data-inputmask="'mask': '99/99'" data-mask>
                                                        @error('mob')
                                                            <span id="mob"
                                                                class="error invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>
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
    <script src="{{ asset('adminlte/plugins/inputmask/jquery.inputmask.min.js') }}"></script>
    <script>
        var cabang_id = '{{ auth()->user()->cabang_id }}';
        var produk_id = '{{ auth()->user()->produk_id }}';
        var tesCall = 0;
        if (produk_id == '1' && cabang_id == '1') {
            $('[data-mask]').inputmask();

            $("#status").change(function() {
                if (this.value == '1') {
                    $('.fokbank').css('display', 'block');
                } else {
                    $('.fokbank').css('display', 'none');
                }
            });
        }
        if (cabang_id == '4') {
            $('[data-mask]').inputmask();

            function testCall() {
                tesCall = parseInt(tesCall) + 1;
            }
            $("#status").change(function() {
                if (this.value == 'Ya') {
                    $('#status1').val('');
                    $('#status3').val('');
                    $('.fsubproduk').css('display', 'block');
                    $('.fPikir').css('display', 'block');
                    $('.fstatus1').css('display', 'none');
                    $('.fstatus3').css('display', 'none');
                } else if (this.value == 'Pikir - Pikir') {
                    $('#subproduk_id').val('');
                    $('#status3').val('');
                    $('.fPikir').css('display', 'block');
                    $('.fsubproduk').css('display', 'none');
                    $('.fstatus1').css('display', 'none');
                    $('.fstatus3').css('display', 'none');
                } else {
                    $('#subproduk_id').val('');
                    $('.fPikir').css('display', 'none');
                    $('.fsubproduk').css('display', 'none');
                    $('.fstatus1').css('display', 'block');
                }
            });
            $("#status1").change(function() {
                if (this.value == '29') {
                    $('.fstatus3').css('display', 'block');
                } else {
                    $('#status3').val('');
                    $('.fstatus3').css('display', 'none');
                }
            });
            $("#status3").change(function() {
                if (this.value == '21' || this.value == '22' || this.value == '38') {
                    $('.fBank_penerbit').css('display', 'block');
                    $('.fLimit').css('display', 'block');
                    $('.fMob').css('display', 'block');
                } else {
                    $('#bank_penerbit').val('');
                    $('#limit').val('');
                    $('#mob').val('');
                    $('.fBank_penerbit').css('display', 'none');
                    $('.fLimit').css('display', 'none');
                    $('.fMob').css('display', 'none');
                }
            });

            $("#rstatus").change(function() {
                if (this.value == 'Terhubung') {
                    var valRbutton = '1';
                } else {
                    var valRbutton = '2';
                }
                $('#rbutton').val(valRbutton);
                $('.fPikir').css('display', 'none');
                if (valRbutton == '1') {
                    $('#rbutton1').css('background-color', '#ff2d2e ');
                    $('#rbutton1').css('color', '#fff');
                    $('.fstatus').css('display', 'block');
                } else {
                    $('#rbutton1').css('background-color', '#f8f9fa');
                    $('#rbutton1').css('color', '#444');
                    $('#subproduk_id').val('');
                    $('#status').val('');
                    $('#status1').val('');
                    $('#status3').val('');
                    $('.fsubproduk').css('display', 'none');
                    $('.fstatus').css('display', 'none');
                    $('.fstatus1').css('display', 'none');
                    $('.fstatus3').css('display', 'none');
                }
                if (valRbutton == '2') {
                    $('#rbutton2').css('background-color', '#ff2d2e ');
                    $('#rbutton2').css('color', '#fff');
                    $('.fstatus2').css('display', 'block');
                } else {
                    $('#rbutton2').css('background-color', '#f8f9fa');
                    $('#rbutton2').css('color', '#444');
                    $('#status2').val('');
                    $('.fstatus2').css('display', 'none');
                }
            });
        }
        $('#formImport').submit(function() {
            if (cabang_id == '4') {
                if ($("#status2").val() == '26') {
                    // if (tesCall == '2') {
                    //     $('#modal-overlay').modal({
                    //         backdrop: 'static',
                    //         keyboard: false
                    //     });
                    //     return true;
                    // } else {
                    //     alert('Tidak dapat melakukan proses, Mohon di coba call 1x lagi!');
                    //     return false;
                    // }
                    $('#modal-overlay').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    return true;
                } else {
                    if ($('#status').val() == 'Ya') {
                        console.log($('#loan_apply').val());
                        console.log($('#limit').val());
                        if ((parseInt($('#loan_apply').val()) >= '5000000' && parseInt($('#loan_apply').val()) <=
                                '300000000') && parseInt($(
                                '#limit').val()) >= '5000000') {
                            $('#modal-overlay').modal({
                                backdrop: 'static',
                                keyboard: false
                            });
                            return true;
                        } else {
                            alert('Mohon isikan limit dan loan apply dengan angka yang benar');
                            return false;
                        }
                    } else {
                        $('#modal-overlay').modal({
                            backdrop: 'static',
                            keyboard: false
                        });
                        return true;
                    }
                }
            } else {
                $('#modal-overlay').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                return true;
            }
        });
    </script>
@endsection
