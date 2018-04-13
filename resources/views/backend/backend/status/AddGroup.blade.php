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
                        <h1>添加状态分组</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    <li><a href="{{ url('/backend/status/index') }}">状态列表</a></li>
                                    <li class="active"><a href="{{ url('/backend/status/group') }}">状态分组</a></li>
                                    {{--<li class="active"><a href="#">添加状态分组</a></li>--}}
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        <h3><p>添加状态分组</p></h3>
                                        @include('backend.layout.alert_info')
                                        <div class="panel-group accordion" id="operation">
                                            <div class="panel panel-default">
                                                <form action="{{ url('/backend/status/add_group_submit') }}" method="post" id="form">
                                                    {{ csrf_field() }}
                                                    <table id="user" class="table table-hover" style="clear: both">
                                                        <tbody>
                                                        <tr>
                                                            <td>分组名称</td>
                                                            <td>
                                                                <input type="text" class="form-control" placeholder="请输入状态名称" name="group_name">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>分组描述</td>
                                                            <td>
                                                                <input type="text" class="form-control" placeholder="请输入分组描述" name="group_describe">
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
                    </p>fu
                </footer>
            </div>
        </div>
    </div>
    <script src="/js/jquery-3.1.1.min.js"></script>
    <script>
        $(function(){
            var btn = $('#btn');
            var group_name = $('input[name=group_name]');
            var group_describe = $('input[name=group_describe]');
            var form = $('#form');

            btn.click(function(){//提交表单，进项验证
                var group_name_val = group_name.val();
                var group_describe_val = group_describe.val();
                if(group_name_val == ''){
                    alert('分组名称不能为空');
                    return false;
                }else if(group_describe_val == ''){
                    alert('请输入分组描述');
                    return false;
                }
                form.submit();
            })
        })
    </script>
@stop

