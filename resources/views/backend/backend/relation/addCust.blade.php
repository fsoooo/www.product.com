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
                            <li><span>客户管理</span></li>
                            <li><span><a href="/backend/relation/cust/all">客户池</a></span></li>
                            @if($type == 'company')
                                <li><span><a href="/backend/relation/cust/company">企业用户</a></span></li>
                                <li class="active"><span><a href="/backend/relation/add_cust/company">添加企业用户</a></span></li>
                            @else
                                <li><span><a href="/backend/relation/cust/person">个人用户</a></span></li>
                                <li class="active"><span><a href="/backend/relation/add_cust/person">添加个人用户</a></span></li>
                            @endif
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                {{--<ul class="nav nav-tabs">--}}
                                    {{--<li><a href="{{ url('backend/relation/cust/all') }}">客户池</a></li>--}}
                                    {{--<li><a href="{{ url('backend/relation/cust/person') }}">个人客户</a></li>--}}
                                    {{--<li><a href="{{ url('backend/relation/cust/company') }}">企业客户</a></li>--}}
                                    {{--@if($controller == 'add')--}}
                                        {{--<li class="active"><a href="">添加客户</a></li>--}}
                                    {{--@elseif($controller == 'edit')--}}
                                        {{--<li class="active"><a href="">修改客户信息</a></li>--}}
                                    {{--@endif--}}
                                {{--</ul>--}}
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        @if($type == 'company')
                                            @if($controller == 'add')
                                                <h3><p>添加企业客户</p></h3>
                                            @elseif($controller == 'edit')
                                                <h3><p>修改企业客户信息</p></h3>
                                            @endif
                                        @else
                                            @if($controller == 'add')
                                                <h3><p>添加个人客户</p></h3>
                                            @elseif($controller == 'edit')
                                                <h3><p>修改个人客户信息</p></h3>
                                            @endif
                                            @endif
                                        @include('backend.layout.alert_info')
                                        <div class="panel-group accordion" id="operation">
                                            <div class="panel panel-default">
                                                @if($controller == 'add')
                                                    <form action="{{ url('/backend/relation/add_cust_submit') }}" method="post" id="cust-form">
                                                @elseif($controller == 'edit')
                                                    <form action="{{ url('/backend/relation/edit_cust_submit') }}" method="post" id="cust-form">
                                                @endif
                                                    {{ csrf_field() }}
                                                    <table id="user" class="table table-hover" style="clear: both">
                                                        <tbody>

                                                        @if($type == 'company')
                                                            <input type="text" name="type" value="company" hidden>
                                                            @if($controller == 'add')
                                                                <input type="text" name="cust_id" value="" hidden>
                                                                <tr>
                                                                    <td width="15%">企业名称</td>
                                                                    <td width="60%">
                                                                        <input type="text" class="form-control" placeholder="请输入企业名称" name="name">
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>三码合一</td>
                                                                    <td>
                                                                        <input class="form-control" type="text" name="code" placeholder="请输入三码合一">
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td width="15%">联系电话</td>
                                                                    <td width="60%">
                                                                        <input type="text" class="form-control" placeholder="请输入联系电话" name="phone">
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>企业邮箱</td>
                                                                    <td>
                                                                        <input type="text" class="form-control" placeholder="请输入企业邮箱" name="email">
                                                                    </td>
                                                                </tr>
                                                            @elseif($controller == 'edit')
                                                                <input type="text" name="cust_id" value="{{ $detail->id }}" hidden>
                                                                <tr>
                                                                    <td width="15%">企业名称</td>
                                                                    <td width="60%">
                                                                        <input type="text" class="form-control" placeholder="请输入企业名称" name="name" value="{{ $detail->name }}">
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>三码合一</td>
                                                                    <td>
                                                                        <input class="form-control" type="text" name="code" placeholder="请输入三码合一" value="{{ $detail->code }}" disabled>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td width="15%">联系电话</td>
                                                                    <td width="60%">
                                                                        <input type="text" class="form-control" placeholder="请输入联系电话" name="phone" value="{{ $detail->phone }}">
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>企业邮箱</td>
                                                                    <td>
                                                                        <input type="text" class="form-control" placeholder="请输入企业邮箱" name="email" value="{{ $detail->email }}">
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @else
                                                            <input type="text" name="type" value="person" hidden>
                                                            @if($controller == 'add')
                                                                <input type="text" name="cust_id" value="" hidden>
                                                                <tr>
                                                                    <td width="15%">客户名称</td>
                                                                    <td width="60%">
                                                                        <input type="text" class="form-control" placeholder="请输入客户名称" name="name">
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>身份证号</td>
                                                                    <td>
                                                                        <input class="form-control" type="text" name="code" placeholder="请输入身份证号">
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td width="15%">联系电话</td>
                                                                    <td width="60%">
                                                                        <input type="text" class="form-control" placeholder="请输入联系电话" name="phone">
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>客户邮箱</td>
                                                                    <td>
                                                                        <input type="text" class="form-control" placeholder="请输入客户邮箱" name="email">
                                                                    </td>
                                                                </tr>
                                                            @elseif($controller == 'edit')
                                                                <input type="text" name="cust_id" value="{{ $detail->id }}" hidden>
                                                                <tr>
                                                                    <td width="15%">客户名称</td>
                                                                    <td width="60%">
                                                                        <input type="text" class="form-control" placeholder="请输入客户名称" name="name" value="{{ $detail->name }}">
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>身份证号</td>
                                                                    <td>
                                                                        <input class="form-control" type="text" name="code" placeholder="请输入身份证号" value="{{ $detail->code }}" disabled>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td width="15%">联系电话</td>
                                                                    <td width="60%">
                                                                        <input type="text" class="form-control" placeholder="请输入联系电话" name="phone" value="{{ $detail->phone }}">
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>客户邮箱</td>
                                                                    <td>
                                                                        <input type="text" class="form-control" placeholder="请输入客户邮箱" name="email" value="{{ $detail->email }}">
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endif

                                                        </tbody>
                                                    </table>
                                                </form>
                                            </div>
                                            @if($controller == 'add')
                                                <button id="add-cust-btn" index="add" class="btn btn-success">确认添加</button>
                                            @elseif($controller == 'edit')
                                                <button id="add-cust-btn" index="edit" class="btn btn-success">确认修改</button>
                                            @endif
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
            //进行验证发送
            var name = $('input[name=name]');
            var code = $('input[name=code]');
            var btn = $('#add-cust-btn');
            var cust_form = $('#cust-form');
            var cust_id_input = $('input[name=cust_id]');
            var phone = $('input[name=phone]');
            var email = $('input[name=email]');








            var code_pattern = {{ config('pattern.code') }};
            var phone_pattern = {{ config('pattern.phone') }};
            var email_pattern = {{ config('pattern.email') }};

            var btn_type = btn.attr('index');
            btn.click(function(){
                var name_val = name.val();
                var code_val = code.val();
                var phone_val = phone.val();
                var email_val = email.val();
                if(name_val == ''){
                    name.parent().addClass("has-error");
                    alert('名称不能为空');
                    return false;
                }
                    name.parent().removeClass("has-error");
                var check_code = check(code,code_val,code_pattern,'证件号');
                var check_phone = check(phone,phone_val,phone_pattern,'手机号');
                var check_email = check(email,email_val,email_pattern,'邮箱');
                if(!check_code||!check_email||!check_phone){
                    alert('格式错误');
                    return false;
                }

                if(btn_type == 'add'){
                    //进行判断是否已经添加过该客户
                    $.ajax({
                        type: "post",
                        dataType: "json",
                        async: true,
                        //修改的地址，
                        url: "/backend/relation/is_my_cust_ajax",
                        data: 'code='+code_val,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        },
                        success: function(data){
                            var status = data['status'];
                            if(status == 200){
                                var cust_id = data['data'];
                                //说明已经添加过该客户
                                if(confirm('已经添加过该客户了，要进行信息修改吗？')){
                                    var data = cust_form.serialize();
                                    cust_id_input.val(cust_id);
                                    cust_form.attr('action','/backend/relation/edit_cust_submit');
                                    cust_form.submit();
                                }
                            }else {
                                cust_form.submit();
                            }
                        },error: function () {
                            alert('发送失败');
                        }
                    });
                }else{
                    cust_form.submit();
                }
            })


        })

    </script>
@stop

