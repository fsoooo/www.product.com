@extends('backend.layout.base')
@section('content')
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
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="{{ url('/backend/status/index') }}">状态列表</a></li>
                                <li><a href="{{ url('/backend/status/group') }}">状态分组</a></li>
                            </ul>
                            <header class="main-box-header clearfix">
                                @include('backend.layout.alert_info')
                                <div class="filter-block pull-right" style="margin-right: 20px;">
                                    <a href="{{ url('/backend/status/add') }}" style="color: white"><button class="md-trigger btn btn-primary mrg-b-lg">添加状态</button></a>
                                </div>
                            </header>
                            <div class="main-box-body clearfix">

                                <div class="table-responsive">
                                    <table id="user" class="table table-hover">
                                        <thead>
                                        <tr>
                                            <th>状态名</th>
                                            <th>所属分组</th>
                                            <th>创建时间</th>
                                            <th>详情</th>
                                        </tr>
                                        </thead>
                                        @foreach($list as $value)
                                            <tbody>
                                            <tr>
                                                <td>{{ $value->status_name }}</td>
                                                <td>
                                                    @if($value->status_group)
                                                        {{ $value->status_group->group_name }}
                                                    @else
                                                        无分组
                                                    @endif
                                                </td>
                                                <td>{{ $value->created_at }}</td>
                                                <td><a href="{{ url('/backend/status/status_detail/'.$value->id) }}">查看</a></td>
                                            </tr>
                                        </tbody>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop