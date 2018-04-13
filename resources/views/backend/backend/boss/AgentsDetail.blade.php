@extends('backend.layout.base')
@section('content')
    <style>
        th{
            text-align: center;
        }
        td{
            text-align: center;
        }
        .mySelect{
            display: inline-block;
            margin-left: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }
    </style>
    <div id="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <ol class="breadcrumb">
                            <li><a href="{{ url('/backend') }}">主页</a></li>
                            <li class=""><a href="{{ url('/backend/boss/agent/index/all.all') }}"><span>代理人统计</span></a></li>
                            <li class="active"><span>代理人业绩详情</span></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">

            <div class="col-lg-12">
                <div class="main-box clearfix">
                    <header class="main-box-header clearfix">
                        <h2 class="pull-left">代理人业绩列表</h2>
                    </header>
                    <div class="main-box-body clearfix">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th><span>销售额</span></th>
                                    <th><span>销售数量</span></th>
                                    <th><span>销售产品</span></th>
                                    <th><span>销售最佳产品</span></th>
                                </tr>
                                </thead>
                                <tbody>
                                    @if($count == 0)
                                        <tr>
                                            <td colspan="7">
                                                暂时没有销售业绩
                                            </td>
                                        </tr>
                                    @else
                                        @foreach($data as $value)
                                            <tr>
                                                <td>{{ $value->premium }}</td>
                                                {{--<td>{{ $value->area }}</td>--}}
                                                <td>{{ $value->product_name }}</td>
                                                {{--<td>{{ $value->real_name }}</td>--}}
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        <div>
                    </div>
                </div>
            </div>
        </div>




    </div>
@stop

