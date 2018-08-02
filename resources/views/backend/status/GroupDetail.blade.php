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
                        <ol class="breadcrumb">
                            <li><a href="{{ url('/backend') }}">主页</a></li>
                            <li ><span>运营管理</span></li>
                            <li class="active"><span>状态管理</span></li>
                        </ol>

                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    <li><a href="{{ url('/backend/status/index') }}">状态列表</a></li>
                                    <li  class="active"><a href="{{ url('/backend/status/group') }}">状态分组</a></li>
                                    {{--<li class="active"><a href="#">详细信息</a></li>--}}
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        <h3><p>基本信息</p></h3>
                                        <div class="panel-group accordion" id="account">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h4 class="panel-title">
                                                        {{--<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion2" href="#account-message">--}}
                                                            状态分组基本信息
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="account-message" class="panel-collapse ">
                                                    <table id="basic" class="table table-hover" style="clear: both">
                                                        <tbody>
                                                        <tr>
                                                            <td width="35%">状态分组名称</td>
                                                            <td id="task_name" >
                                                                {{ $group_detail->group_name }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="35%">状态分组描述</td>
                                                            <td id="task_name" >
                                                                {{ $group_detail->group_describe }}
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <h3><p>子状态</p></h3>
                                        <div class="panel-group accordion" id="bill">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h4 class="panel-title">
                                                        <a class="accordion-toggle collapsed md-trigger "  index="0" style="cursor: pointer;"  data-modal="modal-8">
                                                            状态
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="bill-block" class="panel-collapse ">
                                                    <div class="panel-body" style="padding-bottom: 0">
                                                        <table id="product" class="table table-hover" style="clear: both">
                                                            <thead>
                                                            <th>状态名称</th>
                                                            <th>状态描述</th>
                                                            <th>操作</th>
                                                            </thead>
                                                            <tbody>
                                                                @if( $count==0 )
                                                                    <tr>
                                                                        <td colspan="4" style="text-align: center;">
                                                                            暂无子状态
                                                                        </td>
                                                                    </tr>
                                                                @else
                                                                    @foreach($group_detail->group_status as $value)
                                                                        <tr>
                                                                            <td>{{ $value->status_name }}</td>
                                                                            <td>{{ $value->describe }}</td>
                                                                            <td><a href="{{ url('/backend/status/status_detail/'.$value->id) }}">查看详情</a></td>
                                                                        </tr>
                                                                    @endforeach
                                                                @endif
                                                            </tbody>
                                                        </table>
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
    </div>

    <div class="md-overlay" id="add-condition-wrap"></div>

    <script src="/js/jquery-3.1.1.min.js"></script>
    <script charset="utf-8" src="/r_backend/js/modernizr.custom.js"></script>
    <script charset="utf-8" src="/r_backend/js/classie.js"></script>
    <script charset="utf-8" src="/r_backend/js/modalEffects.js"></script>
@stop

