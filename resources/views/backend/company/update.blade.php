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
                <li class=""><span><a href="{{url(('/backend/product/company'))}}">保险公司</a></span></li>
                <li class="active"><span>保险公司信息修改</span></li>
            </ol>
            <div class="main-box clearfix">
                {{--信息修改页面--}}
                <div class=" md-hide" id="modal-2">
                    <div class="md-content">
                        <div class="modal-header">
                            <button class="md-close close">×</button>
                            <h4 class="modal-title">公司信息修改</h4>
                        </div>
                        <div class="modal-body">
                                <form role="form" id="add_company" action='{{url('backend/product/company/update')}}' method="post" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">公司名称</label>
                                        <input class="form-control" name="name" placeholder="保险公司名称（5-50个字符长度）" type="text" value="{{$companies[0]->name}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">公司简称</label>
                                        <input class="form-control" name="display_name" placeholder="保险公司简称（4-50个字符长度）" type="text" value="{{$companies[0]->display_name}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleTextarea">公司分类</label>
                                        <select name="category_id" id="" class="form-control">
                                            @foreach($categories as $k => $vs)
                                                @if($companies[0]->category->name==$vs->name)
                                                    <option value="{{$vs->id}}" selected ><?php echo str_repeat('|----' , $vs['sort']) . $vs['name'] ?></option>
                                                @else
                                                    <option value="{{$vs->id}}"><?php echo str_repeat('|----' , $vs['sort']) . $vs['name'] ?></option>
                                                @endif

                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">公司代码</label>
                                        <input class="form-control" name="code" placeholder="保险公司代码" type="text" value="{{$companies[0]->code}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">公司主页地址</label>
                                        <input class="form-control" name="url" placeholder="保险公司主页地址http://xxx.xx.xxx" type="text" value="{{$companies[0]->url}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">公司LOGO</label>
                                        @if(!empty($companies[0]->logo))
                                            <img src="{{url($companies[0]->logo)}}" alt="" style="height:50px;">
                                        @endif
                                        <input class="form-control" name="logo" placeholder="保险公司logo" type="file">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">公司二维码</label>
                                        @if(!empty($companies[0]->code_img))
                                            <img src="{{url($companies[0]->code_img)}}" alt="" style="height:50px;">
                                        @endif
                                        <input class="form-control" name="code_img" placeholder="保险公司logo" type="file">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">公司邮箱地址</label>
                                        <input class="form-control" name="email" placeholder="保险公司邮箱地址" type="text" value="{{$companies[0]->email}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">公司联系电话</label>
                                        <input class="form-control" name="phone" placeholder="保险公司客服电话" type="text" value="{{$companies[0]->phone}}">
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" name="company_id" value="{{$companies[0]->id}}">
                                        <input type="submit"  class="btn btn-primary" value="确认修改">
                                    </div>
                                </form>
                        </div>
                        {{--<div class="modal-footer">--}}
                        {{--<button type="button" id="form-submit" class="btn btn-primary">确认修改</button>--}}
                        {{--</div>--}}
                    </div>
                </div>
                <div class="md-overlay"></div>
            </div>
        </div>

        <div class="md-overlay"></div>
    </div>

@stop


