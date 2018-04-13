@extends('backend.layout.base')
@section('content')
    <meta name="_token" content="{{ csrf_token() }}"/>
    <div id="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <ol class="breadcrumb">
                            <li><a href="{{ url('/backend') }}">主页</a></li>
                            <li><span>客户管理</span></li>
                            <li ><span>已分配客户</span></li>
                            <li class="active"><span>重新分配</span></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                            <ul class="nav nav-tabs">
                                @if($type == 'apply')
                                    <li class="active"><a href="{{ url('/backend/relation/distribute/apply/'.$cust_id) }}">代理申请</a></li>
                                    <li><a href="{{ url('/backend/relation/distribute/free/'.$cust_id) }}">自由分配</a></li>
                                @elseif($type == 'free')
                                    <li><a href="{{ url('/backend/relation/distribute/apply/'.$cust_id) }}">代理申请</a></li>
                                    <li class="active"><a href="{{ url('/backend/relation/distribute/free/'.$cust_id) }}">自由分配</a></li>
                                @elseif($type == 'rewrite')
                                    <li><a href="{{ url('/backend/relation/distribute/apply/'.$cust_id) }}">代理申请</a></li>
                                    <li class="active"><a href="{{ url('/backend/relation/distribute/free/'.$cust_id) }}">重新分配</a></li>

                                @endif
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade in active" id="tab-accounts">
                                    @if($type == 'apply')
                                        <h3><p id="cust-name" cust_name="{{ $cust->name }}" cust_code="{{ $cust->code }}">申请列表</p></h3>
                                    @elseif($type == 'free')
                                        <h3><p id="cust-name" cust_name="{{ $cust->name }}" cust_code="{{ $cust->code }}">分配客户</p></h3>
                                    @elseif($type == 'rewrite')
                                        <h3><p id="cust-name" cust_name="{{ $cust->name }}" cust_code="{{ $cust->code }}">已分配客户</p></h3>
                                    @endif
                                    @include('backend.layout.alert_info')
                                    <div class="panel-group accordion" id="account">
                                        <div class="panel panel-default">

                                            <table id="user" class="table table-hover" style="clear: both">
                                            <thead>
                                            <tr>
                                                <th><span>申请人</span></th>
                                                <th><span>申请人身份标识</span></th>
                                                <th><span>申请时间</span></th>
                                                <th><span>备注说明</span></th>
                                                <th><span>操作说明</span></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($records as $value)
                                                    <tr>
                                                        <td>{{ $value->name }}</td>
                                                        <td>{{ $value->code }}</td>
                                                        <td>{{ $value->created_at }}</td>
                                                        <td style="width: 30%">{{ $value->apply_remarks }}</td>
                                                        @foreach($rule as $v)
                                                            @if($v->record_id == $value['id'] )
                                                                <td>
                                                                    <a href="javascript:" class="distribute-btn" name="{{ $value->name }}" id="{{ $v->agent_id }}">分配</a>给已申请代理人
                                                                </td>
                                                            @endif
                                                        @endforeach

                                                    </tr>
                                                @endforeach

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
        <script src="/js/jquery-3.1.1.min.js"></script>
        <script>
            var distribute_btn = $('.distribute-btn');
            var name_block = $('.name-block');
            var cust_name_block = $('#cust-name');
            var cust_name = cust_name_block.attr('cust_name');
            var cust_code = cust_name_block.attr('cust_code');
            distribute_btn.each(function(i,item){
                $(item).click(function(){
                    var agent_name = $(item).attr('name');
                    var agent_id = $(item).attr('id');
                    if(confirm('确定要将客户 '+cust_name+' 分配给 '+agent_name+' 吗')){
                        location.href = '/backend/relation/distribute_cust/'+cust_code+'/'+agent_id;
                    }else{

                    }
                })

            })
        </script>


@stop