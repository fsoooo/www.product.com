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
                                    <li class="active"><a href="{{ url('/backend/status/index') }}">状态列表</a></li>
                                    <li><a href="{{ url('/backend/status/group') }}">状态分组</a></li>
                                    {{--<li class="active"><a href="#">状态详情</a></li>--}}
                                </ul>

                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        @include('backend.layout.alert_info')
                                        <h3><p>状态详情</p></h3>
                                        <div class="panel-group accordion" id="account">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h4 class="panel-title">
                                                        {{--<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion2" href="#account-message">--}}
                                                            状态基本信息
                                                        {{--</a>--}}
                                                    </h4>
                                                </div>
                                                <div id="account-message" class="panel-collapse ">
                                                    <table id="basic" class="table table-hover" style="clear: both">
                                                        <tbody>
                                                        <tr>
                                                            <td width="35%">任务名</td>
                                                            <td>{{ $status_detail->status_name }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>所属分组</td>
                                                            <td>
                                                                @if($status_detail->status_group)
                                                                    {{ $status_detail->status_group->group_name }}
                                                                @else
                                                                    无分组
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>对应表名</td>
                                                            <td>{{ $status_detail->status_field->name }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>创建时间</td>
                                                            <td>{{ $status_detail->create_at }}</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <h3><p>上级状态</p></h3>
                                        <div class="panel-group accordion" id="bill">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h4 class="panel-title">
                                                        <a class="accordion-toggle collapsed md-trigger add-status"  index="0" style="cursor: pointer;"  data-modal="modal-8">
                                                            上级状态
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
                                                            @if($parent_count == 0)
                                                                <tr>
                                                                    <td colspan="3">暂无上级状态限制</td>
                                                                </tr>
                                                            @else
                                                                @foreach($parent_list as $value)
                                                                    <td>{{ $value->parent_rule_status->status_name }}</td>
                                                                    <td>{{ $value->parent_rule_status->describe }}</td>
                                                                    <td></td>
                                                                @endforeach
                                                            @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <h3><p>下级状态</p></h3>
                                        <div class="panel-group accordion" id="bill">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h4 class="panel-title">
                                                        <a class="accordion-toggle collapsed md-trigger add-status"  index="1" style="cursor: pointer;"  data-modal="modal-8">
                                                            下级状态
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
                                                            @if($children_count == 0)
                                                                <tr>
                                                                    <td colspan="3">暂无下级状态限制</td>
                                                                </tr>
                                                            @else
                                                                @foreach($children_list as $value)
                                                                    <td>{{ $value->children_rule_status->status_name }}</td>
                                                                    <td>{{ $value->children_rule_status->describe }}</td>
                                                                    <td></td>
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
    <div class="md-modal md-effect-8 md-hide" id="modal-8">
        <div class="md-content">
            <div class="modal-header">
                <button class="md-close close">×</button>
                <h4 class="modal-title">添加关系</h4>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ url('/backend/status/add_status_relation_submit') }}" id="form" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="text" nam="" value="{{ $status_detail->id }}" id="hidden-input" hidden>
                    <div class="form-group">
                        <label for="exampleInputEmail1" id="add-status-name"> </label>
                        <select name="" id="choose-status" class="form-control">
                            <option value="0">选择状态</option>
                            @foreach($status_list as $value)
                                <option value="{{ $value->id }}">{{ $value->status_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="text-align: center;">
                <button type="button" id='btn' class="btn btn-primary">添加</button>
            </div>
        </div>
    </div>
    <div class="md-overlay" id="add-condition-wrap"></div>

    <script src="/js/jquery-3.1.1.min.js"></script>
    <script charset="utf-8" src="/r_backend/js/modernizr.custom.js"></script>
    <script charset="utf-8" src="/r_backend/js/classie.js"></script>
    <script charset="utf-8" src="/r_backend/js/modalEffects.js"></script>
    <script>
        $(function () {

            var add_status_btn = $('.add-status');
            var add_status_name = $('#add-status-name');
            var hidden_input = $('#hidden-input');
            var choose_status = $('#choose-status');
            //封装一个方法，用来改变各个参数
            function change_parameter(_name,_input_name,_choose_name)
            {
                add_status_name.html(_name);
                hidden_input.attr('name',_input_name)
                choose_status.attr('name',_choose_name);
            }

            add_status_btn.click(function(){
                var add_type = $(this).attr('index');
                if(add_type == 0){
                    change_parameter('添加父级状态','status_id','parent_id');
                }else{
                    change_parameter('添加子级状态','parent_id','status_id');
                }
            })
            var btn = $('#btn');
            var form = $('#form');
            btn.click(function(){
                var add_status_name_val = $('#add-status-name option:selected').val();
                if(add_status_name_val == 0){
                    alert('请选择状态');
                    return false;
                }
                form.submit();
            })



        })
    </script>
@stop

