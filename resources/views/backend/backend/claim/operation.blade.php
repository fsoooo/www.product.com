@extends('backend.layout.base')
@section('content')
    <meta name="_token" content="{{ csrf_token() }}"/>
    <div id="content-wrapper">
        <div class="big-img" style="display: none;">
            <img src="" alt="" id="big-img" style="width: 75%;height: 90%;">
        </div>
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
                                    <li><a href="{{ url('/backend/claim/get_detail/'.$detail->id) }}">详细信息</a></li>
                                    <li class="active"><a href="{{ url('/backend/claim/operation/'.$detail->id) }}">操作</a></li>
                                    <li><a href="{{ url('/backend/claim/get_record/'.$detail->id) }}" >操作记录</a></li>
                                </ul>

                                <div class="tab-content">
                                    @include('backend.layout.alert_info')
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        <h3><p>账户信息</p></h3>
                                        <div class="panel-group accordion" id="account">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h4 class="panel-title">
                                                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion2" href="#account-message">
                                                            收款账户和个人信息
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="account-message" class="panel-collapse collapse">
                                                    <table id="user" class="table table-hover" style="clear: both">
                                                        <tbody>
                                                        <tr>
                                                            <td width="35%">账号类型</td>
                                                            <td width="65%">
                                                                @if ( $detail->account_type == 1 )
                                                                    银行卡号
                                                                @elseif( $detail->account_type == 2 )
                                                                    支付宝账号
                                                                @elseif( $detail->account_type == 3 )
                                                                    微信账号
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @if( $detail->account_type == 1 )
                                                            <tr>
                                                                <td>开户银行</td>
                                                                <td>
                                                                    {{ $detail->bank_name }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        <tr>
                                                            <td>姓名</td>
                                                            <td>
                                                                {{ $detail->account }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>收款账号</td>
                                                            <td>
                                                                {{ $detail->account }}
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <h3><p>单据</p></h3>
                                        <div class="panel-group accordion" id="bill">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h4 class="panel-title">
                                                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion2" href="#bill-block">
                                                            理赔单据证明
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="bill-block" class="panel-collapse collapse">
                                                    <div class="panel-body">
                                                        <div class="img">
                                                            <div class="img-list">
                                                                @if($image_count == 0)
                                                                    暂无单据
                                                                @else
                                                                    <img src="{{ url($detail->claim_url[0]->claim_url) }}" style="width: 100%;" class="bill-img" id="big-pic">
                                                                    @foreach($detail->claim_url as $value)
                                                                        <img src="{{ url($value->claim_url) }}" class="bill-img" width="33%" >
                                                                    @endforeach
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <h3><p>操作</p></h3>
                                        <div class="panel-group accordion" id="operation">
                                            <div class="panel panel-default">
                                                <form action="{{ url('/backend/claim/add_record') }}" method="post" id="form">
                                                    {{ csrf_field() }}
                                                    <input type="text" name="claim_id" value='{{ $detail->id }}' hidden>
                                                    <table id="user" class="table table-hover" style="clear: both">
                                                        <tbody>
                                                        <tr class="operation-block">
                                                            <td width="15%">选择状态</td>
                                                            <td width="60%">
                                                                <select class="form-control operation" index="1" name="status" id="status" style="width: 100%;">
                                                                    <option value="0">请选择状态</option>
                                                                    @foreach( $status as $value )
                                                                        <option value="{{ $value->id }}">{{ $value->status_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr class="operation-block" hidden>
                                                            <td width="15%"></td>
                                                            <td width="60%">
                                                                <select class="form-control operation" name="" id="operation2" style="width: 100%;">
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr class="operation-block" hidden>
                                                            <td width="15%"></td>
                                                            <td width="60%">
                                                                <select class="form-control operation" name="" index="3" id="operation3" style="width: 100%;">

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
                                                                发送邮件给   {{ $order_detail->product->company_name }}
                                                                <input type="text" name="email_url" value="{{ $order_detail->product->company_email }}" hidden>
                                                                <input type="text" name="union_order_code" value="{{ $order_detail->warranty_rule->union_order_code }}"  hidden>
                                                            </td>
                                                        </tr>
                                                        <tr id="email-content" hidden>
                                                            <td>邮件内容</td>
                                                            <td><textarea name="content" id="" cols="30" rows="10" class="form-control"></textarea></td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </form>
                                            </div>
                                            <button  id="btn" class="btn btn-success">保存</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <footer id="footer-bar" class="row">
                    <p id="footer-copyright" class="col-xs-12">
                        &copy; 2014 <a href="http://www.adbee.sk/" target="_blank">Adbee digital</a>. Powered by Centaurus Theme.
                    </p>
                </footer>
            </div>
        </div>
    </div>
    <script src="/js/jquery-3.1.1.min.js"></script>
    <script>
        $(function() {
            //获取dom
            var status = $('#status');
            var remarks = $('#remarks');
            var send_block = $('#send-block');
            var email_content = $('#email-content');
            var send_email = $('input[name=send_email]');
            var btn = $('#btn');
            var form = $('#form');

            //单据的点击事件
            var bill_img = $('.bill-img');
            var big_img = $('.big-img');
            var big_pic = $("#big-pic");
            bill_img.each(function (i, item) {
                $(item).click(function () {
                    var src = $(this).attr('src');
                    big_pic.attr('src', src);
                })
            })
            big_img.click(function () {
                big_img.css("display", 'none');
            })


            btn.click(function(){
                var status_val = status.val();
                if(status_val == 0){
                    alert('请选择状态');
                    return false;
                }


                form.submit();
            })



        })






    </script>
@stop

