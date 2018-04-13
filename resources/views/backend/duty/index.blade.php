@extends('backend.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('r_backend/css/libs/nifty-component.css')}}"/>
@endsection
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="/backend/">主页</a></li>
                <li ><span>产品管理</span></li>
                <li class="active"><span>责任</span></li>
            </ol>
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2 class="pull-left">责任列表</h2>

                    <div class="filter-block pull-right" style="margin-right: 20px;">
                        <button class="md-trigger btn btn-primary mrg-b-lg" data-modal="modal-8">新建责任</button>
                    </div>
                </header>
                <div class="main-box-body clearfix">
                    <form action='{{url('backend/product/duty/')}}' method="get">
                        责任名称: <input name="name">&nbsp;
                        <input type="submit" id="search" value="搜索">
                    </form>
                </div>
                @include('backend.layout.alert_info')
                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        <table class="table user-list table-hover">

                            <thead>
                            <tr>
                                <th class="text-center"><span>责任名称</span></th>
                                <th class="text-center">分类名称</th>
                                <th class="text-center">所属类型</th>
                                <th class="text-center" style="width: 20%"><span>责任描述</span></th>
                                    <th>操作</th>
                            </tr>
                            </thead>
                            <tbody id="tbody">
                            @foreach($duty as $k => $v)
                                <tr>
                                    <td class="text-center">
                                        {{$v->name}}
                                    </td>
                                    <td class="text-center">
                                        {{$v->category->name}}
                                    </td>
                                    <td class="text-center">
                                        @if($v->type == 'main')
                                            主险责任
                                        @else
                                            附加险责任
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{$v->description}}
                                    </td>

                                    <td style="width: 15%;">
                                        <a href="{{url('backend/product/duty/update/'.$v->id)}}" class="table-link">
                                        <span class="fa-stack">
                                            <i class="fa fa-square fa-stack-2x"></i>
                                            <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                                        </span>
                                        </a>
                                        <a href="{{url('backend/product/duty/delete/'.$v->id)}}" class="table-link danger">
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
                        {{ $duty->appends(['name' => $name])->links() }}
                    </div>

                </div>
            </div>
        </div>

        <div class="md-modal md-effect-8 md-hide" id="modal-8">
            <div class="md-content">
                <div class="modal-header">
                    <button class="md-close close">×</button>
                    <h4 class="modal-title">责任添加</h4>
                </div>
                <div class="modal-body">
                    <form role="form" id="add_duty" action='{{url('backend/product/duty/add')}}' method="post">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="exampleInputEmail1"><span class="red">*</span>责任名称</label>
                            <input class="form-control" name="name" placeholder="责任名称（5-50个字符长度）" type="text">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1"><span class="red">*</span>责任描述</label>
                            <input class="form-control" name="description" placeholder="责任描述（5-100个字符长度）" type="text">
                        </div>
                        <div class="form-group">
                            <label for="exampleTextarea"><span class="red">*</span>责任分类</label>
                            <select name="category_id" id="" class="form-control">
                                @foreach($categories as $k => $v)
                                    <option value="{{$v->id}}"><?php echo str_repeat('|----' , $v['sort']) . $v['name'] ?></option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="exampleTextarea"><span class="red">*</span>责任类型</label>
                            <select name="type" id="" class="form-control">
                                    <option value="main">主险责任</option>
                                    <option value="attach">附加险责任</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="exampleTextarea"><span class="red">*</span>关联基础保额</label>
                            <select name="need_coverage" id="" class="form-control">
                                <option value="1">需要</option>
                                <option value="0">不需要</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="exampleTextarea"><span class="red">*</span>责任详情</label>
                            <textarea class="form-control" id="detail" name="detail" rows="3" placeholder="责任详情信息"></textarea>
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
//                var $name = $("input[name=name]").val();
//                var $name_role = /^[（）《》/\u4e00-\u9fa5a-zA-Z\s\-_()]{5,50}$/;
//                var $name_check = $name_role.test($name);
//                changeClass($("input[name=name]"), $name_check);
//
//                var $description = $("input[name=description]").val();
//                var $description_role = /^[（）《》/\u4e00-\u9fa5a-zA-Z\s\-_()]{5,100}$/;
//                var $description_check = $description_role.test($description);
//                changeClass($("input[name=description]"), $description_check);
//
//                var $detail = $("#detail").val();
//                var $detail_role = /^[（）/\u4e00-\u9fa5a-zA-Z\s\-_()]{5,}$/;
//                var $detail_check = $detail_role.test($detail);
//                changeClass($("#detail"), $detail_check);
//
//                function changeClass(obj, result){
//                    if(!result){
//                        obj.val('');
//                        obj.parent().addClass("has-error");
//                    }else{
//                        obj.parent().removeClass("has-error");
//                    }
//                }




//                if($name_check && $description_check && $detail_check){
                    $("#add_duty").submit();
//                }else{
//                    alert("请根据输入框提示填写相关内容");
//                }

            })
        })
    </script>
@stop

