@extends('backend.layout.base')
@section('content')
            <div id="content-wrapper">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-12">
                                <ol class="breadcrumb">
                                    <li><a href="http://product.wangshilei.cn/special/#">Home</a></li>
                                    <li class="active"><span>Widgets</span></li>
                                </ol>
                                <h1></h1>


                            </div>
                        </div>



                        <div class="row">
                            <div class="col-lg-5 col-md-8 col-sm-12 col-xs-12">
                                <div class="main-box">
                                    <div class="clearfix">
                                        <div class="infographic-box merged merged-top pull-left">
                                            <i class="fa fa-user purple-bg"></i>
                                            <span class="value purple">2.562</span>
                                            <span class="headline">Users</span>
                                        </div>
                                        <div class="infographic-box merged merged-top merged-right pull-left">
                                            <i class="fa fa-money green-bg"></i>
                                            <span class="value green">&dollar;12.400</span>
                                            <span class="headline">Income</span>
                                        </div>
                                    </div>
                                    <div class="clearfix">
                                        <div class="infographic-box merged pull-left">
                                            <i class="fa fa-eye yellow-bg"></i>
                                            <span class="value yellow">12.526</span>
                                            <span class="headline">Monthly Visits</span>
                                        </div>
                                        <div class="infographic-box merged merged-right pull-left">
                                            <i class="fa fa-globe red-bg"></i>
                                            <span class="value red">28</span>
                                            <span class="headline">Countries</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <div class="main-box small-graph-box red-bg">
                                    <span class="value">2.562</span>
                                    <span class="headline">Users</span>
                                    <div class="progress">
                                        <div style="width: 60%;" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class="progress-bar">
                                            <span class="sr-only">60% Complete</span>
                                        </div>
                                    </div>
                                    <span class="subinfo">
<i class="fa fa-arrow-circle-o-up"></i> 10% higher than last week
</span>
                                    <span class="subinfo">
<i class="fa fa-users"></i> 29 new users
</span>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-xs-12">
                                <div class="main-box infographic-box">
                                    <i class="fa fa-shopping-cart emerald-bg"></i>
                                    <span class="headline">Purchases</span>
                                    <span class="value">{{$count}}</span>
                                </div>
                            </div>
                        </div>
                        <!--  -->






                        <div class="row">
                            <div class="col-lg-12">
                                <div class="main-box clearfix">
                                    <header class="main-box-header clearfix">
                                        <h2>Products</h2>
                                    </header>
                                    <div class="main-box-body clearfix">
                                        <ul class="widget-products">
                                            @foreach($nums as $num)
                                                <li>
                                                    @if($num->ispay==0)
                                                        <a href="payfor?id={{$num->order_num}}">
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



                                                        </a>
                                                    @elseif($num->ispay==1)

                                                        <a href="payinfo?id={{$num->order_num}}">
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



                                                        </a>
                                                    @elseif($num->ispay==2)

                                                        <a href="sms?id={{$num->company}}">
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



                                                        </a>
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