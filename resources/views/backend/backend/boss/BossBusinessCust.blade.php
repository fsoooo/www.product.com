@extends('backend.layout.base')
@section('content')
    <style>
        th{
            text-align: center;
        }
        td{
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
                            <li class="active"><span>业务统计</span></li>
                        </ol>
                        <h1>添加客户统计</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    @if($type == 'all')
                                        <li class="active"><a href="{{ url('/backend/boss/business/cust/all') }}">客户池</a></li>
                                        <li><a href="{{ url('/backend/boss/business/cust/person') }}">个人客户</a></li>
                                        <li><a href="{{ url('/backend/boss/business/cust/company') }}">企业客户</a></li>
                                    @elseif($type == 'person')
                                        <li><a href="{{ url('/backend/boss/business/cust/all') }}">客户池</a></li>
                                        <li class="active"><a href="{{ url('/backend/boss/business/cust/person') }}">个人客户</a></li>
                                        <li><a href="{{ url('/backend/boss/business/cust/company') }}">企业客户</a></li>
                                    @elseif($type == 'company')
                                        <li><a href="{{ url('/backend/boss/business/cust/all') }}">客户池</a></li>
                                        <li><a href="{{ url('/backend/boss/business/cust/person') }}">个人客户</a></li>
                                        <li class="active"><a href="{{ url('/backend/boss/business/cust/company') }}">企业客户</a></li>
                                    @endif
                                </ul>
            <div class="col-lg-12">

                <div class="main-box clearfix">
                    <header class="main-box-header clearfix">
                        <h2 class="pull-left">客户列表</h2>
                    </header>
                    <div class="main-box-body clearfix">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th><span>客户名称</span></th>
                                    <th><span>联系方式</span></th>
                                    <th><span>邮箱地址</span></th>
                                    <th><span>身份标识</span></th>
                                    <th><span>类型</span></th>
                                    {{--<th><span>联系记录</span></th>--}}
                                    {{--<th><span>详情</span></th>--}}
                                </tr>
                                </thead>
                                <tbody>
                                    @if($count == 0)
                                        <tr>
                                            <td colspan="7">
                                                暂时没有客户
                                            </td>
                                        </tr>
                                    @else
                                        @foreach($list as $value)
                                            <tr>
                                                <td>{{ $value->name }}</td>
                                                <td>{{ $value->phone }}</td>
                                                <td>{{ $value->email }}</td>
                                                <td>{{ $value->code }}</td>
                                                <td>
                                                    @if($value->type == 0)
                                                        <a class="label label-primary" href="{{ url('/backend/boss/business/cust/person') }}" style="color: white">个人客户</a>
                                                    @else
                                                        <a  class="label label-info" href="{{ url('/backend/boss/business/cust/company') }}" style="color: white">企业客户</a>
                                                    @endif
                                                </td>
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

