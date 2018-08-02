@extends('backend.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('r_backend/css/libs/nifty-component.css')}}"/>
@endsection
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2 class="pull-left">
                        账户名: {{ $user->name }}  账户ID: {{ $user->account_id }}  总订单数: {{ $user->orders_count }}
                        @if(request()->route('status') != '')
                            {{ $status[request()->route('status')] }}订单数: {{ $current_status_count }}
                        @endif
                    </h2>
                    <div class="filter-block pull-right" style="margin-right: 40px;">
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                筛选 <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a href="{{ url('backend/order/list', ['account_id' => $user->account_id]) }}">所有订单</a></li>
                                <li><a href="{{ url('backend/order/list', ['account_id' => $user->account_id, 'status' => 'check_ing']) }}">投保中</a></li>
                                <li><a href="{{ url('backend/order/list', ['account_id' => $user->account_id, 'status' => 'check_error']) }}">投保失败</a></li>
                                <li><a href="{{ url('backend/order/list', ['account_id' => $user->account_id, 'status' => 'pay_ing']) }}">等待支付</a></li>
                                <li><a href="{{ url('backend/order/list', ['account_id' => $user->account_id, 'status' => 'pay_error']) }}">支付失败</a></li>
                                <li><a href="{{ url('backend/order/list', ['account_id' => $user->account_id, 'status' => 'pay_end']) }}">支付结束</a></li>
                                <li><a href="{{ url('backend/order/list', ['account_id' => $user->account_id, 'status' => 'send_back_ing']) }}">退保中</a></li>
                                <li><a href="{{ url('backend/order/list', ['account_id' => $user->account_id, 'status' => 'send_back_error']) }}">退保失败</a></li>
                                <li><a href="{{ url('backend/order/list', ['account_id' => $user->account_id, 'status' => 'send_back_end']) }}">退保成功</a></li>
                                <li><a href="{{ url('backend/order/list', ['account_id' => $user->account_id, 'status' => 'close']) }}">关闭</a></li>
                            </ul>
                        </div>
                    </div>
                </header>
                @include('backend.layout.alert_info')
                <div class="main-box-body clearfix">
                    <div class="table-resrponsive">
                        <table class="table user-list table-hover">
                            <thead>
                            <tr>
                                <th class="text-center">订单号</th>
                                <th class="text-center">接口来源码</th>
                                <th class="text-center">产品码</th>
                                <th class="text-center">生效时间</th>
                                <th class="text-center">结束时间</th>
                                <th class="text-center">保费</th>
                                <th class="text-center">总保费</th>
                                <th class="text-center">保额</th>
                                <th class="text-center">产品数量</th>
                                <th class="text-center">被保人数量</th>
                                <th class="text-center">根据佣金比所获收益</th>
                                @if(request()->route('status') == 'check_error')
                                    <th class="text-center">投保失败原因</th>
                                @endif
                                @if(request()->route('status') == 'send_back_error')
                                    <th class="text-center">退保失败原因</th>
                                @endif
                                @if(request()->route('status') == 'pay_error')
                                    <th class="text-center">支付失败原因</th>
                                @endif
                                <th>状态</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td class="text-center">{{ $order->order_no }}</td>
                                    <td class="text-center">{{ $order->api_from }}</td>
                                    <td class="text-center">{{ $order->p_code }}</td>
                                    <td class="text-center">{{ $order->ins_start_time }}</td>
                                    <td class="text-center">{{ $order->ins_end_time }}</td>
                                    <td class="text-center">{{ $order->premium }}</td>
                                    <td class="text-center">{{ $order->total_premium / 100}}</td>
                                    <td class="text-center">{{ $order->coverage }}</td>
                                    <td class="text-center">{{ $order->p_num }}</td>
                                    <td class="text-center">{{ $order->insured_num }}</td>
                                    <td class="text-center">{{ $order->income / 100}}</td>
                                    @if(request()->route('status') == 'check_error')
                                        <td class="text-center">{{ $order->check_error_message }}</td>
                                    @endif
                                    @if(request()->route('status') == 'send_back_error')
                                        <td class="text-center">{{ $order->send_back_error_message }}</td>
                                    @endif
                                    @if(request()->route('status') == 'pay_error')
                                        <td class="text-center">{{ $order->pay_error_message }}</td>
                                    @endif
                                    <td>
                                        {{ $status[$order->status] }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{--分页--}}
                    {{ $orders->links() }}
                </div>
            </div>
        </div>

        <div class="md-modal md-effect-8 md-hide" id="modal-8">
            <div class="md-content">
                <div class="modal-header">
                    <button class="md-close close">×</button>
                    <h4 class="modal-title">参数添加</h4>
                </div>
                <div class="modal-body">
                    <form role="form" id="add_option" action='{{url('backend/product/insure_option/addPost')}}' method="post">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="exampleInputPassword1">唯一英文标识</label>
                            <input class="form-control" name="name" placeholder="唯一英文标识" type="text">
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1">备注信息</label>
                            <input class="form-control" name="comment" type="text">
                        </div>

                        <div class="form-group">
                            <label for="exampleTextarea">是否允许为空</label>
                            <select name="nullable" class="form-control">
                                <option value="yes">可空</option>
                                <option value="no">必填</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="form-submit" class="btn btn-primary">确认提交</button>
                </div>
            </div>
        {{--</div>--}}
        <div class="md-overlay"></div>

    </div>
@stop
@section('foot-js')
    <script charset="utf-8" src="/r_backend/js/modernizr.custom.js"></script>
    <script charset="utf-8" src="/r_backend/js/classie.js"></script>
    <script charset="utf-8" src="/r_backend/js/modalEffects.js"></script>
    <script>
        $(function(){
            $("#form-submit").click(function(){
                $("#add_option").submit();
            })
        })
    </script>
@stop

