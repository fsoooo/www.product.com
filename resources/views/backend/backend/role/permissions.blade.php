@extends('backend.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('r_backend/css/libs/nifty-component.css')}}"/>
@endsection
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2 class="pull-left">权限列表</h2>
                    <div class="filter-block pull-right" style="margin-right: 20px;">
                        <button class="md-trigger btn btn-primary mrg-b-lg" data-modal="modal-8">新建权限</button>
                    </div>
                </header>
                @include('backend.layout.alert_info')
                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        <table class="table user-list table-hover">
                            <thead>
                            <tr>
                                <th><span>权限名称</span></th>
                                <th class="text-center"><span>可读名称</span></th>
                                <th class="text-center"><span>权限描述</span></th>
                                <th class="text-center"><span>创建时间</span></th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($permissions as $k => $v)
                                <tr>
                                    <td>
                                        {{$v->name}}
                                    </td>
                                    <td class="text-center">
                                        {{$v->display_name}}
                                    </td>
                                    <td class="text-center">
                                        {{$v->description}}
                                    </td>
                                    <td class="text-center">
                                        {{$v->created_at}}
                                    </td>
                                    <td style="width: 15%;">
                                        <a href="#" class="table-link">
                                        <span class="fa-stack">
                                            <i class="fa fa-square fa-stack-2x"></i>
                                            <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                                        </span>
                                        </a>
                                        <a href="omitpower?id={{$v['id']}}" class="table-link danger">
                                        <span class="fa-stack">
                                            <i class="fa fa-square fa-stack-2x"></i>
                                            <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                        </span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                    {{--分页--}}
                    <div style="text-align: center;">
                        {{ $permissions->links() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="md-modal md-effect-8 md-hide" id="modal-8">
            <div class="md-content">
                <div class="modal-header">
                    <button class="md-close close">×</button>
                    <h4 class="modal-title">权限添加</h4>
                </div>
                <div class="modal-body">
                    <form role="form" id="add_role" action='{{url('backend/role/post_add_permission')}}' method="post">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="exampleInputEmail1">权限名称</label>
                            <input class="form-control" name="name" placeholder="权限唯一英文名称" type="text">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">可读名称</label>
                            <input class="form-control" name="display_name" placeholder="权限可读中文名称" type="text">
                        </div>
                        <div class="form-group">
                            <label for="exampleTextarea">权限描述</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="权限相关描述信息"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="form-submit" class="btn btn-primary">确认提交</button>
                </div>
            </div>
        </div>
        <div class="md-overlay"></div>
    </div>
@stop
@section('foot-js')
    <script charset="utf-8" src="/r_backend/js/modernizr.custom.js"></script>
    <script charset="utf-8" src="/r_backend/js/classie.js"></script>
    <script charset="utf-8" src="/r_backend/js/modalEffects.js"></script>
    <script>
        $(function(){
            $submit = $("#form-submit");
            $submit.click(function(){
                var $name = $("input[name=name]").val();
                var $name_role = /[a-zA-Z\s\-_.]{5,50}$/;
                var $name_check = $name_role.test($name);
                if(!$name_check){
                    $("input[name=name]").val('');
                    $("input[name=name]").parent().addClass("has-error");
                }else{
                    $("input[name=name]").parent().removeClass("has-error");
                }

                var $display_name = $("input[name=display_name]").val();
                var $display_role = /^[\u4e00-\u9fa5\s]{5,100}$/;
                var $display_check = $display_role.test($display_name);
                if(!$display_check){
                    $("input[name=display_name]").val('');
                    $("input[name=display_name]").parent().addClass("has-error");
                }else{
                    $("input[name=display_name]").parent().removeClass("has-error");
                }

                var $description = $("#description").val();
                var $description_role = /[0-9A-Za-z\u4e00-\u9fa5\s]{5,100}$/;
                var $description_check = $description_role.test($description);
                if(!$description_check){
                    $("#description").val('');
                    $("#description").parent().addClass("has-error");
                }else{
                    $("#description").parent().removeClass("has-error");
                }

                if($name_check && $display_check && $description_check){
                    $("#add_role").submit();
                }else{
                    alert("请根据输入框提示填写相关内容");
                }

            })
        })
    </script>
@stop

