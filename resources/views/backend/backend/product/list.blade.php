@extends('backend.layout.base')
@section('content')
    <div id="content-wrapper" class="email-inbox-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div id="email-box" class="clearfix" style="min-height: 1200px;">
                    <div class="row">
                        <div class="col-lg-12">
                            <ol class="breadcrumb">
                                <li><a href="{{ url('/backend') }}">主页</a></li>
                                <li ><span>产品管理</span></li>
                                <li class="active"><span>产品列表</span></li>
                            </ol>
                            <header id="email-header" class="clearfix">
                                <div id="email-header-tools" style="margin:0 auto; margin-left:10px;">
                                    <div class="btn-group">
                                        <input type="checkbox" name="allChecked" id="allChecked" onclick="DoCheck()"/>全选/取消
                                        <script type="text/javascript">
                                            function DoCheck()
                                            {
                                                var ch=document.getElementsByName("choose");
                                                if(document.getElementsByName("allChecked")[0].checked==true)
                                                {
                                                    for(var i=0;i<ch.length;i++)
                                                    {
                                                        ch[i].checked=true;
                                                    }
                                                }else{
                                                    for(var i=0;i<ch.length;i++)
                                                    {
                                                        ch[i].checked=false;
                                                    }
                                                }
                                            }

                                            function refresh(){
                                                window.location.href=location;
                                            }


                                        </script>
                                    </div>
                                    <div class="btn-group">
                                        <a onclick="refresh()"><button class="btn btn-primary" type="button" title="刷新"  >
                                                <i class="fa fa-refresh"></i>
                                            </button></a>
                                        <a onclick="doSelectedUp()"><button class="btn btn-primary" type="button" title="选择上架" data-toggle="tooltip" data-placement="bottom">
                                                <i class="fa fa-circle"></i>
                                            </button></a>
                                        <a onclick="doSelectedDown()"><button class="btn btn-primary" type="button" title="选择下架" data-toggle="tooltip" data-placement="bottom">
                                                <i class="fa fa-exclamation"></i>
                                            </button></a>
                                        <a onclick="doSelectedDel()"><button class="btn btn-primary" type="button" title="选择删除" data-toggle="tooltip" data-placement="bottom">
                                                <i class="fa fa-trash-o"></i>
                                            </button></a>
                                        <a onclick="goRec()"><button class="btn btn-primary" type="button" title="回收站" data-toggle="tooltip" data-placement="bottom">
                                                已删除
                                            </button></a>
                                    </div>
                                </div>
                            </header>
                        </div>
                    </div>
                    <script type="text/javascript">
                        function refresh(){
                            window.location.href=location;
                        }
                        function goRec(){
                            window.location.href='productrec';
                        }
                        function doSelectedUp(){
                            var choose = document.getElementsByName("choose");
                            var num = choose.length;
                            var id = "";
                            for(var index =0 ; index<num ; index++){
                                if(choose[index].checked){
                                    id += choose[index].value + ",";
                                }
                            }
                            // console.log(id);
                            if(id!=""){
                                if(window.confirm("确定上架所选产品？")){
                                    // alert(1);
                                    // console.log(id);
                                    $.ajax( {
                                        type : "get",
                                        url : 'productup?id=' + id,
                                        dataType : 'json',
                                        success:function(msg){
                                            if(msg.status == 1){
                                                alert(msg.message);
                                                $('#check').html('<font color="red">'+msg.message+'</font>');
                                            }else{
                                                alert(msg.message);
                                                // $('#check').html('<font color="green">'+msg.message+'</font>');
                                                window.location.href=location;
                                            }

                                        }
                                    });
                                }
                            }else{
                                alert("请选择要上架的商品");
                            }
                        }
                        function doSelectedDown(){
                            var choose = document.getElementsByName("choose");
                            var num = choose.length;
                            var id = "";
                            for(var index =0 ; index<num ; index++){
                                if(choose[index].checked){
                                    id += choose[index].value + ",";
                                }
                            }
                            if(id!=""){
                                if(window.confirm("确定下架所选产品？")){
                                    // alert(1);
                                    console.log(id);
                                    $.ajax( {
                                        type : "get",
                                        url : 'productdown?id=' + id,
                                        dataType : 'json',
                                        success:function(msg){
                                            if(msg.status == 1){
                                                alert(msg.message);
                                                $('#check').html('<font color="red">'+msg.message+'</font>');
                                            }else{
                                                alert(msg.message);
                                                // $('#check').html('<font color="green">'+msg.message+'</font>');
                                                window.location.href=location;
                                            }

                                        }
                                    });
                                }
                            }else{
                                alert("请选择要下架的商品");
                            }
                        }
                        function doSelectedDel(){
                            var choose = document.getElementsByName("choose");
                            var num = choose.length;
                            var id = "";
                            for(var index =0 ; index<num ; index++){
                                if(choose[index].checked){
                                    id += choose[index].value + ",";
                                }
                            }
                            if(id!=""){
                                if(window.confirm("确定删除所选产品？")){
                                    // alert(1);
                                    console.log(id);
                                    $.ajax( {
                                        type : "get",
                                        url : 'productdel?id=' + id,
                                        dataType : 'json',
                                        success:function(msg){
                                            if(msg.status == 1){
                                                alert(msg.message);
                                                $('#check').html('<font color="red">'+msg.message+'</font>');
                                            }else{
                                                alert(msg.message);
                                                // $('#check').html('<font color="green">'+msg.message+'</font>');
                                                window.location.href=location;
                                            }

                                        }
                                    });
                                }
                            }else{
                                alert("请选择要删除的商品");
                            }
                        }
                    </script>
                    <div class="main-box-body clearfix">
                        <div class="table-responsive">
                            {{--<ul id="email-list">--}}
                            <table class="table user-list table-hover">
                                <thead>
                                <tr>
                                    <th></th>
                                    {{--<th class="text-center"><span>产品ID</span></th>--}}
                                    <th class="text-center">名称</th>
                                    {{--<th class="text-center">产品码</th>--}}
                                    <th class="text-center"><span>险种</span></th>
                                    <th class="text-center"><span>产品上架时间</span></th>
                                    <th>状态</th>
                                    <th>标签</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($res as $k => $v)
                                    <tr>
                                        <td class="chbox">
                                            <input type="checkbox" name="choose" value="{{$v['id']}}" />
                                        </td>
                                        {{--<td class="text-center">--}}
                                            {{--{{$v['id']}}--}}
                                        {{--</td>--}}
                                        <td class="text-center">
                                            <a href="productinfo?id={{$v['id']}}">{{$v['name']}} </a>
                                        </td>
                                        {{--<td class="text-center">--}}
                                            {{--{{$v['p_code']}}--}}
                                        {{--</td>--}}
                                        <td class="text-center">
                                            {{$v['category']['name']}}
                                        </td>


                                        <td class="text-center">
                                            {{$v['created_at']}}
                                        </td>
                                        <td style="width: 15%;">
                                            @if($status[$k]==1)
                                                <span class="label label-success">已上架</span>
                                            @elseif($status[$k]==0)
                                                <span class="label label-danger">未上架</span>
                                            @elseif($status[$k]==2)
                                                <span class="label label-danger">已删除</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(in_array($v['id'], $labels))
                                                标签已选择
                                                <a href="updateproductlabels?product_id={{$v['id']}}" class="off">修改标签</a>
                                            @else
                                                <a href="addproductlabel?product_id={{$v['id']}}" class="off">请选择标签</a>
                                            @endif
                                        </td>

                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        @include('backend.layout.pages')
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop