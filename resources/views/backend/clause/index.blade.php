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
                <li class="active"><span>条款</span></li>
            </ol>
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2 class="pull-left">条款列表</h2>
                    <div class="filter-block pull-right"style="margin-right: 20px;">
                        <a href="{{asset('backend/product/clause/add')}}"><button class="btn btn-primary mrg-b-lg">新建条款</button></a>
                    </div>
                    <div class="filter-block pull-right" style="margin-right: 20px;">
                        <button class="btn btn-primary mrg-b-lg md-trigger"data-modal="modal-8">费率Excel模板下载</button>
                    </div>
                </header>
                @include('backend.layout.alert_info')
                <div class="main-box-body clearfix">
                    <form action='{{url('backend/product/clause/')}}' method="get">
                        条款名称: <input name="name" value="{{ $name }}">&nbsp;
                        <input type="submit" id="search" value="搜索">
                    </form>
                </div>

                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        <table class="table user-list table-hover">
                            <thead>
                            <tr>
                                <th class="text-center col-md-2"><span>条款简称</span></th>
                                <th class="text-center col-md-2"><span>条款全称</span></th>
                                <th class="text-center col-md-1">分类名称</th>
                                <th class="text-center col-md-1">所属类型</th>
                                {{--<th class="text-center col-md-2"><span>责任信息</span></th>--}}
                                <th class="text-center col-md-2">附件链接</th>
                                <th class="text-center col-md-2">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($clauses as $k => $v)
                                <tr>
                                    <td class="text-center" >
                                        {{$v->display_name}}
                                    </td>
                                    <td class="text-center" >
                                        {{$v->name}}
                                    </td>
                                    <td class="text-center" >
                                        {{$v->category->name}}
                                    </td>
                                    <td class="text-center" >
                                        @if($v->type == 'main')
                                            主险条款
                                            @else
                                            附加险条款
                                            @endif
                                    </td>
                                    {{--<td class="">--}}
                                        {{--@foreach($v->duties as $dk => $dv)--}}
                                            {{--{{$dv->name}} <br />--}}
                                        {{--@endforeach--}}
                                    {{--</td>--}}
                                    {{--<td class="text-center">--}}
                                        {{--{{$v->content}}--}}
                                    {{--</td>--}}
                                    <td class="text-center">
                                        <a href="{{url($v->file_url)}}"  target="_blank">查看附件</a>
                                    </td>

                                    <td  class="text-center">
                                        {{--@if((count($v->tariff)))--}}
                                            {{--<a href="{{url('/backend/product/tariff', array('clause_id'=>$v['id']))}}">--}}
                                                {{--查看费率--}}
                                            {{--</a>--}}
                                        {{--@else--}}
                                            {{--<a href="#" class="md-trigger add_excel" data-modal="modal-9" clause-id="{{$v['id']}}">--}}
                                                {{--导入费率--}}
                                            {{--</a>--}}
                                        {{--@endif--}}

                                        <a href="{{ url("/backend/product/clause/update/" . $v->id) }}" class="table-link">
                                        <span class="fa-stack">
                                            <i class="fa fa-square fa-stack-2x"></i>
                                            <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                                        </span>
                                        </a>
                                        <a href="{{ url("/backend/product/clause/delete/" . $v->id) }}" class="table-link danger">
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
                        {{ $clauses->links() }}
                    </div>

                </div>
            </div>
        </div>

        <div class="md-modal md-effect-8 md-hide" id="modal-8">
            <div class="md-content">
                <div class="modal-header">
                    <button class="md-close close">×</button>
                    <h4 class="modal-title">费率模板下载</h4>
                </div>
                <div class="modal-body">
                    <a href="{{asset('/download_resource/费率模板.xls')}}">下载</a>
                </div>
                <div class="modal-body">
                    <a href="{{asset('/download_resource/团险费率表.xlsx')}}">团险费率表下载</a>
                </div>
                {{--<div class="modal-footer">--}}
                    {{--<button type="button" id="form-submit-excel" class="btn btn-primary">确认提交</button>--}}
                {{--</div>--}}
            </div>
        </div>

        <div class="md-modal md-effect-8 md-hide" id="modal-9">
            <div class="md-content">
                <div class="modal-header">
                    <button class="md-close close">×</button>
                    <h4 class="modal-title">费率添加</h4>
                </div>
                <div class="modal-body">
                    <form role="form" id="add_excel" action='{{url('backend/product/tariff/push_excel_post')}}' method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="clause_id" />
                        <div class="form-group">
                            <label for="exampleInputPassword1">上传excel文件</label>
                            <input class="form-control" name="excel" placeholder="费率logo" type="file">
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="form-submit-excel" class="btn btn-primary">确认提交</button>
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
        $(".add_excel").click(function(){
            var clause_id = $(this).attr('clause-id');
            $("input[name=clause_id]").val(clause_id);
        })

        $("#form-submit-excel").click(function(){
//            alert(1);
            $("#add_excel").submit();
        });
    </script>
@stop

