@extends('backend.layout.base')
@section('css')
<link rel="stylesheet" type="text/css" href="{{asset('r_backend/css/libs/nifty-component.css')}}"/>
@endsection
@section('content')
<div id="content-wrapper">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-12">
                <ol class="breadcrumb">
                    <li><a href="{{ url('/backend') }}">主页</a></li>
                    <li ><span>销售管理</span></li>
                    <li ><span>代理人渠道管理</span></li>
                    <li><span><a href="/backend/sell/ditch_agent/agents">代理人管理</a></span></li>
                    <li class="active"><span>代理人列表</span></li>
                </ol>
            </div>
        </div>
        <div class="main-box clearfix">
            <header class="main-box-header clearfix">
                <h2 class="pull-left">代理人列表</h2>
                <div class="filter-block pull-right" style="margin-right: 20px;">
                    <button class="md-trigger btn btn-primary mrg-b-lg" data-modal="modal-8">新建代理人</button>
                </div>
            </header>
            @include('backend.layout.alert_info')
            <div class="main-box-body clearfix">
                <div class="table-responsive">
                    <table class="table user-list table-hover">
                        <thead>
                        <tr>
                            <th><span>代理人名称</span></th>
                            <th class="text-center"><span>代理人全称</span></th>
                            <th class="text-center"><span>联系地址</span></th>
                            <th class="text-center"><span>联系电话</span></th>
                            <th class="text-center"><span>状态</span></th>
                            {{--<th>操作</th>--}}
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($agents as $k => $v)
                        <tr>
                            <td>
                                {{$v->user->name}}
                            </td>
                            <td class="text-center">
                                {{$v->user->real_name}}
                            </td>
                            <td class="text-center">
                                {{$v->user->address}}
                            </td>
                            <td class="text-center">
                                {{$v->user->phone}}
                            </td>
                            <td class="text-center">
                                <?php
                                    switch ($v->status){
                                        case 'on':
                                            echo "<span class=\"label label-success\">启用</span>";
                                            break;
                                        case 'off':
                                            echo "<span class=\"label label-warning\">禁用</span>";
                                            break;
                                    }
                                ?>
                            </td>
                            {{--<td style="width: 15%;">--}}
                                {{--<a href="#" class="table-link">--}}
                                    {{--<span class="fa-stack">--}}
                                        {{--<i class="fa fa-square fa-stack-2x"></i>--}}
                                        {{--<i class="fa fa-pencil fa-stack-1x fa-inverse"></i>--}}
                                    {{--</span>--}}
                                {{--</a>--}}
                                {{--<a href="#" class="table-link danger">--}}
                                    {{--<span class="fa-stack">--}}
                                        {{--<i class="fa fa-square fa-stack-2x"></i>--}}
                                        {{--<i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>--}}
                                    {{--</span>--}}
                                {{--</a>--}}
                            {{--</td>--}}
                        </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>
                {{--分页--}}
                <div style="text-align: center;">
                    {{ $agents->links() }}
                </div>

            </div>
        </div>
    </div>

    <div class="md-modal md-effect-8 md-hide" id="modal-8">
        <div class="md-content">
            <div class="modal-header">
                <button class="md-close close">×</button>
                <h4 class="modal-title">代理人添加</h4>
            </div>
            <div class="modal-body">
                <form role="form" id="add_agent" action='{{url('backend/sell/ditch_agent/post_add_agent')}}' method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="exampleInputEmail1">代理人简称 <span class="red">*</span></label>
                        <input class="form-control" name="name" placeholder="代理人别称（2-4汉字）" type="text">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">代理人真实全称<span class="red">*</span></label>
                        <input class="form-control" name="real_name" placeholder="代理人全称（2-4汉字）" type="text">
                    </div>
                    <div class="form-group">
                        <label for="exampleTextarea">身份证号<span class="red">*</span></label>
                        <input class="form-control" name="card_id" placeholder="代理人有效身份证号" type="text">
                    </div>
                    <div class="form-group">
                        <label for="exampleTextarea">工号<span class="red">*</span></label>
                        <input class="form-control" name="job_number" placeholder="代理人工号" type="text">
                    </div>
                    <div class="form-group">
                        <label for="exampleTextarea">区域<span class="red">*</span></label>
                        <input class="form-control" name="area" placeholder="代理人所在区域" type="text">
                    </div>
                    <div class="form-group">
                        <label for="exampleTextarea">职位<span class="red">*</span></label>
                        <input class="form-control" name="position" placeholder="代理人所在区域" type="text">
                    </div>
                    <div class="form-group">
                        <label for="exampleTextarea">联系地址</label>
                        <input class="form-control" name="address" placeholder="代理人联系地址" type="text">
                    </div>
                    <div class="form-group">
                        <label for="exampleTextarea">电子邮箱</label>
                        <input class="form-control" name="email" placeholder="代理人邮箱地址" type="email">
                    </div>
                    <div class="form-group">
                        <label for="exampleTextarea">手机号码<span class="red">*</span></label>
                        <input class="form-control" name="phone" placeholder="代理人联系电话" type="text">
                    </div>
                    <input id="lefile" type="file" style="display:none">
                    <div class="input-append">
                        <span>身份证正面：</span>
                        <input type="file" class="filestyle" name="card_img_front">
                    </div>
                    <div class="input-append">
                        <span>身份证反面：</span>
                        <input type="file" class="filestyle" name="card_img_backend">
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
            //简称
            var $name = $("input[name=name]").val();
            var $name_role = /^[\u4e00-\u9fa5]{2,4}$/;
            var $name_check = $name_role.test($name);
            changeClass($("input[name=name]"), $name_check);

            //全称
            var $real_name = $("input[name=real_name]").val();
            var $real_name_role = /^[\u4e00-\u9fa5]{2,4}$/;
            var $real_name_check = $real_name_role.test($real_name);
            changeClass($("input[name=real_name]"), $real_name_check);

            //身份证
            var $card_id = $("input[name=card_id]").val();
            var $card_id_role = /^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|Xx)$/;
            var $card_id_check = $card_id_role.test($card_id);
            changeClass($("input[name=card_id]"), $card_id_check);

            //电话
            var $phone = $("input[name=phone]").val();
            var $phone_role = /^1[34578]\d{9}$/;
            var $phone_check = $phone_role.test($phone);
            changeClass($("input[name=phone]"), $phone_check);



            function changeClass(obj, result){
                if(!result){
                    obj.val('');
                    obj.parent().addClass("has-error");
                }else{
                    obj.parent().removeClass("has-error");
                }

            }

            if($name_check && $real_name && $card_id_check){
                $("#add_agent").submit();
            }else{
                alert("请根据输入框提示填写相关内容");
            }
        })

    })
</script>
@stop

