@extends('admin.layouts.main')

@section('container')
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2/css/select2.min.css') }}">

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="tanggal">Tanggal</label>
                            <input type="date" id="tanggal" name="tanggal"
                                class="form-control @error('tanggal') is-invalid @enderror"
                                value="{{ old('tanggal', date('Y-m-d')) }}" required>
                            @error('tanggal')
                                <span id="tanggal" class="error invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <?php
                        $date_by = [['nama' => 'Distribusi', 'field' => 'distribusi_at'], ['nama' => 'Sales Call', 'field' => 'updated_at']];
                        
                        ?>
                        <div class="form-group">
                            <label for="date_by">Berdasarkan Tanggal</label>
                            <select name="date_by" class="form-control select2 @error('date_by') is-invalid @enderror  "
                                id="date_by">
                                <option value="">-- Pilih --</option>
                                @foreach ($date_by as $key => $val)
                                    @if ($val['nama'] == 'Distribusi')
                                        <option value="{{ $val['field'] }}" selected>{{ $val['nama'] }}</option>
                                    @else
                                        <option value="{{ $val['field'] }}">{{ $val['nama'] }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('date_by')
                                <span id="date_by" class="error invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="user_id">Sales</label>
                            <select name="user_id" class="form-control select2 @error('user_id') is-invalid @enderror  "
                                id="user_id">
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
                    <figure class="highcharts-figure m-5">
                        <div id="container"></div>
                        {{-- <p class="highcharts-description">
                            Chart showing browser market shares. Clicking on individual columns
                            brings up more detailed data. This chart makes use of the drilldown
                            feature in Highcharts to easily switch between datasets.
                        </p> --}}
                    </figure>
                </div>
                <!-- /.card -->
            </div>
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
@endsection
@section('addScript')
    <script src="{{ asset('adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

    <script>
        $('.select2').select2()

        function proses() {
            if ($('#user_id').val() != '' && $('#tanggal').val() != '' && $('#date_by').val() != '') {
                $.ajax({
                    type: "POST",
                    url: "/dashboard/ajaxsalescall",
                    data: {
                        _token: '{{ csrf_token() }}',
                        user_id: $('#user_id').val(),
                        tanggal: $('#tanggal').val(),
                        date_by: $('#date_by').val(),
                    },
                    dataType: "json",
                    encode: true,
                }).done(function(data) {
                    console.log(data);
                    runSaleschart(data);
                });
            } else {
                alert('Tidak dapat melakukan proses!');
            }
        }

        function runSaleschart(data) {
            console.log(data.salescall);
            Highcharts.chart('container', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Grafik Sales Call <span style="color:#f88484">' + data.nama + '</span> ' + data.hariini,
                    align: 'left'
                },
                subtitle: {
                    text: 'Source: SIP System',
                    align: 'left'
                },
                xAxis: {
                    categories: data.statuscall,
                    crosshair: true,
                    accessibility: {
                        description: ''
                    }
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Total Customer'
                    }
                },
                tooltip: {
                    valueSuffix: ' Total Customer'
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true
                        },
                    }
                },
                series: [{
                    name: data.nama,
                    data: data.salescall
                }, ]
            });

        }
    </script>
@endsection
