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
                                        <li><span><a href="/backend/product/productlists">产品池</a></span></li>
                                        <li class="active"><span><a href="/backend/product/productlists">产品池列表</a></span></li>
                                    </ol>
                                    <header id="email-header" class="clearfix">
                                        <div id="email-header-tools" style="margin:0 auto; margin-left:10px;">
                                            <div class="btn-group">
                                               @if($res == '0')
                                                    <script type="text/javascript">
                                                            window.onload=function getData(){
                                                                $.ajax( {
                                                                    type : "get",
                                                                    url : 'getproducts?page=1',
                                                                    dataType : 'json',
                                                                    success:function(msg){
                                                                        if(msg.status=='0'){
//                                                                            alert('获取数据成功');
                                                                            window.location = location;
                                                                        }else if(msg.status=='1'){
                                                                            alert(msg.message);
                                                                            //alert(msg.message);
                                                                            // $('#check').html('<font color="green">'+msg.message+'</font>');
                                                                        }
                                                                    }
                                                                });
                                                            }
                                                                                                    </script>
                                                @else
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
                                                <a onclick="refresh()"><button class="btn btn-primary" type="button" title="更新"  >
                                                    <i class="fa fa-refresh"></i>
                                                </button></a>
                                                <a onclick="doSelected()"><button class="btn btn-primary" type="button" title="同步" data-toggle="tooltip" data-placement="bottom">
                                                    <i class="fa fa-plus-circle fa-lg"></i>
                                                </button></a>
                                            </div>
                                        </div>
                                    </header>
                                </div>
                            </div>
                            <link href="/r_backend/win/window/window.css" rel="stylesheet" />
                            <script src="/r_backend/win/jquery-1.7.1.min.js"></script>
                            <script src="/r_backend/win/window/window.js"></script>

                            <script type="text/javascript">
                                                function refresh(){
                                                                    window.location.href=location;
                                                                }
                                                function doSelected(){
                                                                    var choose = document.getElementsByName("choose");
                                                                    var num = choose.length;
                                                                    var id = "";
                                                                    for(var index =0 ; index<num ; index++){
                                                                        if(choose[index].checked){
                                                                            id += choose[index].value + ",";
                                                                        }
                                                                    }
                                                                    if(id!=""){
                                                                        if(window.confirm("确定添加所选产品？")){
                                                                            win.confirm('系统提示', '再次添加已同步商品会清除已同步商品所有设置！！', function(r){
//                                                                                win.alertEx('结果：' + (r ? "是" : "不是"))
                                                                                var msg = "取消操作！！";
                                                                               r ? $.ajax( {
                                                                                        type : "get",
                                                                                        url : 'addproductlists?id=' + id,
                                                                                        dataType : 'json',
                                                                                        success:function(msg){
                                                                                            if(msg.status = true){
                                                                                            win.alertEx(msg.message);
//                                                                                            window.location = location;
                                                                                            }else{
                                                                                            win.alertEx(msg.message);
//                                                                                                window.location = location;
                                                                                            }
                                                                                        }
                                                                                    }):win.alertEx(msg)
                                                                            });
                                                                        }
                                                                    }else{
                                                                        win.alertEx("请选择要添加的商品");
                                                                        }
                                                                    }
                                             </script>
                            <div class="main-box-body clearfix">
                                <div class="table-responsive">
                                            <table class="table user-list table-hover">
                                                <thead>
                                                <tr>
                                                    <th></th>
                                                    <th class="text-center"><span>保险产品简称</span></th>
                                                    <th class="text-center">保险产品全称</th>
                                                    {{--<th class="text-center">产品唯一码</th>--}}
                                                    <th class="text-center"><span>保险产品分类</span></th>
                                                    <th class="text-center"><span>保险公司</span></th>
                                                    <th>状态</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($res as $k => $v)
                                                    <tr>
                                                        <td class="chbox">
                                                            <input type="checkbox" name="choose" value="{{$v['p_code']}}" />
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="productsinfo?id={{$v['id']}}">{{$v['id']}}
                                                                {{$v['display_name']}}
                                                            </a>
                                                        </td>
                                                        <td class="text-center">
                                                            {{$v['name']}}
                                                        </td>
                                                        {{--<td class="text-center">--}}
                                                            {{--{{$v['p_code']}}--}}
                                                        {{--</td>--}}
                                                        <td class="text-center">
                                                            {{$v['category']['name']}}
                                                        </td>
                                                        <td class="text-center">
                                                            {{$v['company']['display_name']}}
                                                        </td>
                                                        <td style="width: 15%;">
                                                                @if(!empty($product_id))
                                                                   @if(in_array($v['id'],$product_id))
                                                                        <span class="label label-success">已同步</span>
                                                                    @else
                                                                        <span class="label label-danger">未同步</span>
                                                                    @endif
                                                                 @else
                                                                <span class="label label-danger">未同步</span>
                                                                @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                </div>
                            </div>
                            @if($currentPage== '1')
                                @if($currentPage==$pages)
                                    <a onclick='getData("{{$currentPage}}")'><button>当前页:{{$currentPage }}</button></a>
                                    {{--<a onclick='getData("{{$currentPage+1}}")'><button>下一页</button></a>--}}
                                    {{--<a onclick='getData("{{$pages}}")'><button>尾页</button></a>--}}
                                    <a><button>总页数{{$pages }}</button></a>
                                @elseif($currentPage>$pages)
                                    暂无数据
                                    {{--<a onclick='getData("{{$currentPage}}")'><button>当前页:{{$currentPage }}</button></a>--}}
                                    {{--<a onclick='getData("{{$currentPage+1}}")'><button>下一页</button></a>--}}
                                    {{--<a onclick='getData("{{$pages}}")'><button>尾页</button></a>--}}
                                    {{--<a><button>总页数{{$pages }}</button></a>--}}
                                @else
                                    <a onclick='getData("{{$currentPage}}")'><button>当前页:{{$currentPage }}</button></a>
                                    <a onclick='getData("{{$currentPage+1}}")'><button>下一页</button></a>
                                    <a onclick='getData("{{$pages}}")'><button>尾页</button></a>
                                    <a><button>总页数{{$pages }}</button></a>
                                @endif
                            @elseif($currentPage==$pages)
                                <a onclick='getData("1")'><button>首页</button></a>
                                <a onclick='getData("{{$currentPage-1}}")'><button>上一页</button></a>
                                <a onclick='getData("{{$currentPage}}")'><button>当前页:{{$currentPage }}</button></a>
                                <a><button>总页数{{$pages }}</button></a>
                            @else
                                <a onclick='getData("1")'><button>首页</button></a>
                                <a onclick='getData("{{$currentPage-1}}")'><button>上一页</button></a>
                                <a onclick='getData("{{$currentPage}}")'><button>当前页:{{$currentPage }}</button></a>
                                <a onclick='getData("{{$currentPage+1}}")'><button>下一页</button></a>
                                <a onclick='getData("{{$pages}}")'><button>尾页</button></a>
                                <a><button>总页数{{$pages }}</button></a>
                                @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <script type="text/javascript">
                function getData(page){
                    $.ajax({
                        type : "get",
                        url : 'getproducts?page='+page,
                        dataType : 'json',
                        success:function(msg){
                            if(msg.status =true){
                                window.location = location;
                            }else{
                                alert("获取数据失败！");
                            }
                        }
                    });
                }
            </script>
@stop
