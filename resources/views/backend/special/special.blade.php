@extends('backend.layout.base')
@section('content')
            <div id="content-wrapper">
                <div class="row">
                    @foreach($res as $value)
                    <div class="modal fade bs-example-modal-lg"  id = "model{{$value['id']}}"  tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="main-box clearfix" style="min-height: 1100px;">
                                            <div class="tabs-wrapper tabs-no-header">

                                                <div class="tab-content">
                                                    <div class="tab-pane fade in active" id="tab-accounts">
                                                        <h3><p>处理工单</p></h3>
                                                        @include('backend.layout.alert_info')
                                                        <div class="panel-group accordion" id="operation">
                                                            <div class="panel panel-default">
                                                                <form id="my_form">
                                                                    <table id="user" class="table table-hover" style="clear: both">
                                                                        <tbody>
                                                                        <tr>
                                                                            <td width="15%">工单发起方</td>
                                                                            <td width="60%">
                                                                                {{$value['company']}}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td width="15%">工单标题</td>
                                                                            <td width="60%">
                                                                                {{$value['title']}}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>工单内容</td>
                                                                            <td style="height: 214px;width: 666px">
                                                                                {{$value['content']}}
                                                                            </td>

                                                                        </tr>
                                                                        <tr>
                                                                            <td width="15%">回调地址</td>
                                                                            <td width="60%">
                                                                                {{$value['url']}}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>发起时间</td>
                                                                            <td>
                                                                                {{$value['created_at']}}
                                                                            </td>
                                                                        </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </form>
                                                            </div>
                                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
                    @endforeach
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-12">
                                <ol class="breadcrumb">
                                    <li><a href="/backend/">主页</a></li>
                                    <li ><span><a href="/backend/special/nospecial">工单管理</a></span></li>
                                    <li class="active"><a href="{{ url('/backend/special/special') }}">已处理工单</a></li>
                                </ol>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="tabs-wrapper tabs-no-header">
                                    <ul class="nav nav-tabs">
                                        <li><a href="{{ url('/backend/special/nospecial') }}">未处理工单</a></li>
                                        <li class="active"><a href="{{ url('/backend/special/special') }}">已处理工单</a></li>
                                        <li><a href="{{ url('/backend/special/recspecial') }}">工单回收站</a></li>
                                    </ul>
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
                                                    <th>&nbsp;<span id="check"></span> </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($res as $value )
                                                <tr>
                                                    <td>
                                                        @if(array_key_exists($value['company'],$company))
                                                            {{$company[$value['company']]}}
                                                        @endif
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
                                                        @elseif($value['status']==3)
                                                        <span class="label label-danger">处理失败</span>
                                                            @endif
                                                    </td>
                                                    <td style="width: 20%;">
                                                        <a href="#" class="table-link" data-toggle="modal" data-target="#model{{$value['id']}}">
                                                                <span class="fa-stack">
                                                                <i class="fa fa-square fa-stack-2x"></i>
                                                                <i class="fa fa-search-plus fa-stack-1x fa-inverse"></i>
                                                                </span>
                                                        </a>
                                                        <a onclick="delspecial()" class="table-link danger">
                                                            <span class="fa-stack">
                                                                <i class="fa fa-square fa-stack-2x"></i>
                                                                <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                                            </span>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <script type="text/javascript">
                                                    function delspecial(){
                                                        var id ="{{$value['id']}}";
                                                        $.ajax( {
                                                            type : "get",
                                                            url : 'delspecial?id=' + id,
                                                            dataType : 'json',
                                                            success:function(msg){
                                                                if(msg.status == 1){
                                                                    $('#check').html('<font color="red">'+msg.message+'</font>');
                                                                }else{
                                                                    window.location = location;
                                                                    $('#check').html('<font color="green">'+msg.message+'</font>');
                                                                }
                                                            }
                                                        });
                                                    }
                                                </script>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    @include('backend.layout.pages')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

@stop