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
                            <li class="active"><span>售后管理</span></li>
                            <li class="active"><span>查看保单</span></li>
                        </ol>
                        <h1>线下保单录入</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    <li><a href="{{ url('/backend/warranty/add_warranty/personal') }}">个人保单录入</a></li>
                                    <li class="active"><a href="#">团体保单录入</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        <h3><p>保单录入</p></h3>
                                        @include('backend.layout.alert_info')
                                        <div class="panel-group accordion" id="operation">
                                            <div class="panel panel-default">
                                                <form id="warranty-form" action="{{ url('/backend/warranty/add_warranty_submit') }}" method="post">
                                                    {{ csrf_field() }}
                                                    <table id="user" class="table table-hover" style="clear: both">
                                                        <tbody>
                                                        <tr>
                                                            <td width="15%">保单编号</td>
                                                            <td width="60%">
                                                                <input type="text" class="form-control" placeholder="请输入保单编号" name="warranty_id">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>所属保险公司</td>
                                                            <td>
                                                                <select name="" id="" class="form-control">
                                                                    <option value="">请选择保险公司</option>
                                                                    @foreach($company_list as $value)
                                                                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>产品</td>
                                                            <td>
                                                                <select name="" id="" class="form-control">
                                                                    <option value="">选择产品</option>

                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>现金价值</td>
                                                            <td>
                                                                <input type="text" placeholder="请输入现金价值，单位每人每天" name="worth" class="form-control">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td></td>
                                                        </tr>
                                                        <tr id="designated-time-block" hidden>
                                                            <td>选择时间</td>
                                                            <td><input id="designated-time" class="form-control" type="datetime-local" name="designated-time"/></td>
                                                        </tr>
                                                        </tbody>
                                                    </table>

                                            </div>
                                        </div>
                                        <table class="table">
                                            <tr>
                                                <td style="width: 15%;">投保人是被保人的</td>
                                                <td style="width: 60%;">
                                                    <select name="relation_type" id="relation-type" class="form-control">
                                                        <option value="">选择关系</option>
                                                        @foreach($relation_list as $value)
                                                            <option value="{{$value->relation_name}}">{{ $value->relation_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>
                                        {{--</form>--}}


                                        <h3><p>投保人信息</p></h3>
                                        <div class="panel-group accordion" id="operation">
                                            <div class="panel panel-default">
                                                {{--<form id="policy-form">--}}
                                                    <table id="user" class="table table-hover" style="clear: both">
                                                        <tbody>
                                                        <tr>
                                                            <td width="15%">姓名</td>
                                                            <td width="60%">
                                                                <input type="text" class="form-control" placeholder="请输入保单编号" name="policy_name">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>证件类型</td>
                                                            <td>
                                                                <select name="policy_code_type" id="" class="form-control">
                                                                    <option value="">请选择证件</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>证件号码</td>
                                                            <td><input type="text" name="policy_code"  class="form-control"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>手机号码</td>
                                                            <td><input type="text" name="policy_phone"  class="form-control"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>电子邮箱</td>
                                                            <td><input type="text" name="policy_email" class="form-control"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>职业</td>
                                                            <td></td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                {{--</form>--}}
                                            </div>
                                        </div>

                                        <div id="recognizee-block">
                                            <h3><p>被保人信息</p></h3>
                                            <div class="panel-group accordion" id="operation">
                                                <div class="panel panel-default">

                                                    <tr>
                                                        <td>被保人表格上传</td>
                                                        <td><input type="text"></td>
                                                    </tr>

                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <button id="btn" class="btn btn-success">添加</button>
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
            var relation_type = $('#relation-type');
            var recognizee_block = $('#recognizee-block');

            //各个form表单xinxi
            var warranty_form = $('#warranty-form');

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

