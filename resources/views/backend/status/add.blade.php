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
                                    <li class="active"><a href="#">新增状态</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        <h3><p>添加状态</p></h3>
                                        @include('backend.layout.alert_info')
                                        <div class="panel-group accordion" id="operation">
                                            <div class="panel panel-default">
                                                <form action="{{ url('/backend/status/add_status_submit') }}" method="post" id="form">
                                                    {{ csrf_field() }}
                                                    <table id="user" class="table table-hover" style="clear: both">
                                                        <tbody>
                                                        <tr>
                                                            <td>状态名称</td>
                                                            <td>
                                                                <input type="text" class="form-control" placeholder="请输入状态名称" name="status_name">
                                                            </td>
                                                        </tr>
                                                        <tr id="select-by-describe">
                                                            <td>选择对应表</td>
                                                            <td>
                                                                <select name="field_id" id="tables" class="form-control">
                                                                    <option value="0">选择对应表</option>
                                                                    @foreach($tables as $value)
                                                                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                        </tr>
                                                       <tr>
                                                           <td>所属状态分组</td>
                                                           <td>
                                                               <select name="group_id" id="group-id" class="form-control">
                                                                   <option value="0">选择状态分组</option>
                                                                   @if($status_count != 0)
                                                                        @foreach($status_group as $value)
                                                                           <option value="{{ $value->id }}">{{ $value->group_name }}</option>
                                                                       @endforeach
                                                                   @endif
                                                               </select>
                                                           </td>
                                                       </tr>
                                                        <tr>
                                                            <td width="15%">状态描述</td>
                                                            <td width="60%">
                                                                <input type="text" class="form-control" placeholder="输入状态的描述信息" name="describe">
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
    <script src="/js/check.js"></script>
    <script>
        $(function(){

            var btn = $('#btn');
            var status_name = $('input[name=status_name]');
            var tables = $('input[name=field_id]');
            var group_id = $('input[name=group_id]');




            var form = $('#form');
            btn.click(function(){
                var status_name_val = status_name.val();
                var tables_val = $('#tables option:selected').val();
                var group_id_val = $('#group-id option:selected').val();
                console.log(tables_val);
                if(status_name_val == ''){
                    alert('状态名称不能为空');
                    status_name.parent().addClass('has-error');
                    return false;
                }else {
                    status_name.parent().removeClass('has-error');
                }

                if(tables_val == '0'){
                    alert('表不能为空');
                    tables.parent().addClass('has-error');
                    return false;
                }else {
                    tables.parent().removeClass('has-error');
                }
                if(group_id_val == '0'){
                    alert('所属分组不能为空');
                    return false;
                }
                form.submit();
            })
        })
    </script>
@stop

