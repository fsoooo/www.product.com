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
                            <li class="active"><span>工作流管理</span></li>
                        </ol>
                        <h1>工作流信息</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    <li><a href="{{ url('/backend/flow/node') }}">节点列表</a></li>
                                    <li><a href="{{ url('backend/flow/add_node') }}">新增</a></li>
                                    <li  class="active"><a href="">节点详情</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        @include('backend.layout.alert_info')
                                        <h3><p>基本信息</p></h3>
                                        <div class="panel-group accordion" id="account">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h4 class="panel-title">
                                                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion2" href="#account-message">
                                                            节点基本信息
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="account-message" class="panel-collapse ">
                                                    <table id="basic" class="table table-hover" style="clear: both">
                                                        <tbody>
                                                        <tr>
                                                            <td style="width: 35%">节点名称</td>
                                                            <td>
                                                                {{ $node_detail->node_name }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>所属工作流</td>
                                                            <td>
                                                                {{ $node_detail->node_flow->flow_name }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>对应方法</td>
                                                            <td>
                                                                {{ $node_detail->node_route->route_name }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>节点描述</td>
                                                            <td>
                                                                {{ $node_detail->describe }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>节点状态</td>
                                                            <td>
                                                                {{ $node_detail->status }}
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <h3><p>可行行为规范</p></h3>
                                        <div class="panel-group accordion" id="bill">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h4 class="panel-title">
                                                        <a class="accordion-toggle collapsed md-trigger add-behaviour"  index="1"  data-modal="modal-8" style="cursor: pointer;">
                                                            节点可行行为规范
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="bill-block" class="panel-collapse ">
                                                    <div class="panel-body" style="padding-bottom: 0">
                                                        <table id="product" class="table table-hover" style="clear: both">
                                                            <thead>
                                                                <th>行为规范名称</th>
                                                                <th>行为规范描述</th>
                                                                <th>行为规范状态</th>
                                                            </thead>
                                                            <tbody>
                                                                @if($possible_count == 0)
                                                                    <tr>
                                                                        <td colspan="5">
                                                                            暂无可行规范约束
                                                                        </td>
                                                                    </tr>
                                                                @else
                                                                    @foreach($possible as $value)
                                                                        <tr>
                                                                            <td>{{ $value->node_condition_status->status_name }}</td>
                                                                            <td>{{ $value->node_condition_status->describe }}</td>
                                                                            <td>{{ $value->status }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <h3><p>禁止行为规范</p></h3>
                                        <div class="panel-group accordion" id="bill">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h4 class="panel-title">
                                                        <a class="accordion-toggle collapsed md-trigger add-behaviour"  index="0"  data-modal="modal-8" style="cursor: pointer;">
                                                            禁止行为规范
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="bill-block" class="panel-collapse ">
                                                    <div class="panel-body" style="padding-bottom: 0">
                                                        <table id="product" class="table table-hover" style="clear: both">
                                                            <thead>
                                                            <th>行为规范名称</th>
                                                            <th>行为规范描述</th>
                                                            <th>返回错误信息</th>
                                                            <th>行为规范状态</th>
                                                            </thead>
                                                            <tbody>
                                                            @if($impossible_count == 0)
                                                                <tr>
                                                                    <td colspan="5">
                                                                        暂无禁止规范约束
                                                                    </td>
                                                                </tr>
                                                            @else
                                                                @foreach($impossible as $value)
                                                                    <tr>
                                                                        {{--<td>{{ $value->node_condition_status->status_name }}</td>--}}
                                                                        {{--<td>{{ $value->node_condition_status->describe }}</td>--}}
                                                                        {{--<td>{{ $value->return_message }}</td>--}}
                                                                        {{--<td>{{ $value->status }}</td>--}}
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
    <div class="md-modal md-effect-8 md-hide" id="modal-8">
        <div class="md-content">
            <div class="modal-header">
                <button class="md-close close">×</button>
                <h4 class="modal-title">添加特定条件</h4>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ url('/backend/flow/add_node_status') }}" id="form" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="text" name="is_possible" value="" id="is-possible" hidden>
                    <input type="text" name="node_id" value="{{ $node_detail->id }}" hidden>
                    <div class="form-group">
                        <label for="exampleInputEmail1">选择分组<span class="red">*</span></label>
                        <select name="group_id" id="group_id" class="form-control">
                            <option value="0">选择分组</option>
                            @foreach($status_group as $value)
                                <option value="{{ $value->id }}">{{ $value->group_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">选择状态<span class="red">*</span></label>
                        <select name="status_id" id="status_id" class="form-control">
                            <option value="0">选择状态</option>
                        </select>
                    </div>
                    <div class="form-group" id="return-message" hidden>
                        <label for="exampleInputEmail1">返回错误信息</label>
                        <input type="text" class="form-control" name="return_message" placeholder="请输入错误返回信息" >
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="text-align: center;">
                <button type="button" id='btn' class="btn btn-primary">确认保存</button>
            </div>
        </div>
    </div>
    <div class="md-overlay"></div>

    <script src="/js/jquery-3.1.1.min.js"></script>
    <script charset="utf-8" src="/r_backend/js/modernizr.custom.js"></script>
    <script charset="utf-8" src="/r_backend/js/classie.js"></script>
    <script charset="utf-8" src="/r_backend/js/modalEffects.js"></script>
    <script>
       $(function(){
           var group_id = $('#group_id');
           var status_id = $('#status_id');
           var is_possible = $('#is-possible');
           var add_behaviour = $('.add-behaviour');
           var return_message = $('#return-message');
           add_behaviour.click(function(){
               var is_possible_val = $(this).attr('index');
               is_possible.val(is_possible_val);
               if(is_possible_val == 0){
                   return_message.removeAttr('hidden');
               }else {
                   return_message.attr('hidden','');
               }
           })




           group_id.change(function () {
               var group_id_val = $('#group_id option:selected').val();
               if(group_id_val != 0){
                   $.ajax({
                       type: "post",
                       dataType: "json",
                       async: true,
                       url: "/backend/status/get_status_ajax",
                       data: 'group_id='+group_id_val,
                       headers: {
                           'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                       },
                       success: function(data){
                           var _data = data['data'];
                           var len = _data.length;
                           var str = '<option value="0">选择状态</option>';
                           for(var i = 0;i<len;i++){
                               str+='<option value="'+_data[i]["id"]+'">'+_data[i]['status_name']+'</option>';
                           }
                           status_id.html(str);
                       },error: function () {
                           alert('发送失败');
                       }
                   });
               }
           })
           var btn = $('#btn');
           var form = $('#form');
           btn.click(function(){
                form.submit();
           })



       })
    </script>
@stop

