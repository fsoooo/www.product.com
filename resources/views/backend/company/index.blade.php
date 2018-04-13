@extends('backend.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('r_backend/css/libs/nifty-component.css')}}"/>
    <style>
        .form-group{margin-bottom: 5px;}
    </style>
@endsection
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="/backend/">主页</a></li>
                <li ><span>产品管理</span></li>
                <li class="active"><span>保险公司</span></li>
            </ol>
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2 class="pull-left">保险公司列表</h2>
                    <div class="filter-block pull-right" style="margin-right: 20px;">
                        <button class="md-trigger btn btn-primary mrg-b-lg" data-modal="modal-8">新建保险公司</button>
                    </div>
                </header>
                @include('backend.layout.alert_info')
                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        <table class="table user-list table-hover">
                            <thead>
                            <tr>
                                <th class="text-center"><span>公司简称</span></th>
                                <th class="text-center">公司全称</th>
                                <th class="text-center"><span>公司分类</span></th>
                                <th class="text-center"><span>公司logo</span></th>
                                <th class="text-center"><span>公司邮箱地址</span></th>
                                <th class="text-center"><span>创建时间</span></th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($companies as $k => $v)
                                <tr>
                                    <td class="text-center">
                                        {{$v->display_name}}
                                    </td>
                                    <td class="text-center">
                                        {{$v->name}}
                                    </td>
                                    <td class="text-center">
                                        {{$v->category->name}}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{$v->url}}" target="_blank">
                                            <img src="{{url($v->logo)}}" alt="" style="height:50px;">
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        {{$v->email}}
                                    </td>
                                    <td class="text-center">
                                        {{$v->created_at}}
                                    </td>


                                    <td style="width: 15%;" id="fa">
                                        <a href="{{url('/backend/product/company/updateCompanyIndex/'.$v->id)}}" class="md-trigger" data-modal="modal-1" >
                                            <span class="fa-stack">
                                                <i class="fa fa-square fa-stack-2x"></i>
                                                <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                                            </span>
                                        </a>
                                        <a  class="table-link danger" onclick="deletecompany({{$v->id}})">
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
                        {{ $companies->links() }}
                    </div>

                </div>
            </div>
        </div>
        {{--内容添加--}}
        <div class="md-modal md-effect-8 md-hide" id="modal-8">
            <div class="md-content">
                <div class="modal-header">
                    <button class="md-close close">×</button>
                    <h4 class="modal-title">保险公司添加</h4>
                </div>
                <div class="modal-body">
                    <form role="form" id="add_company" action='{{url('backend/product/company/add')}}' method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="exampleInputEmail1"><span class="red">*</span>公司名称</label>
                            <input class="form-control" name="name" placeholder="保险公司名称（4-50个字符长度）" type="text">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1"><span class="red">*</span>公司简称</label>
                            <input class="form-control" name="display_name" placeholder="保险公司简称（4-50个字符长度）" type="text">
                        </div>
                        <div class="form-group">
                            <label for="exampleTextarea"><span class="red">*</span>公司分类</label>
                            <select name="category_id" id="" class="form-control">
                                @foreach($categories as $k => $v)
                                    <option value="{{$v->id}}"><?php echo str_repeat('|----' , $v['sort']) . $v['name'] ?></option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1"><span class="red">*</span>统一社会信用码</label>
                            <input class="form-control" name="code" placeholder="保险公司代码" type="text">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">公司银行卡名称</label>
                            <input class="form-control" name="bank_type" placeholder="公司银行卡类型" type="text">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">公司银行卡账号</label>
                            <input class="form-control" name="bank_num" placeholder="公司银行卡账号" type="text">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1"><span class="red">*</span>公司主页地址</label>
                            <input class="form-control" name="url" placeholder="保险公司主页地址http://xxx.xx.xxx" type="text">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1"><span class="red">*</span>公司LOGO</label>
                            <input class="form-control" name="logo" placeholder="保险公司logo" type="file">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">公司邮箱地址</label>
                            <input class="form-control" name="email" placeholder="保险公司邮箱地址" type="text">
                        </div><div class="form-group">
                            <label for="exampleInputPassword1"><span class="red">*</span>公司联系电话</label>
                            <input class="form-control" name="phone" placeholder="保险公司联系电话" type="text">
                        </div><div class="form-group">
                            <label for="exampleInputPassword1">公众号二维码</label>
                            <input class="form-control" name="code_img" placeholder="保险公司公众号二维码" type="file">
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="form-submit" class="btn btn-primary">确认提交</button>
                </div>
            </div>
        </div>
    </div>

@stop
@section('foot-js')
    <script charset="utf-8" src="/r_backend/js/modernizr.custom.js"></script>
    <script charset="utf-8" src="/r_backend/js/classie.js"></script>
    <script charset="utf-8" src="/r_backend/js/modalEffects.js"></script>
    <script>
        function deletecompany(id) {
            $.ajax( {
                type : "get",
                url : '/backend/product/company/delete/id/' + id,
                dataType : 'json',
                success:function(msg){
                    if(msg.status == '1'){
                        alert(msg.message);
                        $('#check').html('<font color="red">'+msg.message+'</font>');
                    }else {
                        alert(msg.message);
                        window.location =location;
                    }

                }
            });

        }


        $(function(){
            $submit = $("#form-submit");
            $submit.click(function(){
                var $name = $("input[name=name]").val();
                var $name_role = /^[（）\u4e00-\u9fa5a-zA-Z\s\-_()]{4,50}$/;
                var $name_check = $name_role.test($name);
                changeClass($("input[name=name]"), $name_check);

                var $display_name = $("input[name=display_name]").val();
                var $display_name_role = /^[（）\u4e00-\u9fa5a-zA-Z\s\-_()]{4,50}$/;
                var $display_name_check = $display_name_role.test($display_name);
                changeClass($("input[name=display_name]"), $display_name_check);

                var $url = $("input[name=url]").val();
                var $url_role = /(http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;:/~\+#]*[\w\-\@?^=%&amp;/~\+#])?/;
                var $url_check = $url_role.test($url);
                changeClass($("input[name=url]"), $url_check);

//                var $logo = $("input[name=logo]").val();
//				alert($logo);
//				var $logo_role = /^\S.jpg|\S.png|\S.jpeg$/;
//				var $logo_check = $logo_role.test($logo);
//				changeClass($("input[name=logo]"),$logo_check);

                function changeClass(obj, result){
                    if(!result){
                        obj.val('');
                        obj.parent().addClass("has-error");
                    }else{
                        obj.parent().removeClass("has-error");
                    }
                }
                if($name_check && $display_name_check && $url){
                    $("#add_company").submit();
                }else{
                    alert("请根据输入框提示填写相关内容");
                }

            })
        })
    </script>
@stop

