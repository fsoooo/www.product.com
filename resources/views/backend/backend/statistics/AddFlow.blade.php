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
                            <li class="active"><span>工作流管理</span></li>
                        </ol>
                        <h1>添加工作流</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    <li><a href="{{ url('/backend/flow/index') }}">工作流</a></li>
                                    <li class="active"><a href="{{ url('/backend/flow/add_flow') }}">新增</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        <h3><p>添加工作流</p></h3>
                                        @include('backend.layout.alert_info')
                                        <div class="panel-group accordion" id="operation">
                                            <div class="panel panel-default">
                                                <form action="{{ url('/backend/flow/add_flow_submit') }}" method="post" id="form">
                                                    {{ csrf_field() }}
                                                    <table id="user" class="table table-hover" style="clear: both">
                                                        <tbody>
                                                        <tr>
                                                            <td>工作流名称</td>
                                                            <td>
                                                                <input type="text" class="form-control" placeholder="输入工作流名称" name="flow_name">
                                                            </td>
                                                        </tr>
                                                        {{--<tr>--}}
                                                            {{--<td>方法路由名称</td>--}}
                                                            {{--<td>--}}
                                                                {{--<select name="" id="" class="form-control">--}}
                                                                    {{--<option value="">选择方法</option>--}}
                                                                    {{--@foreach($route_list as $value)--}}
{{--                                                                        <option value="">{{ $value->name }}</option>--}}
                                                                    {{--@endforeach--}}
                                                                {{--</select>--}}
                                                            {{--</td>--}}
                                                        {{--</tr>--}}
                                                        {{--<tr>--}}
                                                            {{--<td>判断状态表</td>--}}
                                                            {{--<td>--}}
                                                                {{--<select name="" id="" class="form-control">--}}
                                                                    {{--<option value="">选择状态表</option>--}}
                                                                    {{--@foreach($table_field_list as $value)--}}
                                                                        {{--<option value="">{{ $value->describe }}</option>--}}
                                                                    {{--@endforeach--}}
                                                                {{--</select>--}}
                                                            {{--</td>--}}
                                                        {{--</tr>--}}
                                                        {{--<tr>--}}
                                                            {{--<td>执行条件</td>--}}
                                                            {{--<select name="" id="">--}}
                                                                {{--<option value="">选择条件</option>--}}
                                                            {{--</select>--}}
                                                        {{--</tr>--}}
                                                        {{--<btton>添加条件</btton>--}}

                                                        {{--<tr>--}}
                                                            {{--<td></td>--}}
                                                        {{--</tr>--}}
                                                        {{--<tr>--}}
                                                            {{--<td>选择方式</td>--}}
                                                            {{--<td>--}}
                                                                {{--<select name="" id="select-type" class="form-control">--}}
                                                                    {{--<option value="">选择表字段方式</option>--}}
                                                                    {{--<option value="1">通过表，字段名查找</option>--}}
                                                                    {{--<option value="2">通过备注查找</option>--}}
                                                                {{--</select>--}}
                                                            {{--</td>--}}
                                                        {{--</tr>--}}
                                                        {{--<tr id="select-by-table" hidden>--}}
                                                            {{--<td>选择表名</td>--}}
                                                            {{--<td>--}}
                                                                {{--<select name="" id='tables-block' class="form-control">--}}

                                                                {{--</select>--}}
                                                            {{--</td>--}}
                                                        {{--</tr>--}}
                                                        {{--<input type="text" id="status-root-id" name="status_root_id" value="0" hidden>--}}
                                                        {{--<tr id="select-by-describe">--}}
                                                            {{--<td>选择对应表</td>--}}
                                                            {{--<td>--}}
                                                                {{--<select name="field_id" id="tables" class="form-control">--}}
                                                                    {{--<option value="0">选择对应表</option>--}}
                                                                    {{--@foreach($tables as $value)--}}
                                                                        {{--<option value="{{ $value->id }}">{{ $value->describe }}</option>--}}
                                                                    {{--@endforeach--}}
                                                                {{--</select>--}}
                                                            {{--</td>--}}
                                                        {{--</tr>--}}
                                                       {{--<tr>--}}
                                                           {{--<td>选择父状态</td>--}}
                                                           {{--<td>--}}
                                                               {{--<select name="status_father_id" id="father-status" class="form-control">--}}
                                                                   {{--<option value="0">选择父状态,如果不选，则默认为最高层级</option>--}}
                                                                   {{--@if($count != 0)--}}
                                                                        {{--@foreach($status_list as $value)--}}
                                                                           {{--<option value="{{ $value->id }}">{{ $value->status_name }}</option>--}}
                                                                       {{--@endforeach--}}
                                                                   {{--@endif--}}
                                                               {{--</select>--}}
                                                           {{--</td>--}}
                                                       {{--</tr>--}}
                                                      {{--<tr id="second-status-block">--}}
                                                          {{--<td></td>--}}
                                                          {{--<td>--}}
                                                              {{--<select name="" id="second-status" class="form-control"></select>--}}
                                                          {{--</td>--}}
                                                      {{--</tr>--}}
                                                        {{--<tr>--}}
                                                            {{--<td width="15%">状态描述</td>--}}
                                                            {{--<td width="60%">--}}
                                                                {{--<input type="text" class="form-control" placeholder="输入状态的描述信息" name="describe">--}}
                                                            {{--</td>--}}
                                                        {{--</tr>--}}
                                                        </tbody>
                                                    </table>
                                                </form>
                                            </div>
                                            <button id="btn" class="btn btn-success">提交</button>
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

            var select_by_table = $('#select-by-table');
            var select_by_describe = $('#select-by-describe');
            var table_block = $('table-block');
            var describe_block = $('#describe-block');


            var status_root_id = $('#status-root-id');
            var father_status = $("#father-status");
            var second_status_block = $("#second-status-block");
            var second_status = $('#second-status');
            father_status.change(function()
            {
                var father_status_val = $("#father-status option:selected").val();
                if(father_status_val == 0){
                    second_status.removeAttr('name');
                    father_status.attr('name','status_father_id');
                }else {
                    status_root_id.val(father_status_val);
                    //                发送ajax,查询子状态
                    $.ajax({
                        type: "post",
                        dataType: "json",
                        async: true,
                        url: "/backend/classify/get_classify",
                        data: 'fid='+father_status_val,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        },
                        success: function(data){
                            var status = data['status'];
                            var data = data['data'];
                            console.log(data);
                            if(status == 200){

                                father_status.removeAttr('name');
                                second_status.attr('name','status_father_id');
                                var len = data.length;
                                var str = '';
                                for(var i=0;i<len;i++){
                                    str += '<option value="'+data[i]['id']+'">'+data[i]['status_name']+'</option>';
                                }
                                second_status.html(str);
                            }else {

                            }
                        },error: function () {
                            alert('发送失败');
                        }
                    });
                }
            })

            //进行验证发送
            var status_name = $('input[name=status_name]');
            var field_id = $('input[name=field_id]');
            var status_father_id = $('input[name=status_father_id]');
            var btn = $('#btn');
            var form = $('#form');
            btn.click(function(){
                var status_name_val = status_name.val();
                var field_id_val = $('#tables option:selected').val();
                var status_father_id_val = $('input[name=status_father_id]:selected').val();
                //状态名称验证
//                if(status_name_val == ''){
//                    alert('请输入状态名称');
//                    return false;
//                }else if(field_id_val == 0){
//                    alert('选择表');
//                    return false;
//                }else if(status_father_id_val == 0){
//                    alert('请选择父状态');
//                    return false;
//                }else {
//                    form.submit();
//                }
                form.submit();
            })
        })

    </script>
@stop

