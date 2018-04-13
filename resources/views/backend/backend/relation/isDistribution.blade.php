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
                            @if($type == 'distribution')
                                <li class="active"><a href="{{ url('/backend/relation/cust/distribution') }}">已分配客户</a></li>
                            @elseif($type == 'un_distribution')
                                <li class="active"><a href="{{ url('/backend/relation/cust/un_distribution') }}">未分配客户</a></li>
                            @endif
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                            <ul class="nav nav-tabs">
                                @if($type == 'distribution')
                                    <li class="active"><a href="{{ url('/backend/relation/cust/distribution') }}">已分配客户</a></li>
                                @elseif($type == 'un_distribution')
                                    <li class="active"><a href="{{ url('/backend/relation/cust/un_distribution') }}">未分配客户</a></li>
                                @endif
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade in active" id="tab-accounts">
                                    <h3><p>客户信息</p></h3>
                                    @include('backend.layout.alert_info')
                                    <div class="panel-group accordion" id="account">
                                        <div class="panel panel-default">
                                    <table id="user" class="table table-hover" style="clear: both">
                                        <thead>
                                        <tr>
                                            <th>客户名称</th>
                                            <th>联系方式</th>
                                            <th>邮箱地址</th>
                                            <th>身份标识</th>
                                            <th>类型</th>
                                            <th>联系记录</th>
                                            <th>操作</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if($count == 0)
                                            <tr>
                                                <td colspan="7" style="text-align: center;">暂无符合要求的客户</td>
                                            </tr>
                                        @else
                                            @foreach($list as $value)
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
                                                        <a href="{{ url('/backend/relation/evolve/'.$value->code.'/'.$value->id) }}" class="label label-success" style="color: white;">查看</a>
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
                                                                    <a href="javascript:" cust="{{ $value->code }}" cust_id="{{ $value->id }}" class="distribute-btn">分配</a>
                                                                </li>

                                                                @if($type == 'distribution')

                                                                @elseif($type == 'un_distribution')
                                                                    <li>
                                                                        <a href="javascript:" id="{{ $value->id }}" class="del-cust" name="{{ $value->name }}">删除</a>
                                                                    </li>
                                                                @endif

                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                    </table>
                                            {{--{{ $list->links() }}--}}
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
            distribute_btn.click(function(){
                var code = $(this).attr('cust');
                var cust_id = $(this).attr('cust_id');
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
                                location.href = '/backend/relation/distribute/apply/'+cust_id;
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
        </script>

@stop