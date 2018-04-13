<!DOCTYPE html>
<html>
@include('backend.layout.head')
<body>
<div id="theme-wrapper">
    <header class="navbar" id="header-navbar">
        @include('backend.layout.menu_head')
    </header>
    <div id="page-wrapper" class="container">
        <div class="row">
            @include('backend.layout.menu_left')
            @yield('content')
        </div>
    </div>
</div>
@include('backend.layout.menu_set')
@include('backend.layout.foot')
</body>
</html>