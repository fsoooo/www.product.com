@extends('backend.layout.base')
@section('content')
    <meta name="_token" content="{{ csrf_token() }}"/>
    <style>
        th,td{
            text-align: center;
        }
        a:hover{
            text-decoration: none;
        }
    </style>
    <div id="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <ol class="breadcrumb">
                            <li><a href="{{ url('/backend') }}">主页</a></li>
                            <li><span>客户管理</span></li>
                            <li><span><a href="/backend/relation/cust/all">客户池</a></span></li>
                            @if($type == 'person')
                                <li class="active"><span><a href="/backend/relation/cust/person">个人用户</a></span></li>
                            @elseif($type == 'company')
                                <li class="active"><span><a href="/backend/relation/cust/company">企业用户</a></span></li>
                            @endif
                        </ol>

                    </div>
                </div>
                <div class="row">

                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    @if($type == 'all')
                                        <li class="active"><a href="{{ url('backend/relation/cust/all') }}">客户池</a></li>
                                        <li><a href="{{ url('backend/relation/cust/person') }}">个人客户</a></li>
                                        <li><a href="{{ url('backend/relation/cust/company') }}">企业客户</a></li>
                                    @elseif($type == 'company')
                                        <li><a href="{{ url('backend/relation/cust/all') }}">客户池</a></li>
                                        <li><a href="{{ url('backend/relation/cust/person') }}">个人客户</a></li>
                                        <li class="active"><a href="{{ url('backend/relation/cust/company') }}">企业客户</a></li>
                                    @elseif($type == 'person')
                                        <li><a href="{{ url('backend/relation/cust/all') }}">客户池</a></li>
                                        <li class="active"><a href="{{ url('backend/relation/cust/person') }}">个人客户</a></li>
                                        <li><a href="{{ url('backend/relation/cust/company') }}">企业客户</a></li>
                                    @endif
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-accounts">

                                        @if($type == 'person')
                                            <div class="filter-block pull-right" style="margin-right: 20px;">
                                                <a href="{{ url('/backend/relation/add_cust/person') }}" style="color: white"><button class="md-trigger btn btn-primary mrg-b-lg">添加个人客户</button></a>
                                            </div>
                                        @elseif($type == 'company')
                                            <div class="filter-block pull-right" style="margin-right: 20px;">
                                                <a href="{{ url('/backend/relation/add_cust/company') }}" style="color: white;"><button class="md-trigger btn btn-primary mrg-b-lg">添加企业客户</button></a>
                                            </div>
                                        @endif
                                        @include('backend.layout.alert_info')
                                        <div class="panel-group accordion" id="account">
                                            <div class="panel panel-default">
                                                <table id="user" class="table table-hover" style="clear: both">
                                                    <thead>
                                                    <tr>
                                                        <th><span>客户名称</span></th>
                                                        <th><span>联系方式</span></th>
                                                        <th><span>邮箱地址</span></th>
                                                        <th><span>身份标识</span></th>
                                                        <th><span>类型</span></th>
                                                        <th><span>是否分配</span></th>
                                                        <th><span>联系记录</span></th>
                                                        <th><span>操作</span></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ( $list as $value )
                                                            <tr>
                                                                <td>{{ $value->name }}</td>
                                                                <td>{{ $value->phone }}</td>
                                                                <td>{{ $value->email }}</td>
                                                                <td>{{ $value->code }}</td>

                                                                <td>
                                                                    @if($value->type == 0)
                                                                        <a class="label label-primary" href="{{ url('/backend/relation/cust/person') }}" style="color: white">个人客户</a>
                                                                    @else
                                                                        <a  class="label label-info" href="{{ url('/backend/relation/cust/company') }}" style="color: white">企业客户</a>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($value->distribute=='0')
                                                                        <b style="color: #00aced">暂未分配</b>
                                                                    @else
                                                                        <b style="color: #1a8849">已经分配</b>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <a  class="label label-success" href="{{ url('/backend/relation/evolve/'.$value->code.'/'.$value->id) }}" style="color:white;">查看</a>
                                                                </td>
                                                                <td>
                                                                    <div class="btn-group">
                                                                        <button type="button" class="btn btn-link dropdown-toggle" data-toggle="dropdown" >
                                                                            操作 <span class="caret"></span>
                                                                        </button>
                                                                        <ul class="dropdown-menu">
                                                                            <li>
                                                                                @if($value->type == 0)
                                                                                    <a href="{{ url('/backend/relation/edit_cust/person/'.$value->id) }}">修改</a>
                                                                                @else
                                                                                    <a href="{{ url('/backend/relation/edit_cust/company/'.$value->id) }}">修改</a>
                                                                                @endif
                                                                            </li>
                                                                            <li>
                                                                                @if($value->distribute=='0')
                                                                                    <a href="javascript:" cust="{{ $value->code }}" cust_id="{{ $value->id }}" redistribute="off" class="distribute-btn">分配</a>
                                                                                @else
                                                                                    <a href="javascript:" cust="{{ $value->code }}" cust_id="{{ $value->id}}"  redistribute="on"  class="distribute-btn">重新分配</a>
                                                                                @endif

                                                                            </li>
                                                                            <li>
                                                                                <a href="javascript:" id="{{ $value->id }}" class="del-cust" name="{{ $value->name }}">删除</a>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </td>
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
            <footer id="footer-bar" class="row">
                <p id="footer-copyright" class="col-xs-12">
                    &copy; 2014 <a href="http://www.adbee.sk/" target="_blank">Adbee digital</a>. Powered by Centaurus Theme.
                </p>
            </footer>
        </div>
    </div>
    <script src="/js/jquery-3.1.1.min.js"></script>
    <script>
    $(function(){
        var distribute_btn = $('.distribute-btn');
        distribute_btn.click(function(){
            var code = $(this).attr('cust');
            var cust_id = $(this).attr('cust_id');
            var redistribute = $(this).attr('redistribute');
            $.ajax({
                type: "post",
                dataType: "json",
                async: true,
                //修改的地址，
                url: "/backend/relation/is_distribution_ajax",
                data: 'code='+code,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success: function(data){
                    var status = data['status'];
                    if(status == 200){
                        if(confirm("该客户已经被分配,是否重新分配")){
                            location.href = '/backend/relation/distribute/rewrite/'+cust_id;
                        }
                    }else {
                        location.href = '/backend/relation/distribute/apply/'+cust_id;
                    }
                },error: function () {
                    alert("添加失败");
                }
            });
        })


        //删除客户
        var del_cust = $('.del-cust');
        del_cust.each(function (i,item) {
            $(item).click(function(){
                var del_name = $(item).attr('name');
                var del_cust_id = $(item).attr('id');
                if(confirm('确定要删除客户 '+del_name+' 吗?')){
                    $.ajax({
                        type: "post",
                        dataType: "json",
                        async: true,
                        //修改的地址，
                        url: "/backend/relation/del_cust",
                        data: 'cust_id='+del_cust_id,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        },
                        success: function(data){
                            var status = data['status'];
                            if(status == 200){
                                alert('删除成功');
                                location.reload();
                            }else {
                                alert('删除失败');
                            }
                        },error: function () {
                            alert("删除失败");
                        }
                    });
                }
            })
        })
    })




    </script>
@stop

