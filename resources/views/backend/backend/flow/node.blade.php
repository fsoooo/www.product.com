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
                            <li ><span>工作流管理</span></li>
                            <li class="active"><span>节点</span></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="{{ url('/backend/flow/node') }}">节点列表</a></li>
                                <li><a href="{{ url('backend/flow/add_node') }}">新增</a></li>
                            </ul>
                            <header class="main-box-header clearfix">
                                @include('backend.layout.alert_info')
                            </header>
                            <div class="main-box-body clearfix">
                                <div class="table-responsive">
                                    <table id="user" class="table table-hover" style="clear: both">
                                        <thead>
                                        <tr>
                                            <th>节点名称</th>
                                            <th>所属工作流</th>
                                            <th>创建时间</th>
                                            <th>状态</th>
                                            <th>查看详情</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if($count == 0)
                                            <tr>
                                                <td colspan="3">暂时没有节点</td>
                                            </tr>
                                        @else
                                            @foreach($node_list as $value)
                                                <tr>
                                                    <td>{{ $value->node_name }}</td>
                                                    <td>
                                                        @if($value->node_flow)
                                                            {{ $value->node_flow->flow_name }}
                                                        @else
                                                            不属于任何工作流
                                                        @endif
                                                    </td>
                                                    <td>{{ $value->created_at }}</td>
                                                    <td>{{ $value->status }}</td>
                                                    <td><a href="{{ url('backend/flow/node_detail/'.$value->id) }}">查看</a></td>
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