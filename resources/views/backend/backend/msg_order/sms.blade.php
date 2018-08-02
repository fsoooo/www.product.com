@extends('backend.layout.base')
@section('content')
            <div id="content-wrapper">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-12">
                                <ol class="breadcrumb">
                                    <li><a href="http://www.product.com/special/#">主页</a></li>
                                    <li ><span>信息管理</span></li>
                                    <li class="active"><span>短信订单</span></li>
                                </ol>
                            </div>
                        </div>
                        <div class="row">
                        <div class="col-lg-12">
                                <div class="main-box clearfix">
                                    <header class="main-box-header clearfix">
                                        <h2>订单列表</h2>
                                        <hr>
                                    </header>
                                    <div class="main-box-body clearfix">
                                    <ul class="widget-products">
                                    @foreach($nums as $num)
                                    <li>
                                    @if($num->ispay==0)
                                     {{--<a href="payfor?id={{$num->order_num}}">--}}
                                        <span class="img">
                                         {{$num->company}}公司
                                        </span>
                                        <span class="product clearfix">
                                        <span class="name">
                                        订单号:{{$num->order_num}}
                                                            @if($num['ispay']==0)
                                                            <span class="label label-warning">未支付</span>
                                                            @elseif($num['ispay']==1)                                                     
                                                            <span class="label label-success">支付成功</span>
                                                            @else
                                                          
                                                            
                                                            <span class="label label-danger">支付失败</span>
                                                        
                                                            @endif
                                        </span>
                                        <span class="price">
                                        <i class="fa fa-money"></i> &dollar;{{$num->money}}
                                        </span>
                                        @if($num->create_time)
                                        <span >
                                        <i class="fa fa-certificate"></i> 下单时间：{{date("Y:m:d H:i:s",$num->create_time)}}
                                        </span>
                                        @endif

                                         <span >
                                        <i class="fa fa-certificate"></i> 订单类型：{{$num->name}}
                                        </span>

                                         <span >
                                        <i class="fa fa-certificate"></i> 支付方式：{{$num->pay_type}}
                                        </span>

                                        


                                        </span>
                                                
                                                    
                                                
                                    {{--</a>--}}
                                    @elseif($num->ispay==1)

                                     {{--<a href="payinfo?id={{$num->order_num}}">--}}
                                        <span class="img">
                                         {{$num->company}}公司
                                        </span>
                                        <span class="product clearfix">
                                        <span class="name">
                                        订单号:{{$num->order_num}}
                                                            @if($num['ispay']==0)
                                                            <span class="label label-warning">未支付</span>
                                                            @elseif($num['ispay']==1)                                                     
                                                            <span class="label label-success">支付成功</span>
                                                            @else
                                                          
                                                            
                                                            <span class="label label-danger">支付失败</span>
                                                        
                                                            @endif
                                        </span>
                                        <span class="price">
                                        <i class="fa fa-money"></i> &dollar;{{$num->money}}
                                        </span>
                                        
                                         @if($num->create_time)
                                        <span  class="warranty">
                                        <i class="fa fa-certificate"></i> 下单时间：{{date("Y:m:d H:i:s",$num->create_time)}}
                                        </span>
                                        @endif

                                         <span >
                                        <i class="fa fa-certificate"></i> 订单类型：{{$num->name}}
                                        </span>

                                         <span >
                                        <i class="fa fa-certificate"></i> 支付方式：{{$num->pay_type}}
                                        </span>

                                        


                                        </span>
                                                
                                                    
                                                
                                    {{--</a>--}}
                                    @elseif($num->ispay==2)

                                    {{--<a href="sms?id={{$num->company}}">--}}
                                        <span class="img">
                                         {{$num->company}}公司
                                        </span>
                                        <span class="product clearfix">
                                        <span class="name">
                                        订单号:{{$num->order_num}}
                                                            @if($num['ispay']==0)
                                                            <span class="label label-warning">未支付</span>
                                                            @elseif($num['ispay']==1)                                                     
                                                            <span class="label label-success">支付成功</span>
                                                            @else
                                                          
                                                            
                                                            <span class="label label-danger">支付失败</span>
                                                        
                                                            @endif
                                        </span>
                                        <span class="price">
                                        <i class="fa fa-money"></i> &dollar;{{$num->money}}
                                        </span>
                                        @if($num->create_time)
                                        <span  class="warranty">
                                        <i class="fa fa-certificate"></i> 下单时间：{{date("Y:m:d H:i:s",$num->create_time)}}
                                        </span>
                                        @endif
                                         <span >
                                        <i class="fa fa-certificate"></i> 订单类型：{{$num->name}}
                                        </span>
                                         <span >
                                        <i class="fa fa-certificate"></i> 支付方式：{{$num->pay_type}}
                                        </span>
                                        </span>
                                    {{--</a>--}}
                                    @endif
                                    </li>
                                    @endforeach
                                    </ul>
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

@stop