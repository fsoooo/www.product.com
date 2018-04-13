@extends('backend.layout.base')
@section('content')
    <meta name="_token" content="{{ csrf_token() }}"/>
    <style>
        a{
            color: white;
            text-decoration: none;
        }
        .line  {
            font-size: 18px;
            line-height: 100%;
            vertical-align: bottom;
        }
        .line-right,.line-left{
            height:60px;
            line-height: 60px;
            display: inline-block;
            vertical-align: middle;
        }
        .line-left{

            width:10%;
            text-align: right;
            /*text-align: justify;*/
            /*text-align-last: justify;*/
            margin-right: 5%;
        }
        .line-right{
            border-bottom: 1px dashed black;
            width: 60%;
        }
        .clearFix{content:".";display:block;height:0;clear:both;visibility:hidden}
    </style>
    <div id="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <ol class="breadcrumb">
                            <li><a href="{{ url('/backend') }}">主页</a></li>
                            <li ><span>售后管理</span></li>
                            <li class="active"><span>理赔管理</span></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    <li><a href="{{ url('/backend/claim/index') }}">理赔列表</a></li>
                                    {{--<li><a href="{{ url('/backend/claim/get_claim/no_deal') }}">未处理</a></li>--}}
                                    {{--<li><a href="{{ url('/backend/claim/get_claim/deal') }}">已处理</a></li>--}}
                                    <li class="active"><a>理赔详情</a></li>
                                </ul>
                                <div class="tab-content">
                                    @include('backend.layout.alert_info')
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        <h3><p>订单详情</p></h3>
                                        <div class="panel-group accordion" id="account">
                                            <div class="panel panel-default">
                                                <table id="user" class="table table-hover" style="clear: both">
                                                    <tbody>

                                                    <tr>
                                                        <td width="15%">产品信息</td>
                                                        <td width="65%">
                                                            <ul>

                                                                <li><img src="/{{$insurance->company->logo}}">
                                                                     <a href="{{$insurance->company->url}}">{{$insurance->company->name}}</a></li>
                                                                <li>{{$insurance->name}}</li>
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>投保人信息</td>
                                                        <td>
                                                            <ul>
                                                                <li>姓名：{{$policy->name}}</li>
                                                                <li>邮箱地址：{{$policy->email}}</li>
                                                                <li>联系方式：{{$policy->phone}}</li>
                                                                <li>家庭住址：{{$policy->address}}</li>
                                                                <li>身份证号：{{$policy->card_id}}</li>

                                                            </ul>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>被保人信息</td>
                                                        <td>
                                                            <ul>
                                                                <li>姓名：{{$insure->name}}</li>
                                                                <li>联系方式：{{$insure->phone}}</li>
                                                                <li>身份证号：{{$insure->card_id}}</li>
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>订单信息</td>
                                                        <td>
                                                            <ul>
                                                                <li>订单号：{{$order->order_no}}</li>
                                                                <li>联合订单号：{{$order->union_Order_code}}</li>
                                                                @foreach($company as $value)
                                                                    @if($value['account_id']==$order->create_account_id)
                                                                        <li>中介公司：{{$value['name']}}</li>
                                                                    @endif
                                                                @endforeach
                                                                <li>保费：
                                                                    {{$insure->premium/100}}
                                                                </li>
                                                                <li>保额:
                                                                    {{$insure->coverage}}
                                                                </li>
                                                                <li>
                                                                    保障开始时间:
                                                                    {{$insure['ins_start_time']/1000000}}
                                                                </li>
                                                            </ul>
                                                        </td>
                                                    </tr>
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
    </div>
@stop

