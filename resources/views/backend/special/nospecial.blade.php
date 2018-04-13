@extends('backend.layout.base')
@section('content')
            <div id="content-wrapper"><div class="row">
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
                                                                <button id="send-message-btn" class="btn btn-success" onclick="dospecial()">确认处理</button>
                                                                <div id="check"></div>
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
                        <script type="text/javascript">
                            (function () {
                                $('#my_form').find('input,textarea').attr('readonly',false);
                            }());
                            function dospecial(){
                                var id ="{{$value['id']}}";
                                var num ="{{$value['special_num']}}";
                                $.ajax( {
                                    type : "get",
                                    url : 'dospecial?id=' + id+'&special_num='+num,
                                    dataType : 'json',
                                    success:function(msg){
                                        if(msg.status == '1'){
                                            alert(msg.message);
                                            $('#check').html('<font color="red">'+msg.message+'</font>');
                                        }else if(msg.status == '2'){
                                            alert(msg.message);
                                            $('#check').html('<font color="green">'+msg.message+'</font>');
                                            window.location = location;
                                        }else if(msg.status == '0'){
                                            alert(msg.message);
                                            $('#check').html('<font color="green">'+msg.message+'</font>');
                                            window.location = location;
                                        }
                                    }
                                });
                            }
                        </script>
                    @endforeach
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-12">
                                <ol class="breadcrumb">
                                    <li><a href="/backend/">主页</a></li>
                                    <li ><span><a href="/backend/special/nospecial">工单管理</a></span></li>
                                    <li class="active"><a href="{{ url('/backend/special/nospecial') }}">未处理工单</a></li>
                                </ol>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="tabs-wrapper tabs-no-header">
                                    <ul class="nav nav-tabs">
                                        <li class="active"><a href="{{ url('/backend/special/nospecial') }}">未处理工单</a></li>
                                        <li><a href="{{ url('/backend/special/special') }}">已处理工单</a></li>
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
                                                    <th>&nbsp;  </th>
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
                                                                <span class="label label-default">未处理</span>
                                                        </td>
                                                        <td style="width: 20%;">
                                                            <a href="#" class="table-link" data-toggle="modal" data-target="#model{{$value['id']}}">
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
                                    </div>
                                    @include('backend.layout.pages')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script src="/r_backend/js/jquery.js"></script>

       @stop