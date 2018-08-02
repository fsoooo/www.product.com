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
                <li class="active"><span>保险产品</span></li>
            </ol>
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2 class="pull-left">保险产品列表</h2>
                    <div class="filter-block pull-right" style="margin-right: 20px;">
                        <a href="{{url('backend/product/insurance/add')}}"><button class="btn btn-primary mrg-b-lg">新建保险产品</button></a>
                    </div>
                </header>
                @include('backend.layout.alert_info')
                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        <table class="table user-list table-hover">
                            <thead>
                            <tr>
                                <th class="text-center col-md-1"><span>保险产品简称</span></th>
                                <th class="text-center col-md-1"><span>保险产品全称</span></th>
                                {{--<th class="text-center col-md-1">产品唯一码</th>--}}
                                <th class="text-center col-md-1">保险产品分类</th>
                                <th class="text-center col-md-2"><span>保险公司</span></th>
                                {{--<th class="text-center col-md-2"><span>关联条款</span></th>--}}
                                <th class="text-center col-md-2">描述信息</th>
                                <th class="text-center col-md-2">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($insurance as $k => $v)
                                <tr>
                                    <td class="text-center" >
                                        {{$v->display_name}}
                                    </td>
                                    <td class="text-center" >
                                        {{$v->name}}
                                    </td>
                                    {{--<td class="text-center" >--}}
                                        {{--{{$v->p_code}}--}}
                                    {{--</td>--}}
                                    <td class="text-center" >
                                        {{$v->category->name}}
                                    </td>
                                    <td class="text-center">
                                        {{$v->company->display_name}}
                                    </td>
                                    {{--<td class="text-center">--}}
                                        {{--@foreach($v->clauses as $kk =>$vv)--}}
                                            {{--<li>{{$vv->display_name}}</li>--}}
                                        {{--@endforeach--}}
                                    {{--</td>--}}
                                    <td>
                                        {{mb_strlen($v->content) > 50 ? mb_substr($v->content, 0, 50) . '...' : $v->content}}
                                    </td>

                                    <td  class="text-center">
                                        {{--<a href="{{url('backend/product/insurance/edit/'.$v->id)}}" class="table-link">--}}
                                        <a href="{{ '/backend/product/insurance/edit/'.$v->id }}" class="table-link">
                                        <span class="fa-stack">
                                            <i class="fa fa-square fa-stack-2x"></i>
                                            <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                                        </span>
                                        </a>
                                        {{--<a href="{{url('backend/product/insurance/delete/'.$v->id)}}" class="table-link danger">--}}
                                        <a href="{{url('backend/product/insurance/delete/'.$v->id)}}" class="table-link danger">
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
                        {{ $insurance->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>


@stop
@section('foot-js')
    <script charset="utf-8" src="/r_backend/js/modernizr.custom.js"></script>
    <script charset="utf-8" src="/r_backend/js/classie.js"></script>
    <script charset="utf-8" src="/r_backend/js/modalEffects.js"></script>

@stop

