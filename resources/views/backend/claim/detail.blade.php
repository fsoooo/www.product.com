@extends('backend.layout.base')
@section('content')
    <meta name="_token" content="{{ csrf_token() }}"/>
    <style>
        a{
            color: white;
            text-decoration: none;
        }
        .line  {
            font-size: 18px;
            line-height: 100%;
            vertical-align: bottom;
        }
        .line-right,.line-left{
            height:60px;
            line-height: 60px;
            display: inline-block;
            vertical-align: middle;
        }
        .line-left{

            width:10%;
            text-align: right;
            /*text-align: justify;*/
            /*text-align-last: justify;*/
            margin-right: 5%;
        }
        .line-right{
            border-bottom: 1px dashed black;
            width: 60%;
        }
        .clearFix{content:".";display:block;height:0;clear:both;visibility:hidden}
    </style>
    <div id="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <ol class="breadcrumb">
                            <li><a href="{{ url('/backend') }}">主页</a></li>
                            <li ><span>售后管理</span></li>
                            <li class="active"><span>理赔管理</span></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    <li><a href="{{ url('/backend/claim/index') }}">理赔列表</a></li>
                                    {{--<li><a href="{{ url('/backend/claim/get_claim/no_deal') }}">未处理</a></li>--}}
                                    {{--<li><a href="{{ url('/backend/claim/get_claim/deal') }}">已处理</a></li>--}}
                                    <li class="active"><a>理赔详情</a></li>
                                </ul>
                                <div class="tab-content">
                                    @include('backend.layout.alert_info')
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        <h3><p>理赔详情</p></h3>
                                        <div class="panel-group accordion" id="account">
                                            <div class="panel panel-default">
                                                <table id="user" class="table table-hover" style="clear: both">
                                                    <tbody>
                                                    <tr>
                                                        <td width="15%">账号类型</td>
                                                        <td width="65%">
                                                            @if ($detail['account_type']== 1 )
                                                                银行卡号
                                                            @elseif( $detail['account_type'] == 2 )
                                                                支付宝账号
                                                            @elseif( $detail['account_type']== 3 )
                                                                微信账号
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @if( $detail['account_type'] == 1 )
                                                        <tr>
                                                            <td>开户银行</td>
                                                            <td>
                                                                {{$detail['bank_name']}}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    <tr>
                                                        <td>收款账号</td>
                                                        <td>
                                                            {{ $detail['account'] }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>真实姓名</td>
                                                        <td>
                                                            {{json_decode($detail['user_msg'],true)['real_name']}}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>联系电话</td>
                                                        <td>
                                                            {{json_decode($detail['user_msg'],true)['phone']}}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>身份证号</td>
                                                        <td>
                                                            {{json_decode($detail['user_msg'],true)['code']}}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>申请时间</td>
                                                        <td>
                                                            {{json_decode($detail['user_msg'],true)['created_at']}}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>理赔状态</td>
                                                        <td>
                                                               尚未处理
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>订单编号</td>
                                                        <td>
                                                            {{ $detail['order_code'] }}
                                                            <span><a href="/backend/claim/getorderdetail/{{ $detail['order_code'] }}">查看订单详情</a></span>
                                                        </td>

                                                    </tr>
                                                    <tr>
                                                        <td>联合订单号</td>
                                                        <td>
                                                            {{ $detail['union_order_code'] }}
                                                        </td>
                                                    </tr>
                                                    {{--<tr>--}}
                                                        {{--<td>支付时间</td>--}}
                                                        {{--<td>--}}
                                                            {{--{{ $detail->claim_claim_rule->order->pay_time }}--}}
                                                        {{--</td>--}}
                                                    {{--</tr>--}}
                                                    {{--<tr>--}}
                                                        {{--<td>订单费用</td>--}}
                                                        {{--<td>--}}
                                                            {{--{{ $detail->claim_claim_rule->order->premium }}--}}
                                                        {{--</td>--}}
                                                    {{--</tr>--}}
                                                    {{--<tr>--}}
                                                        {{--<td>保障开始时间</td>--}}
                                                        {{--<td>--}}
                                                            {{--{{ $detail->claim_claim_rule->order->start_time }}--}}
                                                        {{--</td>--}}
                                                    {{--</tr>--}}
                                                    {{--<tr>--}}
                                                        {{--<td>保障结束时间</td>--}}
                                                        {{--<td>--}}
                                                            {{--{{ $detail->claim_claim_rule->order->end_time }}--}}
                                                        {{--</td>--}}
                                                    {{--</tr>--}}
                                                    <tr>
                                                        <td width="15%">理赔单据</td>
                                                        <td>
                                                            @foreach(json_decode($detail['claim_image'],true) as $image)
                                                            <img src="http://{{$image['claim_url']}}" style="width: 250px;height:160px">
                                                            @endforeach
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                                </div>
                                        </div>
                                    </div>
                                    <div class="panel-group accordion" id="operation">
                                        <div class="panel panel-default">
                                            <form action="{{ url('/backend/claim/add_record') }}" method="post" id="form" onsubmit="return doEmail()">
                                                {{ csrf_field() }}
                                                <input type="text" name="claim_id" value='{{ $detail->id }}' hidden>
                                                <table id="user" class="table table-hover" style="clear: both">
                                                    <tbody>
                                                    <tr class="operation-block">
                                                        <td width="15%">操作</td>
                                                        <td width="60%">
                                                            <select class="form-control operation" index="1" name="status" id="status" style="width: 100%;">
                                                                <option value="0">请选择状态</option>
                                                                @foreach( $status as $value )
                                                                    <option value="{{ $value->id }}">{{ $value->status_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>备注说明</td>
                                                        <td><input type="text" name="remarks" id="remarks" class="form-control"></td>
                                                    </tr>
                                                    <input type="text" name="send_email" value="0" hidden>
                                                    <tr id="send-block" hidden>
                                                        <td>发送邮件给</td>
                                                        <td>
                                                            发送邮件给{{$company->name }}
                                                            <input type="text" name="email_url" value="{{ $company->email }}" hidden>
                                                            <input type="text" name="union_order_code" value="{{$order->union_Order_code}}"  hidden>
                                                        </td>
                                                    </tr>
                                                    <tr id="email-content" hidden>
                                                        <td>邮件内容</td>
                                                        <td><textarea name="content" id="" cols="30" rows="10" class="form-control"></textarea></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                                <input type="submit"  id="btn" class="btn btn-success" value="确认提交">
                                                <div id="check"></div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function doEmail() {
            var email = "{{$company->email}}";
            var company_name = "{{$company->name }}";
            var union_order_code = "{{$detail['union_order_code']}}";
            var account_type ="{{$detail['account_type']}}";
            var account = "{{ $detail['account'] }}";
            if(account_type== '1' ){
                var  account_type_name = '银行卡号';
            }else if(account_type == '2' ){
                var account_type_name = '支付宝账号';
            }else if(account_type== '3' ){
                var account_type_name = '微信账号';
            }
            var  real_name = "{{json_encode(json_decode($detail['user_msg'],true)['real_name'])}}";
            var  phone = "{{json_encode(json_decode($detail['user_msg'],true)['phone'])}}";
            var person_code = "{{json_encode(json_decode($detail['user_msg'],true)['code'])}}";
            var send_time = "{{json_encode(json_decode($detail['user_msg'],true)['created_at'])}}";
            var claim_url = "{{$detail['claim_image']}}";
            if(account_type== '1' ){
                bank_name = "{{$detail['bank_name']}}";
                var params = {
                    email: email,
                    union_order_code: union_order_code,
                    account_type: account_type,
                    account_type_name:account_type_name,
                    account: account,
                    real_name: real_name,
                    phone: phone,
                    person_cod: person_code,
                    send_time: send_time,
                    claim_url: claim_url,
                    bank_name:bank_name,
                    company_name:company_name
                };
            }else{
                var params = {
                    email: email,
                    union_order_code: union_order_code,
                    account_type: account_type,
                    account_type_name:account_type_name,
                    account: account,
                    real_name: real_name,
                    phone: phone,
                    person_cod: person_code,
                    send_time: send_time,
                    claim_url: claim_url,
                    company_name:company_name
                };
            }
            alert('发邮件给保险公司');
            $.ajax( {
                type : "get",
                url : '/backend/claim/do_claim_email',
                dataType : 'json',
                data:params,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success:function(msg){
                    if(msg.status == '200'){
                        alert(msg.msg);
                        $('#check').html('<font color="green">'+msg.msg+'</font>');
                        return true;
                    }else if(msg.status == '500'){
                        alert(msg.message);
                        $('#check').html('<font color="red">'+msg.msg+'</font>');
                        return false;
                    }
                }
            });
        }
    </script>
@stop
