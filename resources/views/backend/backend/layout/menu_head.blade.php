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
        <div class="nav-no-collapse navbar-left pull-left hidden-sm hidden-xs">
            <ul class="nav navbar-nav pull-left">
                <li>
                    <a class="btn" id="make-small-nav">
                        <i class="fa fa-bars"></i>
                    </a>
                </li>
            </ul>
        </div>
        <div class="nav-no-collapse pull-right" id="header-nav">
            <ul class="nav navbar-nav pull-right">
                {{--<li class="dropdown hidden-xs">--}}
                    {{--<a class="btn dropdown-toggle" data-toggle="dropdown">--}}
                        {{--<i class="fa fa-warning"></i>--}}
                        {{--<span class="count">8</span>--}}
                    {{--</a>--}}
                    {{--<ul class="dropdown-menu notifications-list">--}}
                        {{--<li class="pointer">--}}
                            {{--<div class="pointer-inner">--}}
                                {{--<div class="arrow"></div>--}}
                            {{--</div>--}}
                        {{--</li>--}}
                        {{--<li class="item-header">You have 6 new notifications</li>--}}
                        {{--<li class="item">--}}
                            {{--<a href="#">--}}
                                {{--<i class="fa fa-eye"></i>--}}
                                {{--<span class="content">New order</span>--}}
                                {{--<span class="time"><i class="fa fa-clock-o"></i>13 min.</span>--}}
                            {{--</a>--}}
                        {{--</li>--}}
                        {{--<li class="item-footer">--}}
                            {{--<a href="#">--}}
                                {{--View all notifications--}}
                            {{--</a>--}}
                        {{--</li>--}}
                    {{--</ul>--}}
                {{--</li>--}}
                {{--<li class="dropdown hidden-xs">--}}
                    {{--<a class="btn dropdown-toggle" data-toggle="dropdown">--}}
                        {{--<i class="fa fa-envelope-o"></i>--}}
                        {{--<span class="count">16</span>--}}
                    {{--</a>--}}
                    {{--<ul class="dropdown-menu notifications-list messages-list">--}}
                        {{--<li class="pointer">--}}
                            {{--<div class="pointer-inner">--}}
                                {{--<div class="arrow"></div>--}}
                            {{--</div>--}}
                        {{--</li>--}}
                        {{--<li class="item">--}}
                            {{--<a href="#">--}}
                                {{--<img src="/r_backend/img/samples/messages-photo-3.png" alt=""/>--}}
                                {{--<span class="content">--}}
                                    {{--<span class="content-headline">--}}
                                        {{--Robert Downey Jr.--}}
                                    {{--</span>--}}
                                    {{--<span class="content-text">--}}
                                        {{--Look, just because I don't be givin' no man a foot massage don't make it--}}
                                        {{--right for Marsellus to throw...--}}
                                    {{--</span>--}}
                                {{--</span>--}}
                                {{--<span class="time"><i class="fa fa-clock-o"></i>13 min.</span>--}}
                            {{--</a>--}}
                        {{--</li>--}}
                        {{--<li class="item-footer">--}}
                            {{--<a href="#">--}}
                                {{--View all messages--}}
                            {{--</a>--}}
                        {{--</li>--}}
                    {{--</ul>--}}
                {{--</li>--}}
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
