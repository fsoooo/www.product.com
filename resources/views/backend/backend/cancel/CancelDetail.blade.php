@extends('backend.layout.base')
<link rel="stylesheet" type="text/css" href="{{asset('r_backend/css/libs/nifty-component.css')}}"/>
@section('content')
    <meta name="_token" content="{{ csrf_token() }}"/>
    <style>

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
                            <li class="active"><span>退保申请</span></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    <li><a href="{{ url('/backend/cancel/hesitation') }}">犹豫期内退保</a></li>
                                    <li><a href="{{ url('/backend/cancel/out_hesitation') }}">犹豫期外退保</a></li>
                                    {{--<li class="active"><a href="#">退保申请详情</a></li>--}}
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        <h3><p>退保信息</p></h3>
                                        @include('backend.layout.alert_info')
                                        <div class="panel-group accordion" id="operation">
                                            <div class="panel panel-default">
                                                <form action="{{ url('/order/add_recognizee_submit') }}" method="post" id="form">
                                                    {{ csrf_field() }}
                                                    <table id="user" class="table table-hover" style="clear: both">
                                                        <tbody>
                                                        <tr>
                                                            <td>保单编号</td>
                                                            <td>{{ $cancel_detail->cancel_order->order_code }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>退保理由</td>
                                                            <td>
                                                                {{ $cancel_detail->result }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>申请时间</td>
                                                            <td>{{ $cancel_detail->apply_time }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>保单生效时间</td>
                                                            <td>{{ $cancel_detail->cancel_order->start_time }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>保单结束时间</td>
                                                            <td>{{ $cancel_detail->cancel_order->end_time }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>保单犹豫期</td>
                                                            <td>{{ $cancel_detail->hesitation }}</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
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
    </div>




    <script src="/js/jquery-3.1.1.min.js"></script>
    <script charset="utf-8" src="/r_backend/js/modernizr.custom.js"></script>
    <script charset="utf-8" src="/r_backend/js/classie.js"></script>
    <script charset="utf-8" src="/r_backend/js/modalEffects.js"></script>


    <script>
        $(function(){
            var btn = $('#btn');
            var form = $('#form');
            btn.click(function(){
                form.submit();
            })
        })
    </script>
@stop

