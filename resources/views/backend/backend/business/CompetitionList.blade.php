@extends('backend.layout.base')
@section('content')
    <div id="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <ol class="breadcrumb">
                            <li><a href="{{ url('/backend') }}">主页</a></li>
                            <li ><span>销售管理</span></li>
                            <li><span><a href="{{ url('/backend/business/competition') }}">竞赛方案管理</a></span></li>
                            @if($type == 'expire')
                                <li class="active"><a href="{{ url('/backend/business/get_expire') }}">已过期方案</a></li>
                            @else
                                <li class="active"><a href="{{ url('/backend/business/competition') }}">竞赛方案列表</a></li>
                            @endif

                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix">
                            <ul class="nav nav-tabs">
                                @if($type == 'expire')
                                    <li><a href="{{ url('/backend/business/competition') }}">竞赛方案列表</a></li>
                                    <li><a href="{{ url('/backend/business/create_competition') }}">生成竞赛方案</a></li>
                                    <li class="active"><a href="{{ url('/backend/business/get_expire') }}">已过期方案</a></li>
                                @else
                                    <li class="active"><a href="{{ url('/backend/business/competition') }}">竞赛方案列表</a></li>
                                    <li><a href="{{ url('/backend/business/create_competition') }}">生成竞赛方案</a></li>
                                    <li><a href="{{ url('/backend/business/get_expire') }}">已过期方案</a></li>
                                @endif
                            </ul>
                            <header class="main-box-header clearfix">
                                @include('backend.layout.alert_info')
                            </header>
                            <div class="main-box-body clearfix">
                                <div class="table-responsive">
                                    <table id="user" class="table table-hover" style="clear: both">
                                        <thead>
                                        <tr>
                                            <th>方案名称</th>
                                            <th style="text-align: center;">关联产品</th>
                                            <th style="text-align: center;">开始时间</th>
                                            <th style="text-align: center;">结束时间</th>
                                            <th style="text-align: center;">详情</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if( $count == 0 )
                                            <tr>
                                                <td colspan="5" style="text-align: center;">暂无方案</td>
                                            </tr>
                                        @else
                                            @foreach ( $competition_list as $value )
                                                <tr>
                                                    <td>{{ $value->name }}</td>
                                                    <td style="text-align: center;">
                                                        @if( $value->product_id == 0 )
                                                            暂无关联产品
                                                        @else
                                                            {{ $value->product_id }}
                                                        @endif
                                                    </td>
                                                    <td style="text-align: center;">{{ $value->start_time }}</td>
                                                    <td style="text-align: center;">{{ $value->end_time }}</td>
                                                    <td style="text-align: center;">
                                                        <a href="{{ url('/backend/business/get_detail/'.$value->id) }}">查看详情</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            {{--{{ $list->links() }}--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop