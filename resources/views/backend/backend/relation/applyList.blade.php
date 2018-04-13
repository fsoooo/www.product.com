@extends('backend.layout.base')
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
                        <ol class="breadcrumb">
                            <li><a href="{{ url('/backend') }}">主页</a></li>
                            <li><span>客户管理</span></li>
                            <li><span>申请列表</span></li>
                            @if($type == 'deal')
                                <li class="active"><a href="{{ url('/backend/relation/get_deal_apply') }}">已处理申请列表</a></li>
                            @else
                                <li class="active"><a href="{{ url('/backend/relation/get_apply') }}">未处理申请列表</a></li>
                            @endif
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    @if($type == 'deal')
                                        <li><a href="{{ url('/backend/relation/get_apply') }}">未处理申请列表</a></li>
                                        <li class="active"><a href="{{ url('/backend/relation/get_deal_apply') }}">已处理申请列表</a></li>
                                    @else
                                        <li class="active"><a href="{{ url('/backend/relation/get_apply') }}">未处理申请列表</a></li>
                                        <li><a href="{{ url('/backend/relation/get_deal_apply') }}">已处理申请列表</a></li>
                                    @endif
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        <h3><p>操作记录</p></h3>
                                        @include('backend.layout.alert_info')
                                        <div class="panel-group accordion" id="account">
                                            <div class="panel panel-default">
                                                @if($type == 'deal')
                                                    <table id="user" class="table table-hover" style="clear: both">
                                                        <thead>
                                                        <tr>
                                                            <th><span>客户名称</span></th>
                                                            <th><span>客户身份标识</span></th>
                                                            <th><span>客户类型</span></th>
                                                            <th><span>申请人</span></th>
                                                            <th><span>申请人身份标识</span></th>
                                                            <th><span>申请时间</span></th>
                                                            <th><span>状态</span></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @if($count == 0)
                                                            <tr>
                                                                <td colspan="6">
                                                                    暂无未处理申请
                                                                </td>
                                                            </tr>
                                                        @else
                                                            @foreach($list as $value)
                                                                <tr>
                                                                    <td>{{ $value->name }}</td>
                                                                    <td>{{ $value->code }}</td>
                                                                    <td>
                                                                        @if($value->type == 0)
                                                                            <a class="label label-primary" href="{{ url('/backend/relation/cust/person') }}" style="color: white">个人客户</a>
                                                                        @else
                                                                            <a  class="label label-info" href="{{ url('/backend/relation/cust/company') }}" style="color: white">企业客户</a>
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ $value->real_name }}</td>
                                                                    <td>{{ $value->user_code }}</td>
                                                                    <td>{{ $value->created_at }}</td>
                                                                    <td>
                                                                        @if($value->status == 1)
                                                                            申请通过
                                                                        @elseif($value->status == 2)
                                                                            申请拒绝
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                        </tbody>
                                                    </table>
                                                @else
                                                    <table id="user" class="table table-hover" style="clear: both">
                                                        <thead>
                                                        <tr>
                                                            <th><span>客户名称</span></th>
                                                            <th><span>客户身份标识</span></th>
                                                            <th><span>客户类型</span></th>
                                                            <th><span>申请人</span></th>
                                                            <th><span>申请人身份标识</span></th>
                                                            <th><span>申请时间</span></th>
                                                            <th><span>备注说明</span></th>
                                                            <th><span>操作说明</span></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @if($count == 0)
                                                            <tr>
                                                                <td colspan="6">
                                                                    暂无未处理申请
                                                                </td>
                                                            </tr>
                                                        @else
                                                            @foreach($list as $value)
                                                                <tr>
                                                                    <td>{{ $value->name }}</td>
                                                                    <td>{{ $value->code }}</td>
                                                                    <td>
                                                                        @if($value->type == 0)
                                                                            <a class="label label-primary" href="{{ url('/backend/relation/cust/person') }}" style="color: white">个人客户</a>
                                                                        @else
                                                                            <a  class="label label-info" href="{{ url('/backend/relation/cust/company') }}" style="color: white">企业客户</a>
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ $value->real_name }}</td>
                                                                    <td>{{ $value->user_code }}</td>
                                                                    <td>{{ $value->created_at }}</td>
                                                                    <td style="width: 30%">{{ $value->apply_remarks }}</td>
                                                                    <td>
                                                                        <div class="btn-group">
                                                                            <button type="button" class="btn btn-link dropdown-toggle" data-toggle="dropdown" >
                                                                                操作 <span class="caret"></span>
                                                                            </button>
                                                                            <ul class="dropdown-menu">
                                                                                <li><a href="{{ url('/backend/relation/agree_apply/'.$value->id) }}">同意申请</a></li>
                                                                                <li><a href="{{ url('/backend/relation/refuse_apply/'.$value->apply_id) }}">拒绝申请</a></li>
                                                                            </ul>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                        </tbody>
                                                    </table>
                                                @endif
                                            </div>
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
        </div>
    </div>
@stop

