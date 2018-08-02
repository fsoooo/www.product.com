@extends('backend.layout.base')
@section('content')
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
                        <ol class="breadcrumb">
                            <li><a href="{{ url('/backend') }}">主页</a></li>
                            <li ><span>运营管理</span></li>
                            <li class="active"><span>状态管理</span></li>
                        </ol>
                        <div class="filter-block pull-right" style="margin-right: 20px;">
                            <button class="md-trigger btn btn-primary mrg-b-lg"><a href="{{ url('/backend/status/add_group') }}" style="color: white">添加状态分组</a></button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix">
                            <ul class="nav nav-tabs">
                                <li><a href="{{ url('/backend/status/index') }}">状态列表</a></li>
                                <li class="active"><a href="{{ url('/backend/status/group') }}">状态分组</a></li>
                            </ul>
                            <header class="main-box-header clearfix">
                                @include('backend.layout.alert_info')
                            </header>
                            <div class="main-box-body clearfix">
                                <div class="table-responsive">
                                    <table id="user" class="table table-hover" style="clear: both">
                                        <thead>
                                        <tr>
                                            <th>状态名</th>
                                            <th>状态描述</th>
                                            <th>状态</th>
                                            <th>详情</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @if($count == 0)
                                                <tr>
                                                    <td colspan="4" style="text-align: center;">暂无分组</td>
                                                </tr>
                                            @else
                                                @foreach($status_group as $value)
                                                    <tr>
                                                        <td>{{ $value->group_name }}</td>
                                                        <td>{{ $value->group_describe }}</td>
                                                        <td>{{ $value->status }}</td>
                                                        <td><a href="{{ url('/backend/status/group_detail/'.$value->id) }}">查看详情</a></td>
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
        <footer id="footer-bar" class="row">
            <p id="footer-copyright" class="col-xs-12">
                &copy; 2014 <a href="http://www.adbee.sk/" target="_blank">Adbee digital</a>. Powered by Centaurus Theme.
            </p>
        </footer>
    </div>

@stop