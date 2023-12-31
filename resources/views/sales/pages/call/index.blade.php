@extends('admin.layouts.main')

@section('container')
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <div class="container-fluid pt-3">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mr-2 text-right">
                                <div class="float-sm-right">
                                    <form action="/logout" method="post">
                                        @csrf
                                        <button type="submit" class="btn btn-warning">
                                            <i class="nav-icon fas fa-sign-out"></i>
                                            Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-12  text-center my-3">
                            <div class="text-center">
                                <img class="img-fluid" src="{{ asset('assets/img/logo.png') }}" alt="Photo">
                            </div>
                            <h3 class="profile-username text-center">{{ auth()->user()->name }}</h3>
                            <?php
                            $signalBar = '';
                            $signalText = '';
                            $timerun1 = date('Y-m-d 00:00:00');
                            $timerun2 = date('Y-m-d H:i:s');
                            $jarak = date_diff(date_create($timerun2), date_create($timerun1));
                            if ($jarak->h <= '10') {
                                $runhour = '1' * 60;
                                $runhour = ($runhour + (int) $jarak->i) / 60;
                            } else {
                                if ($jarak->h >= '17') {
                                    $runhour = '7';
                                } else {
                                    if ($jarak->h <= '12') {
                                        if ($jarak->h == '12') {
                                            $runhour = ((int) $jarak->h + 1 - 10) * 60;
                                            $runhour = $runhour / 60;
                                        } else {
                                            $runhour = ((int) $jarak->h + 1 - 10) * 60;
                                            $runhour = ($runhour + (int) $jarak->i) / 60;
                                        }
                                    } else {
                                        $runhour = ((int) $jarak->h - 10) * 60;
                                        $runhour = ($runhour + (int) $jarak->i) / 60;
                                    }
                                }
                            }
                            ?>

                            @foreach ($datastatistik as $items)
                                <?php
                                $signalPercent = round(((int) $items->callout + (int) $items->nocallout) / (float) $runhour);
                                if ($signalPercent >= '0' && $signalPercent < '26') {
                                    $signalText = '<span style="font-weight:bold;font-size:20px;color:#eb0424;">-__-</span>';
                                } elseif ($signalPercent >= '26' && $signalPercent < '30') {
                                    $signalText = '<span style="font-weight:bold;font-size:20px;color:#eb0424;">Sangat Lambat</span>';
                                } elseif ($signalPercent >= '30' && $signalPercent < '34') {
                                    $signalText = '<span style="font-weight:bold;font-size:20px;color:#eb0424;">Lambat</span>';
                                } elseif ($signalPercent >= '34' && $signalPercent < '39') {
                                    $signalText = '<span style="font-weight:bold;font-size:20px;color:#eb7904;">Kurang Cepat</span>';
                                } elseif ($signalPercent >= '39' && $signalPercent < '43') {
                                    $signalText = '<span style="font-weight:bold;font-size:20px;color:#009b05;">OK</span>';
                                } elseif ($signalPercent >= '43') {
                                    $signalText = '<span style="font-weight:bold;font-size:20px;color:#009b05;">Cepat</span>';
                                }
                                ?>
                                <p class="text-muted text-center">
                                    {{ auth()->user()->roleuser_id == '3' ? 'TELEMARKETING' : '' }}
                                </p>
                                <div class="row justify-content-md-center">
                                    <div class="col-md-3 col-sm-6 col-12">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-success"><i class="fas fa-headset"></i></span>
                                            <div class="info-box-content">
                                                <div class="row">
                                                    <div class="col-md-6 col-12">
                                                        <span class="info-box-text">Total Calls Out</span>
                                                        <span class="info-box-number">{{ $items->callout }}</span>
                                                    </div>
                                                    <div class="col-md-6 col-12">
                                                        <span class="info-box-text">Closing 3 Hari</span>
                                                        {{-- @if ($dataClosing->count() == 0)
                                                        <span class="info-box-number">0 | 0 | 0</span>
                                                    @else --}}
                                                        <span
                                                            class="info-box-number">{{ $items->closing1 . ' | ' . $items->closing2 . ' | ' . $items->closing3 }}</span>

                                                        {{-- @endif --}}

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-md-center">
                                    <div class="col-md-3 col-sm-6 col-12">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-info"><i class="fas fa-database"></i></span>
                                            <div class="info-box-content">
                                                <div class="row">
                                                    <div class="col-md-4 col-12">
                                                        <span class="info-box-text">Total Data</span>
                                                        <span
                                                            class="info-box-number">{{ (int) $items->nocall + (int) $items->nocallout + (int) $items->callout - (int) $items->distribusi_today . ' + ' . (int) $items->distribusi_today }}</span>
                                                    </div>
                                                    <div class="col-md-4 col-12">
                                                        <span class="info-box-text">Sudah Di Telepon</span>
                                                        <span
                                                            class="info-box-number">{{ (int) $items->nocallout + (int) $items->callout }}</span>
                                                    </div>
                                                    <div class="col-md-4 col-12">
                                                        <span class="info-box-text">Belum Di Telepon</span>
                                                        <span class="info-box-number">{{ (int) $items->nocall }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <p class="profile-username text-center" style="font-size:18px;">Kecepatan
                                Menelpon<br>{!! $signalText !!}</p>
                            {{-- <a class="btn"><i class="nav-icon fas fa-database"></i>
                                Total Data : {{ count($data_total) }}</a> --}}
                            @foreach ($data as $item)
                                <a href="/call/detail/{{ encrypt($item->id) }}"
                                    class="btn btn-primary btn-lg border_white"><i class="nav-icon fas fa-headset"></i>
                                    Call</a>
                            @endforeach
                        </div>
                        <hr>
                        <div class="text-center">
                            @if (auth()->user()->roleuser_id == '2')
                                <a onclick="renderTable('List Pengajuan','4');"
                                    class="btn btn-primary btn-sm border_white m-1">
                                    Show List Pengajuan Team</a>
                            @endif
                            <a onclick="renderTable('List Pengajuan','1');" class="btn btn-primary btn-sm border_white m-1">
                                Show List Pengajuan</a>
                            <a onclick="renderTable('List Prospek','3');" class="btn btn-primary btn-sm border_white m-1">
                                Show List Prospek</a>
                            <a onclick="renderTable('List Call Back','2');" class="btn btn-primary btn-sm border_white m-1">
                                Show List Call Back</a>
                        </div>
                        <div class='cTable' style="display:none">
                            <h3 class="profile-username text-left pl-2 jTable"><i class="nav-icon fas fa-headset"></i>
                                List
                                Call
                                Back
                            </h3>
                            <div class="col-12 table-responsive">
                                <table class="table table-head-fixed text-nowrap" id="dataTables1">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>Incoming Date</th>
                                            <th>Nama</th>
                                            <th>Status</th>
                                            <th>Remarks</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    {{-- <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Budi Hartanto</td>
                                        <td>Call Lagi</td>
                                        <td></td>
                                    </tr>
                                </tbody> --}}
                                </table>
                            </div>
                        </div>
                        {{-- <hr>

                        <h3 class="profile-username text-left pl-2"><i class="nav-icon fas fa-headset"></i> List Apply
                        </h3>
                        <div class="col-12 table-responsive">
                            <table class="table table-head-fixed text-nowrap" id="dataTables2">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                        <th>Nama</th>
                                        <th>Status</th>
                                        <th>Remarks</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Budi Hartanto</td>
                                        <td>Call Lagi</td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div> --}}
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
    <script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        function renderTable(judul, param) {
            $('.cTable').css('display', 'block')
            $('.jTable').html(judul);
            $('#dataTables1').DataTable({
                processing: true,
                serverside: true,
                autoWidth: false,
                bDestroy: true,
                initComplete: function(settings, json) {
                    //fromTabel = this.api().data().length;
                },
                ajax: {
                    type: 'POST',
                    url: '/call/ajax',
                    data: {
                        _token: '{{ csrf_token() }}',
                        user_id: '{{ auth()->user()->id }}',
                        status: param,
                    }
                },
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                }, {
                    data: 'updated_at',
                    name: 'updated_at'
                }, {
                    data: 'nama',
                    name: 'nama'
                }, {
                    data: 'statusText',
                    name: 'statusText'
                }, {
                    data: 'deskripsi',
                    name: 'deskripsi'
                }, {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                }],
                columnDefs: [{
                    targets: 4,
                    className: "text-center",
                }]
            });
        }
        // $('#dataTables2').DataTable({
        //     processing: true,
        //     serverside: true,
        //     autoWidth: false,
        //     bDestroy: true,
        //     initComplete: function(settings, json) {
        //         //fromTabel = this.api().data().length;
        //     },
        //     ajax: {
        //         type: 'POST',
        //         url: '/call/ajax',
        //         data: {
        //             _token: '{{ csrf_token() }}',
        //             user_id: '{{ auth()->user()->id }}',
        //             status: '1',
        //         }
        //     },
        //     columns: [{
        //         data: 'DT_RowIndex',
        //         name: 'DT_RowIndex',
        //     }, {
        //         data: 'nama',
        //         name: 'nama'
        //     }, {
        //         data: 'statusText',
        //         name: 'statusText'
        //     }, {
        //         data: 'deskripsi',
        //         name: 'deskripsi'
        //     }, {
        //         data: 'action',
        //         name: 'action',
        //         orderable: false,
        //         searchable: false,
        //     }],
        //     columnDefs: [{
        //         targets: 4,
        //         className: "text-center",
        //     }]
        // });
        $('#formImport').submit(function() {
            $('#modal-overlay').modal({
                backdrop: 'static',
                keyboard: false
            });
            return true;
        });
    </script>
@endsection
