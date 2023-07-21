<aside class="main-sidebar sidebar-ligth-primary elevation-4 bg-light">
    <!-- Brand Logo -->
    <a href="/admin" class="brand-link">
        {{-- <img src="{{ asset('assets/img/logo-mini.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8"> --}}
        <span class="brand-text font-weight-light">SIP System</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('adminlte/dist/img/blankon.jpg') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a class="d-block">{{ auth()->user()->name }}</a>
            </div>
        </div>
        <hr>
        <!-- SidebarSearch Form -->
        {{-- <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                    aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div> --}}

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
       with font-awesome or any other icon font library -->

                <li class="nav-item">
                    <a href="/admin" class="nav-link {{ $active === 'dashboard' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-th"></i>
                        <p>
                            Dashboard
                            <!--span class="right badge badge-danger">New</span-->
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/dashboard/sales" class="nav-link {{ $active === 'dashboardsales' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-column"></i>
                        <p>
                            Dashboard Sales
                            <!--span class="right badge badge-danger">New</span-->
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/user" class="nav-link {{ $active === 'user' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user"></i>
                        <p>
                            User
                            <!--span class="right badge badge-danger">New</span-->
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/customer/import" class="nav-link {{ $active === 'customer' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-database"></i>
                        <p>
                            Import Customer
                            <!--span class="right badge badge-danger">New</span-->
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/customer/distribusi" class="nav-link {{ $active === 'distribusi' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-database"></i>
                        <p>
                            Distribusi Customer
                            <!--span class="right badge badge-danger">New</span-->
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/call" class="nav-link {{ $active === 'call' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-headset"></i>
                        <p>
                            Call
                            <!--span class="right badge badge-danger">New</span-->
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/customer/callhistory" class="nav-link {{ $active === 'callhistory' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-list"></i>
                        <p>
                            Call History
                            <!--span class="right badge badge-danger">New</span-->
                        </p>
                    </a>
                </li>
                @if (auth()->user()->roleuser_id == '1' || auth()->user()->roleuser_id == '4')
                    <li class="nav-item">
                        <a href="/customer/cekdbr" class="nav-link {{ $active === 'cekdbr' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-database"></i>
                            <p>
                                Cek DBR
                                <!--span class="right badge badge-danger">New</span-->
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/jmosip" class="nav-link {{ $active === 'jmosip' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-address-card"></i>
                            <p>
                                JMO
                                <!--span class="right badge badge-danger">New</span-->
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/statuscall" class="nav-link {{ $active === 'statuscall' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-database"></i>
                            <p>
                                Status Call
                                <!--span class="right badge badge-danger">New</span-->
                            </p>
                        </a>
                    </li>
                @endif
                {{-- <li class="nav-item {{ $active === 'menu1' ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $active === 'menu1' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Starter Pages
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item ">
                            <a href="/tes1" class="nav-link {{ $active_sub === 'menu_sub1' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Active Page</p>
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a href="#" class="nav-link {{ $active_sub === 'menu_sub2' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Inactive Page</p>
                            </a>
                        </li>
                    </ul>
                </li> --}}
                <li class="nav-item">
                    <form action="/logout" method="post">
                        @csrf
                        <button type="submit" class="nav-link d-flex align-items-center gap-2 btn-logout">
                            <i class="nav-icon fas fa-sign-out"></i>
                            <p>
                                Keluar
                                <!--span class="right badge badge-danger">New</span-->
                            </p>
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
