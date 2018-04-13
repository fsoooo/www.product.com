@extends('backend.layout.base')
@section('content')
    <div id="content-wrapper" class="email-inbox-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div id="email-box" class="clearfix">
                    <div class="row">
                        <div class="col-lg-12">
                            <ol class="breadcrumb">
                                <li><a href="{{ url('/backend') }}">主页</a></li>
                                <li ><span>产品管理</span></li>
                                <li><span><a href="/backend/product/productlists">产品池</a></span></li>
                                <li class="active"><span>产品池产品详情</span></li>
                            </ol>

                            <div id="email-detail" class="email-detail-nano" style="min-height:1200px;">
                                <div class="email-detail-nano-content">
                                    <div id="email-detail-inner">
                                        @if(empty($json))
                                            <h1>数据解析错误！！，调试中...</h1>
                                            @else
                                        <div id="email-detail-subject" class="clearfix">
                                            <span class="subject">
                                                <img  src="{{config('curl_product.company_logo_url')}}{{$json['res']['company']['logo']}}", style="height: 40px;width:80px;">
                                                保险公司:<a href="{{$json['res']['company']['url']}}">{{$json['res']['company']['name']}}</a></span>
                                            <span class="subject">保险公司Email:{{$json['res']['company']['email']}}</span>
                                            <span class="subject">保险公司编号:{{$json['res']['company']['code']}}</span>
                                            <span id="check"></span>
                                        </div>
                                        <div id="email-detail-sender" class="clearfix">
                                            <div class="users">
                                                <div class="from clearfix">
                                                    <div class="name">
                                                        <h1>产品名称：{{$json['res']['name']}}</h1>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tools">
                                                <div class="date">
                                                    产品发布时间：{{$json['res']['created_at']}}
                                                </div>
                                            </div>
                                        </div>
                                        <div id="email-body">
                                            <ul>
                                                <li>产品ID：{{$json['res']['id']}}</li>
                                                <li>产品全称：{{$json['res']['display_name']}}</li>
                                                <li>产品唯一码：{{$json['res']['p_code']}}</li>
                                                <li>产品介绍：{{$json['res']['content']}}</li>
                                                <li>险种：{{$json['res']['category']['name']}}</li>
                                                <li>产品佣金比：{{$json['res']['brokerage']}}</li>
                                                <li>产品来源：{{$json['res']['api_from_uuid']}}</li>
                                            </ul>
                                        </div>
                                        <div id="email-detail-attachments">
                                            <div id="email-attachments-header" class="clearfix">

                                                @foreach($json['clause'] as $v)


                                                <div class="headline">
                                                    <i class="fa fa-paperclip"></i>
                                                    <span>条款:</span>
                                                    {{--@foreach($v as $value)--}}
                                                    <ul>
                                                        <li>名称{{$v['name']}}</li>
                                                        <li>条款全称{{$v['display_name']}}</li>
                                                        <li>条款类型{{$v['type']}}</li>
                                                        <li><a href="{{config('curl_product.company_logo_url')}}{{$v['file_url']}}">文件地址 </a></li>
                                                        <li>条款保额{{$v['coverage']}}</li>
                                                        <li>创建时间{{$v['created_at']}}</li>
                                                    </ul>
                                                    @if(!empty($v['duties']))
                                                        <table>
                                                                    <tr>
                                                                        <th>责任名称</th>
                                                                        <th>责任描述</th>
                                                                        <th>责任细节</th>
                                                                        <th>责任类型</th>
                                                                        <th>创建时间</th>
                                                                    </tr>
                                                            @foreach($v['duties'] as $duty)
                                                                    <tr>
                                                                        <td>{{$duty['name']}}</td>
                                                                        <td>{{$duty['description']}}</td>
                                                                        <td>{{$duty['detail']}}</td>
                                                                        <td>{{$duty['type']}}</td>
                                                                        <td>{{$duty['created_at']}}</td>
                                                                    </tr>
                                                            @endforeach
                                                        </table>
                                                    @endif
                                                    @if(!empty($v['tariff']))
                                                        <div style="height: 250px;overflow-y:scroll">
                                                        <table cellspacing="2" cellpadding="20">
                                                            <tr>
                                                                <th>费率</th>
                                                                <th>年龄</th>
                                                                <th>性别</th>
                                                                <th>缴费方式</th>
                                                                <th>缴费期间</th>
                                                                <th><有无社保></有无社保></th>
                                                            </tr>
                                                            @foreach($v['tariff'] as $tariff)
                                                            <tr>
                                                                <td>{{$tariff['tariff']}}</td>
                                                                <td>{{$tariff['age']}}</td>
                                                                <td>{{$tariff['sex']}}</td>
                                                                <td>{{$tariff['period']}}</td>
                                                                <td>{{$tariff['by_stages']}}</td>
                                                                <td>{{$tariff['shebao']}}</td>
                                                            </tr>
                                                            @endforeach
                                                        </table>
                                                        </div>
                                                        @endif

                                                </div>

                                                @endforeach
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
        </div>
    </div>







@stop