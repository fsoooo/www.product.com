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
                            <li class="active"><span>保单管理</span></li>
                        </ol>
                        <h1>保单分配</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                            <ul class="nav nav-tabs">

                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade in active" id="tab-accounts">
                                    <h3><p>保单分配</p></h3>
                                    {{--<h3><p id="cust-name" cust_name="{{ $cust->name }}" cust_code="{{ $cust->code }}">分配客户</p></h3>--}}
                                    @include('backend.layout.alert_info')
                                    <div class="panel-group accordion" id="account">
                                        <div class="panel panel-default">
                                            <div class="main-box-body clearfix">
                                                <div class="table-responsive">
                                                <table id="table-example-fixed" class="table table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th>代理人名称</th>
                                                        <th>真实姓名</th>
                                                        <th>代理人身份证号</th>
                                                        <th>创建时间</th>
                                                        <th>操作</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @if($count == 0)
                                                        <tr>
                                                            <td colspan="5">暂无代理人</td>
                                                        </tr>
                                                    @else
                                                        @foreach($agent_list as $value)
                                                            <tr>
                                                                <td>{{ $value->name }}</td>
                                                                <td>{{ $value->real_name }}</td>
                                                                <td>{{ $value->code }}</td>
                                                                <td>{{ $value->created_at }}</td>
                                                                <td>cc</td>
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