<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">

<head>
    @include('admin.layouts.head')
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        @switch(auth()->user()->roleuser_id)
            @case(1)
            @case(2)

            @case(4)
            @case(5)

            @case(6)
                <!-- Navbar -->
                @include('admin.layouts.navbar')
                <!-- /.navbar -->

                <!-- Main Sidebar Container -->
                @include('admin.layouts.sidebar')
            @break

            @default
        @endswitch

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper {{ auth()->user()->roleuser_id == 3 ? 'm-0' : '' }}">
            <!-- Content Header (Page header) -->
            @switch(auth()->user()->roleuser_id)
                @case(1)
                @case(2)

                @case(4)
                @case(5)

                @case(6)
                    <div class="content-header">
                        <div class="container-fluid">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <h1 class="m-0">{{ $title }}</h1>
                                </div><!-- /.col -->
                                {{-- <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Starter Page</li>
                            </ol>
                        </div><!-- /.col --> --}}
                            </div><!-- /.row -->
                        </div><!-- /.container-fluid -->
                    </div>
                @break

                @default
            @endswitch
            <!-- /.content-header -->

            <!-- Main content -->
            <div class="content">
                @yield('container')
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
            <div class="p-3">
                <h5>Title</h5>
                <p>Sidebar content</p>
            </div>
        </aside>
        <!-- /.control-sidebar -->

        <!-- Main Footer -->
        @include('admin.layouts.footer')
        <div class="modal fade" id="modal-overlay">
            <div class="overlay modal_loading">
                <button class="btn btn-primary" style="border:1px; color:#fff;" type="button" disabled>
                    <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                    Loading...
                </button>
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->

    <!-- jQuery -->
    @include('admin.layouts.scripts')
    @yield('addScript')
</body>

</html>
