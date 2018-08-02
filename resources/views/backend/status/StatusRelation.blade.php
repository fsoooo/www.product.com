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
                            <li class="active"><span>状态管理</span></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    <li><a href="{{ url('/backend/status/index') }}">状态列表</a></li>
                                    <li class="active"><a href="{{ url('/backend/sms/hasSend') }}">新增状态</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        <h3><p>添加状态</p></h3>
                                        @include('backend.layout.alert_info')
                                        <div class="panel-group accordion" id="operation">
                                            <div class="panel panel-default">
                                                <form action="{{ url('/backend/status/add_status_relation_submit') }}" method="post" id="form">
                                                    {{ csrf_field() }}
                                                    <table id="user" class="table table-hover" style="clear: both">
                                                        <tbody>
                                                        <tr>
                                                            <td>选择分组</td>
                                                            <td>
                                                                <select name="" id="select-group" class="form-control">
                                                                    <option value="-1">请选择分组</option>
                                                                    @foreach($group_list as $value)
                                                                        <option value="{{ $value->id }}">{{ $value->group_name }}</option>
                                                                    @endforeach
                                                                    <option value="0">其他未分组状态</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr id="parent-status-block" hidden>
                                                            <td>父级状态</td>
                                                            <td>
                                                                <select name="" id="parent-status" class="form-control">
                                                                    
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr id="children-status-block" hidden>
                                                            <td>子级状态</td>
                                                            <td>
                                                                <select name="status_id" id="children-status"  class="form-control"></select>
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
            var select_group = $('#select-group');
            var parent_status_block = $('#parent-status-block');
            var children_status_block = $('#children-status-block');
            var parent_status = $('#parent-status');
            var children_status = $('#children-status');
            function parent_block_hide()
            {
                parent_status_block.attr('hidden','');
            }
            function children_block_hide()
            {
                children_status_block.attr('hidden','');
            }
            select_group.change(function(){
                var select_group_val = $("#select-group option:selected").val();
                if(select_group_val == -1){
                    parent_block_hide();
                    children_block_hide();
                }else{
                    $.ajax({
                        type: "post",
                        dataType: "json",
                        async: true,
                        url: "/backend/status/get_status_ajax",
                        data: 'group_id='+select_group_val,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        },
                        success: function(data){
                            var status = data['status'];
                            var data = data['data'];
                            if(status == 200){
                                var len = data.length;
                                var str = '<option value=-1>选择父级状态</option>';
                                for(var i=0;i<len;i++){
                                    str += '<option value="'+data[i]['id']+'">'+data[i]['status_name']+'</option>';
                                }
                                parent_status_block.removeAttr('hidden');
                                parent_status.html(str);
                            }else {
                                alert('错误');
                            }
                        },error: function () {
                            alert('错误');
                        }
                    });
                }
            })
            parent_status.change(function(){
                var parent_status_val = $('#parent-status option:selected').val();
                if(parent_status_val == 0){
                    children_block_hide();
                }else {
                    $.ajax({
                        type: "post",
                        dataType: "json",
                        async: true,
                        url: "/backend/status/get_children_status_ajax",
                        data: 'id='+parent_status_val,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        },
                        success: function(data){
                            var status = data['status'];
                            var data = data['data'];
                            if(status == 200){
                                var len = data.length;
                                var str = '<option value=0>选择子级状态</option>';
                                for(var i=0;i<len;i++){
                                    str += '<option value="'+data[i]['id']+'">'+data[i]["status_name"]+'</option>';
                                }
                                children_status_block.removeAttr('hidden');
                                children_status.html(str);
                            }else {
                                alert(data);
                            }
                        },error: function () {
                            alert('错误');
                        }
                    });
                }
            })

            var btn = $('#btn');
            var form = $('#form');
            btn.click(function(){
                var parent_status_val = $('#parent-status option:selected').val();
                var children_status_val = $('#children-status option:selected').val();
                if(parent_status_val != -1&&children_status_val != 0){
                    form.submit();
                }else {
                    alert('请选择参数');
                    return false;
                }


            })


        })
    </script>
@stop

