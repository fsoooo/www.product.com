@extends('backend.layout.base')
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
                            <li class="active"><span>订单录入</span></li>
                        </ol>
                        <h1>线下订单录入</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="">个险订单录入</a></li>
                                    <li><a href="{{ url('/backend/order/add_order/group') }}">团险订单录入</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        <h3><p>订单录入</p></h3>
                                        @include('backend.layout.alert_info')
                                        <div class="panel-group accordion" id="operation">
                                            <div class="panel panel-default">
                                                <form id="warranty-form" action="{{ url('/backend/order/add_order_submit') }}" method="post">
                                                    {{ csrf_field() }}
                                                    <table id="user" class="table table-hover" style="clear: both">
                                                        <tbody>

                                                        </tbody>
                                                    </table>

                                            </div>
                                        </div>
                                        <button id="btn" class="btn btn-success">添加</button>
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
    <script>
        $(function(){
            var relation_type = $('#relation-type');
            var recognizee_block = $('#recognizee-block');

            //各个form表单xinxi
            var warranty_form = $('#warranty-form');0
                    .toExponential(


                    )

            var btn = $('#btn');


            relation_type.change(function(){
                var relation_type_val = $('#relation-type option:selected').html();
                if(relation_type_val == '本人'){
                    recognizee_block.attr('hidden','');
                }else{
                    recognizee_block.removeAttr('hidden');
                }
            })

            btn.click(function(){
                var warranty_form_val = warranty_form.serialize();
                warranty_form.submit();
            })


        })

    </script>
@stop

