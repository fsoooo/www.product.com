@extends('backend.layout.base')
@section('content')
    <div id="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <ol class="breadcrumb">
                            <li><a href="{{ url('/backend') }}">主页</a></li>
                            <li ><span>销售管理</span></li>
                            <li><span><a href="{{ url('/backend/task/index') }}">任务管理</a></span></li>
                            <li class="active"><span><a href="{{ url('/backend/task/index') }}">任务列表</a></span></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="{{ url('/backend/task/index') }}">任务列表</a></li>
                                <li><a href="{{ url('/backend/task/add_task') }}">新建任务</a></li>
                                {{--<li><a href="">查看完成情况</a></li>--}}
                            </ul>
                            @include('backend.layout.alert_info')
                            <header class="main-box-header clearfix">

                            </header>
                            <div class="main-box-body clearfix">
                                <div class="table-responsive">
                                    <table id="user" class="table table-hover" style="clear: both">
                                        <thead>
                                        <tr>
                                            <th>任务名称</th>
                                            <th>任务类型</th>
                                            <th>创建时间</th>
                                            <th>状态</th>
                                            <th>操作</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @if($count == 0)
                                                <tr>
                                                    <td colspan="4" style="text-align: center;">
                                                        暂无任务
                                                    </td>
                                                </tr>
                                            @else
                                                @foreach($list as $value)
                                                <tr>
                                                    <td>
                                                        {{ $value->name }}
                                                    </td>
                                                    <td>
                                                        @if($value->task_type == 1)
                                                            月任务
                                                        @elseif($value->task_type == 2)
                                                            季度任务
                                                        @elseif($value->task_type == 3)
                                                            年任务
                                                        @else
                                                            其他条件任务
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $value->created_at }}
                                                    </td>
                                                    <td>
                                                        @if($value->status == 0)
                                                            启用
                                                        @elseif($value->status == -1)
                                                            禁用
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ url('/backend/task/detail/'.$value->id) }}">查看详情</a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                    {{ $list->links() }}
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