<div>
    {{-- The Master doesn't talk, he acts. --}}

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="index3.html" class="brand-link text-center ">
            {{-- <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8"> --}}

            <span class="brand-text font-weight-light ">MaaSMS</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="{{ empty(Auth::user()->profile_photo_path) == false ? asset('assets/uploads/' . Auth::user()->profile_photo_path) : asset('face-0.jpg') }}"
                        width="60" height="60" class="rounded-circle" alt="User Image">
                </div>
                <div class="info">
                    <a href="{{ route('user.profile') }}" class="d-block text-capitalize "> {{ $name->name }} </a>
                </div>
            </div>



            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                    data-accordion="false">

                    {{-- system admin --}}
                    @if (Auth::user()->role->name == 'system-admin')

                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}" class="nav-link ">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                                <livewire:messages.checker-livewire>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('users') }}" class="nav-link ">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Users</p>
                            </a>
                        </li>


                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-copy"></i>
                                <p>
                                    Trimesters
                                    <i class="fas fa-angle-left right"></i>
                                    {{-- <span class="badge badge-info right">6</span> --}}
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                @foreach ($trimesters as $item)
                                    <li class="nav-item">
                                        <a href="{{ route('trimester.weeks', $item->id) }}" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Trimester {{ $item->trimester }}</p>
                                        </a>
                                    </li>
                                @endforeach


                            </ul>
                        </li>


                        <li class="nav-item">
                            <a href="{{ route('days.range') }}" class="nav-link">
                                <i class="nav-icon fas fa-th"></i>
                                <p>
                                    Days Time Range

                                </p>
                            </a>
                        </li>

                    @endif

                    {{-- admin --}}
                    @if (Auth::user()->role->name == 'admin')

                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link ">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                            <livewire:messages.checker-livewire>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('users') }}" class="nav-link ">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Users</p>
                        </a>
                    </li>


                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-copy"></i>
                            <p>
                                Trimesters
                                <i class="fas fa-angle-left right"></i>
                                {{-- <span class="badge badge-info right">6</span> --}}
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @foreach ($trimesters as $item)
                                <li class="nav-item">
                                    <a href="{{ route('trimester.weeks', $item->id) }}" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Trimester {{ $item->trimester }}</p>
                                    </a>
                                </li>
                            @endforeach


                        </ul>
                    </li>


                    <li class="nav-item">
                        <a href="{{ route('days.range') }}" class="nav-link">
                            <i class="nav-icon fas fa-th"></i>
                            <p>
                                Days Time Range

                            </p>
                        </a>
                    </li>

                    @endif
                    {{-- doctor  --}}
                    @if (Auth::user()->role->name == 'doctor')

                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link ">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                            <livewire:messages.checker-livewire>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('users') }}" class="nav-link ">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Users</p>
                        </a>
                    </li>


                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-copy"></i>
                            <p>
                                Trimesters
                                <i class="fas fa-angle-left right"></i>
                                {{-- <span class="badge badge-info right">6</span> --}}
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @foreach ($trimesters as $item)
                                <li class="nav-item">
                                    <a href="{{ route('trimester.weeks', $item->id) }}" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Trimester {{ $item->trimester }}</p>
                                    </a>
                                </li>
                            @endforeach


                        </ul>
                    </li>


                    <li class="nav-item">
                        <a href="{{ route('days.range') }}" class="nav-link">
                            <i class="nav-icon fas fa-th"></i>
                            <p>
                                Days Time Range

                            </p>
                        </a>
                    </li>

                    @endif
                    {{-- practitioner --}}
                    @if (Auth::user()->role->name == 'practitioner')

                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link ">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                            <livewire:messages.checker-livewire>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('users') }}" class="nav-link ">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Users</p>
                        </a>
                    </li>

                    @endif
                    {{-- mother --}}
                    @if (Auth::user()->role->name == 'mother')
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}" class="nav-link ">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                                <livewire:messages.checker-livewire>
                            </a>
                        </li>
                    @endif


                    <li class="nav-item ">
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout').submit();"
                            class="nav-link">

                            <i class="nav-icon fas fa-door-open"></i>
                            <p class="text-danger">
                                Logout
                                <form id="logout" action="{{ route('logout') }}" method="POST">
                                    @csrf
                                </form>
                            </p>
                        </a>


                    </li>

                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>
</div>
