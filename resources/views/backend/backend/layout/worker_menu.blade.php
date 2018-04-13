<?php
$a = preg_match("/\/claim\//",Request::getPathinfo());
$b = preg_match("/\/warranty\//",Request::getPathinfo());
$c = preg_match("/\/maintenance\//",Request::getPathinfo());
$order_active = preg_match("/\/order\//",Request::getPathinfo());
$cancel_active = preg_match("/\/cancel\//",Request::getPathinfo());
if($a||$b||$c||$order_active||$cancel_active){
    $after_active = 'active open';
}else{
    $after_active = '';
}
if($c){
    $maintenance_active = 'active opoen';
}else{
    $maintenance_active = '';
}
$order_child_active = $order_active?'active open':'';
$cancel_child_active = $cancel_active?'active open':'';

?>
<li class="{{ $after_active }}">
    <a href="#" class="dropdown-toggle">
        <i class="fa fa-desktop"></i>
        <span>售后管理</span>
        <i class="fa fa-chevron-circle-right drop-icon"></i>
    </a>
    <ul class="submenu" >
        <li>
            <a href="{{ url('/backend/claim/get_claim/all') }}">
                理赔管理
            </a>
        </li>
        <li class = {{ $maintenance_active }}>
            <a href="#" class="dropdown-toggle">
                保全管理
                <i class="fa fa-chevron-circle-right drop-icon"></i>
            </a>
            <ul class="submenu">
                <li style="list-style-type: none">
                    <a href="{{url('/backend/maintenance/change_data/user')}}">
                        投保人信息变更
                    </a>
                </li>
                <li style="list-style-type: none">
                    <a href="{{url('/backend/maintenance/change_premium')}}">
                        保额变更
                    </a>
                </li>
                <li style="list-style-type: none">
                    <a href="{{url('/backend/maintenance/change_person')}}">
                        团险人员变更
                    </a>
                </li>
            </ul>
        </li>
        <li class = {{ $cancel_child_active }}>
            <a href="{{ url('/backend/cancel/hesitation') }}">
                退保管理
            </a>
        </li>
        <li class="{{ $order_child_active }}">
            <a href="{{url('/backend/order/get_order/all')}}">
                查看订单
            </a>
        </li>
        <li>
            <a href="{{url('/backend/warranty/get_warranty/all')}}">
                查看保单
            </a>
        </li>
        {{--<li>--}}
            {{--<a href="#" class="dropdown-toggle">--}}
                {{--保单管理--}}
                {{--<i class="fa fa-chevron-circle-right drop-icon"></i>--}}
            {{--</a>--}}
            {{--<ul class="submenu">--}}
                {{--<li style="list-style-type: none">--}}

                {{--</li>--}}
                {{--<li>--}}
                    {{--<a href="{{ url('/backend/warranty/add_warranty/personal') }}">--}}
                        {{--线下保单录入--}}
                    {{--</a>--}}
                {{--</li>--}}
            {{--</ul>--}}
        {{--</li>--}}
    </ul>
</li>






<?php
    $c = preg_match("/\/status\//",Request::getPathinfo());
    $b = preg_match("/\/demand\//",Request::getPathinfo());
    $e = preg_match("/\/flow\//",Request::getPathinfo());
    if($b||$c||$e){
        $business_active = 'active open';
    }else{
        $business_active = '';
    }
    $demand_active = preg_match("/\/demand\//",Request::getPathinfo())?'active open':'';
    $flow_active = preg_match("/\/flow\//",Request::getPathinfo())?'active open':'';
?>
<li class="{{ $business_active }}">
    <a href="#" class="dropdown-toggle">
        <i class="fa fa-desktop"></i>
        <span>运营管理</span>
        <i class="fa fa-chevron-circle-right drop-icon"></i>
    </a>
    <ul class="submenu">
        <li class="{{$flow_active}}">
            <a href="#" class="dropdown-toggle">
                工作流管理
                <i class="fa fa-chevron-circle-right drop-icon"></i>
            </a>
            <ul class="submenu">
                <li style="list-style-type: none">
                    <a href="{{url('/backend/flow/index')}}">
                        工作流
                    </a>
                </li>
                <li>
                    <a href="{{ url('/backend/flow/node') }}">
                        节点
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a href="/backend/status/index">
                状态管理
            </a>
        </li>
        <li class="{{$demand_active}}">
            <a href="#" class="dropdown-toggle">
                需求管理
                <i class="fa fa-chevron-circle-right drop-icon"></i>
            </a>
            <ul class="submenu">
                <li>
                    <a href="{{url('backend/demand/index/user')}}">
                        待响应需求
                    </a>
                </li>
                <li>
                    <a href="{{asset('backend/demand/deal/user')}}">
                        已处理需求
                    </a>
                </li>
                {{--<li>--}}
                    {{--<a href="{{asset('backend/sell/ditch_agent/ditch_bind_agent')}}">--}}
                        {{--已转化保单--}}
                    {{--</a>--}}
                {{--</li>--}}
            </ul>
        </li>
    </ul>
</li>

<?php
$agent_active = preg_match("/\/relation\//",Request::getPathinfo()) ? "active open" : "";
?>
<li class="{{ $agent_active }}">
    <a href="#" class="dropdown-toggle">
        <i class="fa fa-desktop"></i>
        <span>客户管理</span>
        <i class="fa fa-chevron-circle-right drop-icon"></i>
    </a>
    <ul class="submenu">
        <li>
            <a href="/backend/relation/cust/all">
                客户池
            </a>
        </li>
        <li>
            <a href="{{asset('/backend/relation/cust/un_distribution')}}">
                未分配客户
            </a>
        </li>
        <li>
            <a href="{{asset('/backend/relation/cust/distribution')}}">
                已分配客户
            </a>
        </li>
        <li>
            <a href="/backend/relation/get_apply">
                申请列表
            </a>
        </li>
    </ul>
</li>

<?php
$active = preg_match("/\/sell\//",Request::getPathinfo()) ? "active open" : "";
$business_active = preg_match("/\/business\//",Request::getPathinfo());
$task_active = preg_match("/\/task\//",Request::getPathinfo());
if($active||$business_active||$task_active){
    $sale_active = 'active open';
}else{
    $sale_active = '';
}
?>
<li class="{{$sale_active}}">
    <a href="#" class="dropdown-toggle">
        <i class="fa fa-file-text-o"></i>
        <span>销售管理</span>
        <i class="fa fa-chevron-circle-right drop-icon"></i>
    </a>
    <ul class="submenu">
        <li class="{{ $business_active }}">
            <a href="/backend/business/competition">
                竞赛方案管理
            </a>
        </li>
        <li class="{{ $task_active }}">
            <a href="{{ url('/backend/task/index') }}">
                任务管理
            </a>
        </li>
        <li class="{{$active}}">
            <a href="#" class="dropdown-toggle">
                代理人渠道管理
                <i class="fa fa-chevron-circle-right drop-icon"></i>
            </a>
            <ul class="submenu">
                <li>
                    <a href="{{asset('backend/sell/ditch_agent/ditches')}}">
                        渠道管理
                    </a>
                </li>
                <li>
                    <a href="{{asset('backend/sell/ditch_agent/agents')}}">
                        代理人管理
                    </a>
                </li>
                <li>
                    <a href="{{asset('backend/sell/ditch_agent/ditch_bind_agent')}}">
                        渠道代理人关联
                    </a>
                </li>
                <li>
                    <a href="{{ asset('backend/sell/ditch_agent/brokerage') }}">
                        佣金设置
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</li>
<?php
$sms_active = preg_match("/\/sms\//",Request::getPathinfo()) ? "active open" : "";
?>

<li class="{{$sms_active}}">
    <a href="#" class="dropdown-toggle">
        <i class="fa fa-desktop"></i>
        <span>消息管理</span>
        <i class="fa fa-chevron-circle-right drop-icon"></i>
    </a>
    <ul class="submenu">
        <li>
            <a href='/backend/sms/email'>
                邮件管理
            </a>
        </li>
        <li>
            <a href='/backend/sms/sms'>
                短信管理
            </a>
        </li>
        {{--<li>--}}
        {{--<a href="/backend/sms/dopay">--}}
        {{--短信订单--}}
        {{--</a>--}}
        {{--</li>--}}
        <li>
            <a href='/backend/sms/onlineservice'>
                在线客服
            </a>
        </li>
        <li>
            <a href="/backend/sms/message">
                站内信管理
            </a>
        </li>
    </ul>

<?php
$product_active = preg_match("/\/product\//",Request::getPathinfo()) ? "active open" : "";
?>

<li class="{{$product_active}}">
    <a href="#" class="dropdown-toggle">
        <i class="fa fa-desktop"></i>
        <span>产品发布</span>
        <i class="fa fa-chevron-circle-right drop-icon"></i>
    </a>
    <ul class="submenu">
        <li>
            <a href="/backend/product/productlists">
                产品池
            </a>
        </li>
        <li>
            <a href="/backend/product/productlist">
                产品列表
            </a>
        </li>
        <li>
            <a href="/backend/product/productlabels">
                产品标签
            </a>
        </li>
    </ul>
</li>
<?php
$special_active = preg_match("/\/special\//",Request::getPathinfo()) ? "active open" : "";
?>
<li class="{{$special_active}}">
    <a href="/backend/special/addspecial">
        <i class="fa fa-desktop"></i>
        <span>工单管理</span>
    </a>
</li>
