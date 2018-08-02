@extends('backend.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('r_backend/css/libs/nifty-component.css')}}"/>
@endsection
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="/backend/">主页</a></li>
                <li ><span>API参数</span></li>
                <li class="active"><span>银行信息</span></li>
            </ol>
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2 class="pull-left">银行列表</h2>
                    <div class="filter-block pull-right" style="margin-right: 20px;">
                        <div class="btn-group">
                            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                选择API来源 <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                @foreach($apis as $api)
                                    <li><a href="javascript:;" value="{{ $api->uuid }}" class="change-table">{{ $api->name }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                        <button class="md-trigger btn btn-primary" data-modal="modal-8">添加银行信息</button>
                    </div>
                </header>
                @include('backend.layout.alert_info')
                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        @foreach($apis as $api)
                            <table class="table user-list table-hover" id="table-{{ $api->uuid }}">
                                <thead>
                                <tr>
                                    <th>接口名</th>
                                    <th>接口唯一ID</th>
                                    <th>银行名</th>
                                    <th>银行编码</th>
                                    <th>银行代号</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(!empty($api->api_option))
                                    @foreach ($api->api_option as $bank)
                                        <tr>
                                            <td>{{ $api->name }}</td>
                                            <td>{{ $api->uuid }}</td>
                                            <td>{{ $bank->name }}</td>
                                            <td>{{ $bank->number }}</td>
                                            <td>{{ $bank->code }}</td>
                                            <td style="width: 15%;">
                                                {{--<a href="#" class="btn md-trigger add_son" data-modal="modal-9" id="{{$v['id']}}" name="{{$v['name']}}">--}}
                                                {{--<i class="fa fa-plus-circle fa-lg"></i>--}}
                                                {{--</a>--}}
                                                {{--<a href="#" class="table-link alter md-trigger" data-modal="modal-10" id="{{$v['id']}}" name="{{$v['name']}}" slug="{{$v->slug}}">--}}
                                                {{--<span class="fa-stack">--}}
                                                {{--<i class="fa fa-square fa-stack-2x"></i>--}}
                                                {{--<i class="fa fa-pencil fa-stack-1x fa-inverse"></i>--}}
                                                {{--</span>--}}
                                                {{--</a>--}}
                                                {{--<a href="category/omit?id={{$v['id']}}" class="table-link danger">--}}
                                                {{--<span class="fa-stack">--}}
                                                {{--<i class="fa fa-square fa-stack-2x"></i>--}}
                                                {{--<i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>--}}
                                                {{--</span>--}}
                                                {{--</a>--}}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        @endforeach
                    </div>


                </div>
            </div>
        </div>

        <div class="md-modal md-effect-8 md-hide" id="modal-8">
            <div class="md-content">
                <div class="modal-header">
                    <button class="md-close close">×</button>
                    <h4 class="modal-title">银行信息添加</h4>
                </div>
                <div class="modal-body">
                    <form role="form" id="form-store" action='{{ route('api_option.bank.store') }}' method="post">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="uuid">接口来源</label>
                            <select name="api_from_uuid" id="uuid" class="form-control">
                                @foreach($apis as $api)
                                    <option value="{{ $api->uuid }}">{{ $api->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="name">银行名称</label>
                            <input class="form-control" name="name" placeholder="银行名称（应少于255个字符长度）" type="text">
                        </div>
                        <div class="form-group">
                            <label for="number">银行编码</label>
                            <input class="form-control" name="number" placeholder="银行编码（应少于255个字符长度）" type="text">
                        </div>
                        <div class="form-group">
                            <label for="code">银行代号</label>
                            <input class="form-control" name="code" placeholder="银行代号（应少于255个字符长度）" type="text">
                        </div>
                        <div class="form-group">
                            <button type="submit" id="alter-submit" class="btn btn-primary">确认提交</button>
                        </div>
                    </form>
                </div>
                {{--<div class="modal-footer">--}}
                    {{--<button type="button" id="form-submit" class="btn btn-primary">确认提交</button>--}}
                {{--</div>--}}
            </div>
        </div>
        <div class="md-overlay"></div>
    </div>

    <div class="md-modal md-effect-8 md-hide" id="modal-10">
        <div class="md-content">
            <div class="modal-header">
                <button class="md-close close">×</button>
                <h4 class="modal-title">分类的修改</h4>
            </div>
            <div class="modal-body">
                <form role="form" id="alter_category" action='{{url('backend/product/category/alter')}}' method="get">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="exampleInputEmail1">分类名称</label>
                        <input id="alter_category_name" class="form-control" name="name" placeholder="保险公司名称（5-50个字符长度）" type="text">
                        <input id="alter_category_id" type="hidden" name="pid">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">唯一标识</label>
                        <input class="form-control" id="alter_slug" name="slug" placeholder="唯一标识缩略名（2-50个字符长度）" type="text">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="alter-submit" class="btn btn-primary">确认提交</button>
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
//            $submit = $("#form-submit");
//            $submit.click(function(){
//                var $name = $("input[name=name]").val();
//                var $name_role = /^[（）\u4e00-\u9fa5a-zA-Z\s\-_()]{4,50}$/;
//                var $name_check = $name_role.test($name);
//                changeClass($("input[name=name]"), $name_check);
//
//                var $slug = $("input[name=slug]").val();
//                var $slug_role = /^[（）\u4e00-\u9fa5a-zA-Z\s\-_()]{2,50}$/;
//                var $slug_check = $slug_role.test($slug);
//                changeClass($("input[name=slug]"), $slug_check);
//
//
//                function changeClass(obj, result){
//                    if(!result){
//                        obj.val('');
//                        obj.parent().addClass("has-error");
//                    }else{
//                        obj.parent().removeClass("has-error");
//                    }
//                }
//
//                if($name_check && $slug){
//                    $("#add_category").submit();
//                }else{
//                    alert("请根据输入框提示填写相关内容");
//                }
//
//            })


            // 隐藏所有表格
            $(".table").hide().eq(0).show();

            // 点击API来源切换表格
            $(".change-table").on("click", function (event) {
                event.preventDefault();

                $uuid = $(this).attr("value");
                $(".table").hide();
                $("#table-"+$uuid).show();
            });
        })

        function trim(str) {
            return str.replace(/(^\s*)|(\s*$)/g, "");
        }
    </script>
@stop