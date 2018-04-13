@extends('backend.layout.base')
@section('content')
    <style>
        th,td{
            text-align: center;
        }
    </style>
    <meta name="_token" content="{{ csrf_token() }}"/>
    <div id="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <ol class="breadcrumb">
                            <li><a href="{{ url('/backend') }}">主页</a></li>
                            <li><span>客户管理</span></li>
                            <li><span>客户池</span></li>
                            @if($type == 'apply')
                                <li class="active"><a href="{{ url('/backend/relation/distribute/apply/'.$cust_id) }}">代理申请</a></li>
                            @elseif($type == 'free')
                                <li class="active"><a href="{{ url('/backend/relation/distribute/free/'.$cust_id) }}">自由分配</a></li>
                            @endif
                        </ol>
                        <h1>分配客户 {{ $cust->name }} 代理权</h1>
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
                                @endif
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade in active" id="tab-accounts">
                                    @if($type == 'apply')
                                        <h3><p id="cust-name" cust_name="{{ $cust->name }}" cust_code="{{ $cust->code }}">申请列表</p></h3>
                                    @elseif($type == 'free')
                                        <h3><p id="cust-name" cust_name="{{ $cust->name }}" cust_code="{{ $cust->code }}">分配客户</p></h3>
                                    @endif
                                    @include('backend.layout.alert_info')
                                    <div class="panel-group accordion" id="account">
                                        <div class="panel panel-default">
                                    @if($type == 'apply')
                                            <table id="user" class="table table-hover" style="clear: both">
                                            <thead>
                                            <tr>
                                                <th><span>申请人工号</span></th>
                                                <th><span>申请人姓名</span></th>
                                                <th><span>申请时间</span></th>
                                                <th><span>备注说明</span></th>
                                                <th><span>操作说明</span></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if($count == 0)
                                                <tr>
                                                    <td colspan="7" style="text-align: center;">
                                                        暂时没有申请记录,去<a href="{{ url('/backend/relation/distribute/free/'.$cust_id) }}">自由分配</a>
                                                    </td>
                                                </tr>
                                            @else
                                                @foreach($list as $value)
                                                    <tr>
                                                        <td>{{ $value->code }}</td>
                                                        <td>{{ $value->real_name }}</td>
                                                        <td>{{ $value->created_at }}</td>
                                                        <td style="width: 30%">{{ $value->apply_remarks }}</td>
                                                        <td>
                                                            <div class="btn-group">
                                                                <button type="button" class="btn btn-link dropdown-toggle" data-toggle="dropdown" >
                                                                    操作 <span class="caret"></span>
                                                                </button>
                                                                <ul class="dropdown-menu">
                                                                    <li><a href="{{ url('/backend/relation/agree_apply/'.$value->id) }}">同意申请</a></li>
                                                                    <li><a href="{{ url('/backend/relation/refuse_apply/'.$value->apply_id) }}">拒绝申请</a></li>
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                         @endif
                                        {{--{{ $list->links() }}--}}
                                    @elseif($type == 'free')
                                            <div class="main-box-body clearfix">
                                                <div class="table-responsive">
                                                    <table id="table-example-fixed" class="table table-hover">
                                                        <thead>
                                                        <tr>
                                                            <th>代理人工号</th>
                                                            <th>代理人姓名</th>
                                                            <th>区域</th>
                                                            <th>创建时间</th>
                                                            <th>操作</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                           @foreach($list as $value)
                                                               <tr>
                                                                   <td class="name-block">{{ $value->name }}</td>
                                                                   <td>{{ $value->real_name }}</td>
                                                                   <td>{{ $value->code }}</td>
                                                                   <td>{{ $value->created_at }}</td>
                                                                   <td><a href="javascript:" class="distribute-btn" id="{{ $value->id }}">分配</a></td>
                                                               </tr>
                                                           @endforeach
                                                        </tbody>
                                                    </table>
                                    @endif
                                </div>
                            </div>
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

        <script src="/js/jquery-3.1.1.min.js"></script>
        <script>
            var distribute_btn = $('.distribute-btn');
            var name_block = $('.name-block');
            var cust_name_block = $('#cust-name');
            var cust_name = cust_name_block.attr('cust_name');
            var cust_code = cust_name_block.attr('cust_code');
            distribute_btn.each(function(i,item){
                $(item).click(function(){
                    var agent_name = name_block.eq(i).html();
                    var agent_id = $(item).attr('id');
                    if(confirm('确定要将客户 '+cust_name+' 分配给 '+agent_name+' 吗')){
                        location.href = '/backend/relation/distribute_cust/'+cust_code+'/'+agent_id;
                    }else{

                    }
                })

            })
        </script>

@stop