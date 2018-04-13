<div class="container">
    <a href="{{asset('backend/')}}" id="logo" class="navbar-brand">
        <img src="/r_backend/img/logo.png" alt="" class="normal-logo logo-white"/>
        <img src="/r_backend/img/logo-black.png" alt="" class="normal-logo logo-black"/>
        <img src="/r_backend/img/logo-small.png" alt="" class="small-logo hidden-xs hidden-sm hidden"/>
    </a>
    <div class="clearfix">
        <button class="navbar-toggle" data-target=".navbar-ex1-collapse" data-toggle="collapse" type="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="fa fa-bars"></span>
        </button>
        <div class="nav-no-collapse pull-right" id="header-nav">
            <ul class="nav navbar-nav pull-right">
                <li class="dropdown hidden-xs">
                </li>
                <li class="dropdown hidden-xs">
                </li>
                <li class="dropdown profile-dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="/r_backend/img/samples/head.jpg" alt=""/>
                        <span class="hidden-xs">{{Auth::guard('admin')->user()->name}}</span> <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href=""><i class="fa fa-user"></i>Profile</a></li>
                        <li><a href="#"><i class="fa fa-cog"></i>Settings</a></li>
                        <li><a href="{{url('backend/logout')}}"><i class="fa fa-power-off"></i>Logout</a></li>
                    </ul>
                </li>
                <li class="hidden-xxs">
                    <a class="btn" href="{{url('backend/logout')}}">
                        <i class="fa fa-power-off"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
