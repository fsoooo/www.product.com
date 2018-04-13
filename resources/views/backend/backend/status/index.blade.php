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
                        <div class="filter-block pull-right" style="margin-right: 20px;">
                            <button class="md-trigger btn btn-primary mrg-b-lg"><a href="{{ url('/backend/status/add') }}" style="color: white">添加状态</a></button>
                        </div>
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