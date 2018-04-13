@extends('backend.layout.base')
@section('content')
    <div id="content-wrapper">
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
                                    <li><a href="{{ url('/backend/status/group') }}">状态分组</a></li>
                                    <li class="active"><a href="#">添加状态分组</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        <h3><p>添加状态分组</p></h3>
                                        @include('backend.layout.alert_info')
                                        <div class="panel-group accordion" id="operation">
                                            <div class="panel panel-default">
                                                <form action="{{ url('/backend/status/add_group_submit') }}" method="post"  onsubmit="return doAddGroup()">
                                                    {{ csrf_field() }}
                                                    <table id="user" class="table table-hover" style="clear: both">
                                                        <tbody>
                                                        <tr>
                                                            <td>分组名称</td>
                                                            <td>
                                                                <input type="text" class="form-control" placeholder="请输入状态名称" name="group_name" id="group_name">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>分组描述</td>
                                                            <td>
                                                                <input type="text" class="form-control" placeholder="请输入分组描述" name="group_describe" id="group_describe">
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                    <input type="submit" class="btn btn-success" value="确认添加">
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
    <script>
        function doAddGroup(){
            var group_name = document.getElementById('#group_name').val();
            var group_describe = document.getElementById('#group_describe').val();
                if(group_name == ''){
                    alert('分组名称不能为空');
                    return false;
                }
                if(group_describe == ''){
                    alert('请输入分组描述');
                    return false;
                }
                    return true;

        }
    </script>
@stop

