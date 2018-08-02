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
                            <li ><span>运营管理</span></li>
                            <li class="active"><span>工作流管理</span></li>
                        </ol>
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
                                        <h3><p>添加流程</p></h3>
                                        @include('backend.layout.alert_info')
                                        <div class="panel-group accordion" id="operation">
                                            <div class="panel panel-default">
                                                <form action="{{ url('/backend/status/add_status_submit') }}" method="post" id="form">
                                                    {{ csrf_field() }}
                                                    <table id="user" class="table table-hover" style="clear: both">
                                                        <tbody>
                                                        <tr>
                                                            <td>所属工作流</td>
                                                            <td>
                                                                <input type="text" value="{{ $flow_detail->flow_name }}" disabled class="form-control">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>流程名称</td>
                                                            <td><input type="text" placeholder="请输入流程名称" class="form-control"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>方法路由名称</td>
                                                            <td>
                                                                <select name="" id="" class="form-control">
                                                                    <option value="">选择方法</option>
                                                                    @foreach($route_list as $value)
                                                                        <option value="">{{ $value->route_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>判断状态表</td>
                                                            <td>
                                                                <select name="" id="" class="form-control">
                                                                    <option value="">选择状态表</option>
                                                                    @foreach($table_field_list as $value)
                                                                        <option value="">{{ $value->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>选择前置节点</td>
                                                            <td>
                                                                <select name="" id="" class="form-control">
                                                                    <option value="">请选择节点</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>选择执行后状态</td>
                                                            <td>
                                                                <select name="" id="" class="form-control">
                                                                    <option value="">请选择节点</option>
                                                                </select>
                                                            </td>
                                                        </tr>

                                                        <input type="text" id="status-root-id" name="status_root_id" value="0" hidden>
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
                if(status_name_val == ''){
                    alert('请输入状态名称');
                    return false;
                }else if(field_id_val == 0){
                    alert('选择表');
                    return false;
                }else if(status_father_id_val == 0){
                    alert('请选择父状态');
                    return false;
                }else {
                    form.submit();
                }
            })
        })

    </script>
@stop

