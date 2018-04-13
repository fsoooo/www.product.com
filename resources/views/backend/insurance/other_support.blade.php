@extends('backend.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('r_backend/css/libs/nifty-component.css')}}"/>
    <style>
        .dty{float:left;width:120px;text-align:right;margin-right:10px;}
    </style>
@endsection
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
                <div class="main-box" style="width: 100%">
                    @include('backend.layout.alert_info')
                    <div class="row">
                        <div class="col-lg-12">
                            <ol class="breadcrumb">
                                <li><a href="#">首页</a></li>
                                <li class="active"><span>产品绑定</span></li>
                                <li class="active"><span>接口支持</span></li>
                            </ol>
                            <h1><small>{{$bind->insurance->name}}--{{$bind->api->name}}</small></h1>
                        </div>
                    </div>


                </div>
        </div>
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    {{--<h2 class="pull-left">Orders</h2>--}}
                </header>
                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        <form action="/backend/product/insurance/other_support_post" method="post">
                            {{ csrf_field() }}
                            <input type="hidden" name="bind_id" value="{{$bind->id}}">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th><a href="#"><span>接口类型</span></a></th>
                                    <th><a href="#" class="desc"><span>是否支持</span></a></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>理赔</td>
                                    <td>
                                        <select class="form-control" name="claim" style="width:300px">
                                            <option value="0" {{$json ? ($json['claim']== 0 ? 'selected': '') : ''}}>否</option>
                                            <option value="1" {{$json ? ($json['claim']== 1 ? 'selected': '') : ''}}>是</option>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td>退保</td>
                                    <td>
                                        <select class="form-control" name="reject" style="width:300px">
                                            <option value="0" {{$json ? ($json['reject']== 0 ? 'selected': '') : ''}}>否</option>
                                            <option value="1" {{$json ? ($json['reject']== 1 ? 'selected': '') : ''}}>是</option>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td>保单查询</td>
                                    <td>
                                        <select class="form-control" name="query" style="width:300px">
                                            <option value="0" {{$json ? ($json['query']== 0 ? 'selected': '') : ''}}>否</option>
                                            <option value="1" {{$json ? ($json['query']== 1 ? 'selected': '') : ''}}>是</option>
                                        </select>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <ul class="pagination pull-center">
                                <li>
                                    <button type="submit">确认</button>
                                    <button type="reset">重置</button>
                                </li>
                            </ul>
                        </form>
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

