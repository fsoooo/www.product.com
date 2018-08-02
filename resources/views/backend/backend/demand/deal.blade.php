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
                            <li ><span>需求管理</span></li>
                            <li class="active"><span>已处理需求</span></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix">
                            <ul class="nav nav-tabs">
                                @if($type == 'user')
                                    <li class="active"><a href="{{ url('/backend/demand/deal/user') }}">客户发布</a></li>
                                    <li><a href="{{ url('/backend/demand/deal/agent') }}">代理人发布</a></li>
                                @elseif($type == 'agent')
                                    <li><a href="{{ url('/backend/demand/deal/user') }}">客户发布</a></li>
                                    <li class="active"><a href="{{ url('/backend/demand/deal/agent') }}">代理人发布</a></li>
                                @endif
                            </ul>
                            @include('backend.layout.alert_info')
                            <header class="main-box-header clearfix">

                            </header>
                            <div class="main-box-body clearfix" style="padding-bottom: 0">
                                <div class="table-responsive">
                                    <table id="user" class="table table-hover" style="clear: both">
                                        <thead>
                                        <tr>
                                            <th>申请人昵称</th>
                                            <th>申请人真实姓名</th>
                                            <th>申请人身份证号</th>
                                            <th>报价</th>
                                            <th>创建时间</th>
                                            <th>处理时间</th>
                                            <th>查看详情</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @if($count == 0)
                                                <tr>
                                                    <td colspan="5" style="text-align: center;">
                                                        暂无已处理需求
                                                    </td>
                                                </tr>
                                            @else
                                                @foreach($list as $value)
                                                    <td>{{ $value->demand_user->name }}</td>
                                                    <td>{{ $value->demand_user->real_name }}</td>
                                                    <td>{{ $value->demand_user->code }}</td>
                                                    <td>{{ $value->offer }}</td>
                                                    <td>{{ $value->created_at }}</td>
                                                    <td>{{ $value->updated_at }}</td>
                                                    <td><a href="{{ url('/backend/demand/detail/'.$value->id) }}">查看详情</a></td>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                    @if($count != 0)
                                        {{ $list->links() }}
                                    @endif
                                </div>
                            </div>
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