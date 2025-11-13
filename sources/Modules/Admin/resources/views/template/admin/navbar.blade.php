<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item">
            <a href="javascript:void(0)" class="nav-link"><i class="fa fa-circle fa-sm text-success"></i> Online</a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Notifications Dropdown Menu -->
        @php
            $berkaskhusus = session('notifapprovalberkaskhusus');
            $test = session('notifapprovaltest');
            $all = $berkaskhusus+$test;
        @endphp
        @if($all>0)
            <li class="nav-item dropdown" style="margin-right: 10px;">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-bell"></i>
                    <span class="badge badge-warning navbar-badge">{{$all}}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-item dropdown-header">{{$all}} Notifications</span>
                    @if($berkaskhusus>0)
                        <div class="dropdown-divider"></div>
                        <a href="{{route('admin.berkaspmb.show')}}" class="dropdown-item">
                            <i class="fas fa-file mr-2"></i> {{$berkaskhusus}} Approval Berkas Khusus
                        </a>
                    @endif
                    @if($test>0)
                        <div class="dropdown-divider"></div>
                        <a href="{{route('admin.testpmb.show')}}" class="dropdown-item">
                            <i class="fas fa-users mr-2"></i> {{$test}} Approval Test PMB
                        </a>
                    @endif
                    {{-- <div class="dropdown-divider"></div> --}}
                    {{-- <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a> --}}
                </div>
            </li>
        @endif

        <li class="dropdown user user-menu" style="margin-top: 8px;">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                {{-- <img src="{{ asset('public/assets/dist/img/user2-160x160.jpg') }}" class="user-image" alt="User Image"> --}}
                <i class="fas fa-users-cog"></i>
                {{-- <span class="hidden-xs">Hi, {{session('session')['user_nama']}}</span> --}}
            </a>
            <ul class="dropdown-menu">
                <!-- User image -->
                <li class="user-header">
                    <img src="{{url('sources/storage/app/FILE_PHOTOPROFILE/'.photo_profile())}}" style="width: 100px; height: 100px; object-fit: cover; border: 2px solid #adb5bd;" class="img-circle" alt="User Image">

                    <p>
                        {{session('session')->nama}}
                        <small> </small>
                    </p>
                </li>
                <!-- Menu Footer-->
                <li class="user-footer">
                    <form action="{{route('admin.logout')}}" method="POST" id="form-logout">
                        @csrf
                    </form>
                    <a href="{{route('admin.show.changeprofile')}}" class="btn btn-primary">Profile</a>
                    <button type="submit" class="btn btn-danger float-right" form="form-logout" style="background-color: red;">Sign out</button>
                </li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button" title="Zoom Page">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
    </ul>
</nav>
<!-- /.navbar -->
