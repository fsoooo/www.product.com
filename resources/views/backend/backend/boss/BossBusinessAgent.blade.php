@extends('backend.layout.base')
@section('content')
    <style>
        th{
            text-align: center;
        }
        td{
            text-align: center;
        }
        .mySelect{
            display: inline-block;
            margin-left: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }
    </style>
    <div id="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <ol class="breadcrumb">
                            <li><a href="{{ url('/backend') }}">主页</a></li>
                            <li class="active"><span>代理人统计</span></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">

            <div class="col-lg-12">
                <form action="">
                    {{--区域选择--}}
                    <select name="area" id="area" class="mySelect">
                        <option value="0">选择区域</option>
                        @foreach($area as $v)
                            <option <?php if($selected[1] == "$v->area"){?> selected <?php }?> value="{{$v->area}}">{{$v->area}}</option>
                        @endforeach
                    </select>
                    {{--职级选择--}}
                    <select name="position" id="position" class="mySelect">
                        <option value="0">选择职级</option>
                        @foreach($position as $v)
                            <option <?php if($selected[1] == "$v->position"){?> selected <?php }?> value="{{$v->position}}">{{$v->position}}</option>
                        @endforeach
                    </select>
                </form>
                {{--筛选js--}}
                <script>
                    $(document).ready(function(){
                        if ($('#area').change(function(){
                                    var $area = $('#area').val();
                                    location.href='area.'+$area;
                                }));
                        if ($('#position').change(function(){
                                    var $position = $('#position').val();
                                    location.href='position.'+$position;
                                }));
                        if ($('#performance').change(function(){
                                    var $performance = $('#performance').val();
                                    location.href='performance.'+$performance;
                                }));
                    });
                </script>
                <div class="main-box clearfix">
                    <header class="main-box-header clearfix">
                        <h2 class="pull-left">代理人列表</h2>
                    </header>
                    <div class="main-box-body clearfix">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th><span>工号</span></th>
                                    <th><span>区域</span></th>
                                    <th><span>职级</span></th>
                                    <th><span>姓名</span></th>
                                    <th><span>身份证号</span></th>
                                    <th><span>联系电话</span></th>
                                    <th><span>邮箱</span></th>
                                    <th><span>住址</span></th>
                                    <th><span>业绩</span></th>
                                </tr>
                                </thead>
                                <tbody>
                                    @if($count == 0)
                                        <tr>
                                            <td colspan="7">
                                                暂时没有代理人
                                            </td>
                                        </tr>
                                    @else
                                        @foreach($list as $value)
                                            <tr>
                                                <td>{{ $value->job_number }}</td>
                                                <td>{{ $value->area }}</td>
                                                <td>{{ $value->position }}</td>
                                                <td>{{ $value->ureal_name }}</td>
                                                <td>{{ $value->code }}</td>
                                                <td>{{ $value->phone }}</td>
                                                <td>{{ $value->email }}</td>
                                                <td>{{ $value->address }}</td>
                                                <td>
                                                    <a href="{{url('/backend/boss/agent/detail/'.$value->uid)}}" type="button" class="btn btn-success">查看</a>
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

