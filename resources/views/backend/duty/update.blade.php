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
                <li class=""><span><a href="{{url('/backend/product/duty')}}">责任</a></span></li>
                <li class="active"><span>更新责任</span></li>
            </ol>
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2 class="pull-left">更新责任</h2>
                </header>
                @include('backend.layout.alert_info')
                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        <div class="md-hide" id="modal-8">
                            <div class="md-content">
                                <div class="modal-body">
                                    <form role="form" id="add_duty" action='{{url('backend/product/duty/updataDutySubmit')}}' method="post">
                                        {{ csrf_field() }}
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">责任名称</label>
                                            <input class="form-control" name="name" placeholder="责任名称（5-50个字符长度）" type="text" value="{{$data[0]->name}}">
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">责任描述</label>
                                            <input class="form-control" name="description" placeholder="责任描述（5-100个字符长度）" type="text" value="{{$data[0]->description}}">
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleTextarea">责任分类</label>
                                            <select name="category_id" id="" class="form-control">
                                                @foreach($categories as $k => $v)
                                                    <option value="{{$v->id}}" <?php if($data[0]->category->name == $v->name){ ?> selected <?php }?> ><?php echo str_repeat('|----' , $v['sort']) . $v['name'] ?></option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleTextarea">责任类型</label>
                                            <select name="type" id="" class="form-control">
                                                @if($data[0]->type == 'main')
                                                    <option value="main" selected>主险责任</option>
                                                    <option value="attach">附加险责任</option>
                                                @else
                                                    <option value="main" >主险责任</option>
                                                    <option value="attach" selected>附加险责任</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleTextarea">关联基础保额</label>
                                            <select name="need_coverage" id="" class="form-control">
                                                @if($data[0]->need_coverage == 1)
                                                    <option value="1" selected>需要</option>
                                                    <option value="0">不需要</option>
                                                @else
                                                    <option value="0">不需要</option>
                                                    <option value="1">需要</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleTextarea">责任详情</label>
                                            <textarea class="form-control" id="detail" name="detail" rows="3" placeholder="责任详情信息">{{$data[0]->detail}}</textarea>
                                        </div>
                                        <input type="hidden" name="id" value="{{$id}}">

                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" id="form-submit" class="btn btn-primary">确认提交</button>
                                </div>
                            </div>
                        </div>
                    </div>
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
//                var $name_role = /^[（）\u4e00-\u9fa5a-zA-Z\s\-_()]{4,50}$/;
//                var $name_check = $name_role.test($name);
//                changeClass($("input[name=name]"), $name_check);

//                var $description = $("input[name=description]").val();
//                var $description_role = /^[（）\u4e00-\u9fa5a-zA-Z\s\-_()]{4,100}$/;
//                var $description_check = $description_role.test($description);
//                changeClass($("input[name=description]"), $description_check);

//                var $detail = $("#detail").val();
//                var $detail_role = /^[（）\u4e00-\u9fa5a-zA-Z\s\-_()]{5,}$/;
//                var $detail_check = $detail_role.test($detail);
//                changeClass($("#detail"), $detail_check);

//                function changeClass(obj, result){
//                    if(!result){
//                        obj.val('');
//                        obj.parent().addClass("has-error");
//                    }else{
//                        obj.parent().removeClass("has-error");
//                    }
//                }
//
//
//
//
//                if($name_check){
                    $("#add_duty").submit();
//                }else{
//                    alert("请根据输入框提示填写相关内容");
//                }

            })
        })
    </script>
@stop

