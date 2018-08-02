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
                        <h1>添加节点</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    <li><a href="{{ url('/backend/flow/node') }}">节点列表</a></li>
                                    <li class="active"><a href="{{ url('backend/flow/add_node') }}">新增</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        <h3><p>添加工作流</p></h3>
                                        @include('backend.layout.alert_info')
                                        <div class="panel-group accordion" id="operation">
                                            <div class="panel panel-default">
                                                <form action="{{ url('/backend/flow/add_node_submit') }}" method="post" id="form">
                                                    {{ csrf_field() }}
                                                    <table id="user" class="table table-hover" style="clear: both">
                                                        <tbody>
                                                        <tr>
                                                            <td>节点名称</td>
                                                            <td>
                                                                <input type="text" class="form-control" placeholder="输入节点名称" name="node_name">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>方法路由名称</td>
                                                            <td>
                                                                <select name="route_id" id="route-id" class="form-control">
                                                                    <option value="0">选择方法</option>
                                                                    @foreach($route_list as $value)
                                                                        <option value="{{ $value->id }}">{{ $value->route_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>所属工作流</td>
                                                            <td>
                                                                <select name="flow_id" id="flow-id" class="form-control">
                                                                    <option value="0">请选择工作流</option>
                                                                    @foreach($flow_list as $value)
                                                                        <option value="{{ $value->id }}">{{ $value->flow_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>节点描述</td>
                                                            <td>
                                                                <input type="text" name="describe" class="form-control" placeholder="请输入描述">
                                                            </td>
                                                        </tr>
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


            var node_name = $('input[name=node_name]');
            var route_id = $('input[name=route_id]');
            var flow_id = $('input[name=flow_id]');
            var form = $('#form');
            //进行验证提交
            var btn = $('#btn');
            btn.click(function(){
                var node_name_val = node_name.val();
                var route_id_val = $('#route-id option:selected').val();
                var flow_id_val = $('#flow_id option:selected').val();
                if(node_name_val == ''){
                    alert('请输入节点名称');
                    return false;
                }else if(route_id_val == '0'||flow_id_val == '0'){
                    alert('请选择参数');
                    return false;
                }
                form.submit();
            })





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

