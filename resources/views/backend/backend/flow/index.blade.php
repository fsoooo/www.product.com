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
                            <li><span>工作流管理</span></li>
                            <li class="active"><span>工作流</span></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="{{ url('/backend/flow/index') }}">工作流列表</a></li>
                                <li><a href="{{ url('backend/flow/add_flow') }}">新增</a></li>
                            </ul>
                            <header class="main-box-header clearfix">
                                @include('backend.layout.alert_info')
                            </header>
                            <div class="main-box-body clearfix">
                                <div class="table-responsive">
                                    <table id="user" class="table table-hover" style="clear: both">
                                        <thead>
                                        <tr>
                                            <th>工作流名称</th>
                                            <th>创建时间</th>
                                            <th>查看详情</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ( $list as $value )
                                            <tr>
                                                <td>{{ $value->flow_name }}</td>
                                                <td>{{ $value->created_at }}</td>
                                                <td><a href="{{ url('/backend/flow/get_detail/'.$value->id) }}">查看详情</a></td>
                                            </tr>
                                        @endforeach
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