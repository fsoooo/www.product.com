@extends('backend.layout.base')
<link rel="stylesheet" type="text/css" href="{{asset('r_backend/css/libs/nifty-component.css')}}"/>
@section('content')
    <meta name="_token" content="{{ csrf_token() }}"/>
    <style>
        th,td{
            text-align: center;
        }
    </style>
    <div id="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-12">
                                <ol class="breadcrumb">
                                    <li><a href="{{ url('/backend') }}">主页</a></li>
                                    <li ><span>销售管理</span></li>
                                    <li ><span>代理人渠道管理</span></li>
                                    <li class="active"><span>佣金设置</span></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="#">佣金设置</a></li>
                                </ul>
                                @include('backend.layout.alert_info')
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        <div class="panel-group accordion" id="account">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h4 class="panel-title">
                                                        {{--<a class="accordion-toggle " data-toggle="collapse" >--}}
                                                            产品搜索
                                                        {{--</a>--}}
                                                    </h4>
                                                </div>
                                                <div >
                                                    <form action="{{ url('/backend/sell/ditch_agent/search_product') }}" method="post">
                                                        {{ csrf_field() }}
                                                        <table class="table user-list table-hover"  id="basic">
                                                            <tr >
                                                                <td><label for="">产品id</label></td>
                                                                <td><input type="text" value="" placeholder="" name="product_id"></td>
                                                                <td> <button id="product-btn">搜索</button></td>
                                                            </tr>
                                                        </table>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>


                                        <h3><p>我的产品</p></h3>
                                        <div class="panel-group accordion" id="bill">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h4 class="panel-title">
                                                        {{--<a class="accordion-toggle  md-trigger " >--}}
                                                            产品列表
                                                        {{--</a>--}}
                                                    </h4>
                                                </div>
                                                <div id="bill-block" class="panel-collapse ">
                                                    <div class="panel-body" style="padding-bottom: 0">
                                                        <table id="product" class="table table-hover" style="clear: both">
                                                            <thead>
                                                                <tr>
                                                                    <th>产品名称</th>
                                                                    <th>所属公司</th>
                                                                    <th>查看设置佣金</th>
                                                                </tr>
                                                            </thead>
                                                            @if($count == 0)
                                                                <tr>
                                                                    <td colspan="4">暂无产品</td>
                                                                </tr>
                                                            @else
                                                                @foreach($product_list as $value)
                                                                    <tr>
                                                                        <td>{{ $value->product_name }}</td>
                                                                        <td>{{ $value->company_name }}</td>
                                                                        <td><a href="{{ url('/backend/sell/ditch_agent/brokerage_detail/'.$value->id) }}">查看设置佣金</a></td>
                                                                    </tr>
                                                                @endforeach
                                                            @endif
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="page" style="text-align: center;">
                                                    {{ $product_list->links() }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/js/jquery-3.1.1.min.js"></script>
    <script charset="utf-8" src="/r_backend/js/modernizr.custom.js"></script>
    <script charset="utf-8" src="/r_backend/js/classie.js"></script>
    <script charset="utf-8" src="/r_backend/js/modalEffects.js"></script>
@stop

