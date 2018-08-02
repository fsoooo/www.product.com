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
                    <li><span>销售管理</span></li>
                    <li><span>代理人渠道管理</span></li>
                    <li><span><a href="/backend/sell/ditch_agent/ditches">渠道管理</a></span></li>
                    <li class="active"><span>渠道列表</span></li>
                </ol>
            </div>
        </div>
        <div class="main-box clearfix">
            <header class="main-box-header clearfix">
                <h2 class="pull-left">渠道列表</h2>
                <div class="filter-block pull-right" style="margin-right: 20px;">
                    <button class="md-trigger btn btn-primary mrg-b-lg" data-modal="modal-8">新建渠道</button>
                </div>
            </header>
            @include('backend.layout.alert_info')
            <div class="main-box-body clearfix">
                <div class="table-responsive">
                    <table class="table user-list table-hover">
                        <thead>
                        <tr>
                            <th><span>渠道名称</span></th>
                            <th class="text-center"><span>渠道全称</span></th>
                            <th class="text-center"><span>联系地址</span></th>
                            <th class="text-center"><span>组织机构代码</span></th>
                            <th class="text-center"><span>渠道类型</span></th>
                            <th class="text-center"><span>状态</span></th>
                            {{--<th>操作</th>--}}
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($ditches as $k => $v)
                        <tr>
                            <td>
                                {{$v->display_name}}
                            </td>
                            <td class="text-center">
                                {{$v->name}}
                            </td>
                            <td class="text-center">
                                {{$v->address}}
                            </td>
                            <td class="text-center">
                                {{$v->group_code}}
                            </td>
                            <td class="text-center">
                                <?php
                                    switch ($v->type){
                                        case 'external_group':
                                            echo "外部合作";
                                        break;
                                        case 'son_group':
                                            echo "附属公司";
                                        break;
                                        case 'internal_group':
                                            echo "内部分组";
                                        break;
                                    }
                                ?>
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
                    {{ $ditches->links() }}
                </div>

            </div>
        </div>
    </div>

    <div class="md-modal md-effect-8 md-hide" id="modal-8">
        <div class="md-content">
            <div class="modal-header">
                <button class="md-close close">×</button>
                <h4 class="modal-title">渠道添加</h4>
            </div>
            <div class="modal-body">
                <form role="form" id="add_ditch" action='{{url('backend/sell/ditch_agent/post_add_ditch')}}' method="post">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="exampleInputEmail1">渠道名称</label>
                        <input class="form-control" name="display_name" placeholder="渠道名称（4-100长度内汉字包括（）-_）" type="text">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">渠道全称</label>
                        <input class="form-control" name="name" placeholder="渠道全称（4-100长度内汉字包括（）-_）" type="text">
                    </div>
                    <div class="form-group">
                        <label for="exampleTextarea">联系地址</label>
                        <input class="form-control" name="address" placeholder="渠道联系地址（4-100长度内汉字包括（）-_）" type="text">
                    </div>
                    <div class="form-group">
                        <label for="exampleTextarea">渠道类型</label>
                        <select class="form-control" name="type" id="ditch_type">
                            <option value="external_group">外部合作</option>
                            <option value="son_group">附属公司</option>
                            <option value="internal_group">内部分组</option>
                        </select>
                    </div>
                    <div class='code hide'>
                        <div class="form-group">
                            <label for="exampleTextarea">组织机构代码</label>
                            <input class="form-control" name="group_code" placeholder="渠道组织机构代码" type="text">
                        </div>
                        <div class="form-group">
                            <label for="exampleTextarea">信用码</label>
                            <input class="form-control" name="credit_code" placeholder="渠道信用码" type="text">
                        </div>
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
        $("#ditch_type").change(function(){
            $('.code').removeClass('hide');
            var val = $(this).val();
            if(val == 'internal_group'){
                $('.code').addClass('hide');
            }
        });



        $submit = $("#form-submit");
        $submit.click(function(){
            var $name = $("input[name=name]").val();
            var $name_role = /^[\u4e00-\u9fa5\(\)-_]{4,100}$/;
            var $name_check = $name_role.test($name);
            if(!$name_check){
                $("input[name=name]").val('');
                $("input[name=name]").parent().addClass("has-error");
            }else{
                $("input[name=name]").parent().removeClass("has-error");
            }

            var $display_name = $("input[name=display_name]").val();
            var $display_role = /^[\u4e00-\u9fa5\(\)-_]{4,100}$/;
            var $display_check = $display_role.test($display_name);
            if(!$display_check){
                $("input[name=display_name]").val('');
                $("input[name=display_name]").parent().addClass("has-error");
            }else{
                $("input[name=display_name]").parent().removeClass("has-error");
            }

            var $address = $("input[name=address]").val();
            var $address_role = /[0-9A-Za-z\u4e00-\u9fa5\(\)-_]{4,100}$/;
            var $address_check = $address_role.test($address);
            if(!$address_check){
                $("input[name=address]").val('');
                $("input[name=address]").parent().addClass("has-error");
            }else{
                $("input[name=address]").parent().removeClass("has-error");
            }

            if($name_check && $display_check && $address_check){
                $("#add_ditch").submit();
            }else{
                alert("请根据输入框提示填写相关内容");
            }

        })
    })
</script>
@stop

