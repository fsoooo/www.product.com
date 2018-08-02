@extends('backend.layout.base')
@section('content')
    <meta name="_token" content="{{ csrf_token() }}"/>
    <style>
        .img img:hover{
            cursor: pointer;
            cursor: hand;
        }
        .big-img{
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            position: fixed;
            z-index: 100;
            top:0;
            left:0;
            padding-top: 5%;
            text-align: center;
            vertical-align: middle;
        }
        .img-list{
            height: 400px;
            min-width: 100%;
            overflow: auto;
        }
        .img-list img{
            height: 300px;
            width: 33%;
            display: inline-block;
        }
        #big-pic{
            margin-bottom: 20px;
        }
    </style>
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
                                                            <img src="/image/1.png" style="width: 100%;height: 600px;" class="bill-img" id="big-pic">
                                                            <div class="img-list">
                                                                <img src="/image/1.png" class="bill-img">
                                                                <img src="/image/Chrysanthemum.jpg" class="bill-img">
                                                                <img src="/image/Desert.jpg" class="bill-img">
                                                                <img src="/image/1.png" class="bill-img">
                                                                <img src="/image/1.png" class="bill-img">
                                                                <img src="/image/1.png" class="bill-img">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <h3><p>操作</p></h3>
                                        <div class="panel-group accordion" id="operation">
                                            <div class="panel panel-default">
                                                <form action="{{ url('/backend/claim/add_record') }}" method="post" id="operation-form">
                                                    {{ csrf_field() }}
                                                    <input type="text" name="claim_id" value='{{ $detail->id }}' hidden>
                                                    <table id="user" class="table table-hover" style="clear: both">
                                                        <tbody>
                                                        <tr class="operation-block">
                                                            <td width="15%">选择状态</td>
                                                            <td width="60%">
                                                                <select class="form-control operation" index="1" name="status" id="operation1" style="width: 100%;">
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
                                                        <tr id="explain-block">
                                                            <td>操作说明</td>
                                                            <td><input type="text" class="form-control" name=""></td>
                                                        </tr>
                                                        <tr>
                                                            <td>备注说明</td>
                                                            <td><input type="text" name="remarks" class="form-control"></td>
                                                        </tr>
                                                        <tr class="send-block" hidden>
                                                            <td></td>
                                                            <td><input type="text" class="form-control" value="发送邮件给。。。公司" disabled></td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </form>
                                            </div>
                                            <button style="text-align: center;" id="operation-btn" class="btn btn-success">保存</button>
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
        $(function(){
            var operation = $('.operation');
            var operation_block = $('.operation-block');
            var explain_block = $('#explain-block');
            var send_block = $('.send-block');
            //确定状态是否为最终子状态
            function change_name(i){
                operation.each(function(i,item){
                    $(item).attr('name','');
                })
                operation.eq(i).attr('name','status');
            }
            //确定子状态block是否显示
            function block(i){
                if(i == 0){
                    operation_block.eq(2).attr('hidden','');
                    operation_block.eq(1).removeAttr('hidden');
                }else if(i == 1){
                    operation_block.eq(2).removeAttr('hidden');
                }
            }
            //判断当前显示那个文本标签
            function explan_block(value){
                if(value == '审核通过'){
                    send_block.removeAttr('hidden');
                    var str = '<td>理赔建议</td><td><textarea class="form-control" name="operation" id="exampleTextarea" rows="9" style="resize: none"></textarea></td>';
                }else{
                    send_block.attr('hidden','');
                    var str = '<td>操作说明</td><td><input type="text" class="form-control" name="operation"></td>';
                }
                explain_block.html(str);
            };

            //发送表单
            var operation_btn = $('#operation-btn');
            var form = $('#operation-form');

            operation_btn.click(function(){
                form.submit();
            })
        });

        //单据的点击事件
        var bill_img = $('.bill-img');
        var big_img = $('.big-img');
        var big_pic = $("#big-pic");
        bill_img.each(function(i,item){
            $(item).click(function(){
                var src = $(this).attr('src');
                big_pic.attr('src',src);
            })
        })
        big_img.click(function(){
            big_img.css("display",'none');
        })






    </script>
@stop

