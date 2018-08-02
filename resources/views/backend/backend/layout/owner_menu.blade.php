<?php
    $product_active = preg_match("/\/product\//",Request::getPathinfo())?'active open':'';
?>
<li class="{{ $product_active }}">
    <a href="{{ url('/backend/boss/product/index/all') }}" >
        <i class="fa fa-desktop"></i>
        <span>产品统计</span>
        {{--<i class="fa fa-chevron-circle-right drop-icon"></i>--}}
        {{--<ul class="submenu">--}}
            {{--<li>--}}
                {{--<a href="{{ url('/backend/boss/product/index/all') }}">--}}
                    {{--我的产品--}}
                {{--</a>--}}
            {{--</li>--}}
            {{--<li>--}}
                {{--<a href="{{ url('/backend/boss/business/agent') }}">--}}
                    {{--销售情况--}}
                {{--</a>--}}
            {{--</li>--}}
        {{--</ul>--}}
    </a>
</li>
<?php
    $sale_active = preg_match("/\/sale\//",Request::getPathinfo())?'active open':'';
?>
<li class="{{$sale_active}}">
    <a href="{{url('/backend/boss/sale/index/5')}}">
        <i class="fa fa-desktop"></i>
        <span>销售统计</span>
        {{--<i class="fa fa-chevron-circle-right drop-icon"></i>--}}
        {{--<ul class="submenu">--}}
            {{--<li>--}}
                {{--<a href="{{url('/backend/boss/sale/index/5')}}">--}}
                    {{--销售详情--}}
                {{--</a>--}}
            {{--</li>--}}
        {{--</ul>--}}
    </a>
</li>
<?php
$brokerage__active = preg_match("/\/brokerage\//",Request::getPathinfo())?'active open':'';
?>
<li class="{{$brokerage__active}}">
    <a href="#" class="dropdown-toggle">
        <i class="fa fa-desktop"></i>
        <span>佣金管理</span>
        <i class="fa fa-chevron-circle-right drop-icon"></i>
        <ul class="submenu">
            <li>
                <a href="{{url('/backend/boss/brokerage/index/1')}}">
                    个人佣金统计
                </a>
            </li>
            <li>
                <a href="{{url('/backend/boss/brokerage/index/2')}}">
                    公司佣金统计
                </a>
            </li>
        </ul>
    </a>
</li>
<?php
$cust_active = preg_match("/\/boss\/cust\//",Request::getPathinfo())?'active open':'';
?>
<li class="{{$cust_active}}">
    <a href="{{ url('/backend/boss/business/cust/all') }}" class="dropdown-toggle">
        <i class="fa fa-desktop"></i>
        <span>客户统计</span>
        <i class="fa fa-chevron-circle-right drop-icon"></i>
    </a>
    <ul class="submenu">
        <li>
            <a href="{{ url('/backend/boss/cust/index/all') }}">
                添加客户统计
            </a>
        </li>
        <li>
            <a href="{{ url('/backend/boss/cust/register') }}">
                注册客户统计
            </a>
        </li>
    </ul>
</li>

<?php
$agent_active = preg_match("/\/agent\//",Request::getPathinfo())?'active open':'';
?>
<li class="{{$agent_active}}">
    <a href="{{ url('/backend/boss/agent/index/all.all') }}">
        <i class="fa fa-desktop"></i>
        <span>代理人统计</span>
        {{--<i class="fa fa-chevron-circle-right"></i>--}}
    </a>
    {{--<ul class="submenu">--}}
        {{--<li>--}}
            {{--<a href="{{ url('/backend/boss/agent/index') }}">--}}
                {{--代理人统计--}}
            {{--</a>--}}
        {{--</li>--}}
    {{--</ul>--}}
</li>


<?php
$business_active = preg_match("/\/business\//",Request::getPathinfo())?'active open':'';
?>
<li class="{{ $business_active }}">
    <a href="{{ url('/backend/boss/business/index') }}" >
        <i class="fa fa-desktop"></i>
        <span>业务统计</span>
        {{--<i class="fa fa-chevron-circle-right "></i>--}}
    </a>
    {{--<ul class="submenu">--}}

        {{--<li>--}}
            {{--<a href="{{ url('/backend/boss/business/agent') }}">--}}
                {{--代理人统计--}}
            {{--</a>--}}
        {{--</li>--}}
        {{--<li>--}}
            {{--<a href="{{ url('/backend/boss/business/index') }}">--}}
                {{--业务统计--}}
            {{--</a>--}}
        {{--</li>--}}
    {{--</ul>--}}
</li>