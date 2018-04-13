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
                            <li><span>工作流管理</span></li>
                            <li class="active"><span>节点</span></li>
                        </ol>
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




        })

    </script>
@stop

