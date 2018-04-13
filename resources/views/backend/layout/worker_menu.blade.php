<?php
$active = preg_match("/\/product\//",Request::getPathinfo()) ? "active open" : "";
$sms_active = preg_match("/\/sms\//",Request::getPathinfo()) ? "active open" : "";
?>
<li class="{{$active}}">
    <a href="#" class="dropdown-toggle">
        <i class="fa fa-desktop"></i>
        <span>产品管理</span>
        <i class="fa fa-chevron-circle-right drop-icon"></i>
    </a>
    <ul class="submenu">
        <li>
            <a href="{{asset('backend/product/category')}}">
                1)分类列表
            </a>
        </li>
        <li>
            <a href="{{asset('backend/product/company')}}">
                2)保险公司
            </a>
        </li>
        <li>
            <a href="{{asset('backend/product/duty')}}">
                3)责任列表
            </a>
        </li>
        {{--<li>--}}
            {{--<a href="{{asset('backend/product/insure_option')}}">--}}
                {{--投保参数--}}
            {{--</a>--}}
        {{--</li>--}}
        <li>
            <a href="{{asset('backend/product/clause')}}">
                4)条款列表
            </a>
        </li>
        <li>
            <a href="{{asset('backend/product/insurance')}}">
                5)保险产品
            </a>
        </li>
        <li>
            <a href="{{asset('backend/product/api_from')}}">
                接口来源
            </a>
        </li>
        <li>
            <a href="{{ route('insurance.bind.index') }}">
                接口产品绑定
            </a>
        </li>
        <li>
            <a href="{{ route('insurance.bind.list') }}">
                产品绑定列表
            </a>
        </li>
    </ul>
</li>

<li class="{{$sms_active}}">
    <a href="#" class="dropdown-toggle">
        <i class="fa fa-desktop"></i>
        <span>消息管理</span>
        <i class="fa fa-chevron-circle-right drop-icon"></i>
    </a>
    <ul class="submenu">
        <li>
            <a href='/backend/sms/emails'>
                邮件管理
            </a>
        </li>
    </ul>
</li>
