@extends('backend.layout.base')
@section('content')
            <div id="content-wrapper">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-12">
                                <ol class="breadcrumb">
                                    <li><a href="{{ url('/backend') }}">主页</a></li>
                                    <li ><span>工单管理</span></li>
                                    <li class="active"><a href="{{ url('/backend/special/recspecial') }}">回收站</a></li>
                                </ol>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <ul class="nav nav-tabs">
                                    <li><a href="{{ url('/backend/special/addspecial') }}">发起工单</a></li>
                                    <li><a href="{{ url('/backend/special/special') }}">已发工单</a></li>
                                    <li class="active"><a href="{{ url('/backend/special/recspecial') }}">回收站</a></li>
                                </ul>
                                <div class="main-box no-header clearfix">
                                    <div class="main-box-body clearfix">
                                        <div class="table-responsive">
                                            <table class="table user-list table-hover">
                                                <thead>
                                                <tr>
                                                    <th><span>公司名称</span></th>
                                                    <th><span>工单号</span></th>
                                                    <th><span>提交时间</span></th>
                                                    <th><span>更新时间</span></th>
                                                    <th class="text-center"><span>工单状态</span></th>
                                                    <th>&nbsp;  </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($res as $value )
                                                <tr>
                                                    <td>
                                                        {{$value['company']}}
                                                    </td>
                                                    <td>
                                                        {{$value['special_num']}}
                                                    </td>
                                                    <td>
                                                        {{$value['created_at']}}
                                                    </td>
                                                    <td>
                                                        {{$value['updated_at']}}
                                                    </td>
                                                    <td class="text-center">
                                                        @if($value['status']==1)
                                                        <span class="label label-warning">已处理...</span>
                                                        @elseif($value['status']==2)
                                                        <span class="label label-success">处理成功</span>
                                                        @elseif($value['status']==0)
                                                        <span class="label label-danger">未处理</span>
                                                            @endif
                                                    </td>
                                                    <td>
                                                       </td>
                                                    <td style="width: 20%;">
                                                        <a href="specialinfo?id={{$value['id']}}" class="table-link">
                                                            <span class="fa-stack">
                                                                <i class="fa fa-square fa-stack-2x"></i>
                                                                <i class="fa fa-search-plus fa-stack-1x fa-inverse"></i>
                                                            </span>
                                                        </a>

                                                    </td>
                                                </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @include('backend.layout.pages')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        @stop