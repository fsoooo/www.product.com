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
                            <li ><span>运营管理</span></li>
                            <li class="active"><span>需求管理</span></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    <li><a href="{{ url('/backend/demand/index/user') }}">客户发布</a></li>
                                    <li><a href="{{ url('/backend/demand/index/agent') }}">代理人发布</a></li>
                                    <li class="active"><a href="#">需求详情</a></li>
                                </ul>
                                <div class="tab-content">
                                    @include('backend.layout.alert_info')
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        <h3><p>账户信息</p></h3>
                                        <div class="panel-group accordion" id="account">
                                            <div class="panel panel-default">
                                                <table id="user" class="table table-hover" style="clear: both">
                                                    <tbody>
                                                    <tr>
                                                        <td>发布人姓名</td>
                                                        <td>{{ $demand_detail->demand_user->real_name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>类型</td>
                                                        <td>
                                                            @if($demand_detail->create_type == 1)
                                                                代理人发布
                                                            @else
                                                                客户发布
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>发布人身份证号</td>
                                                        <td>{{ $demand_detail->code }}</td>
                                                    </tr>
                                                    @foreach($demand_options as $key=>$value)
                                                        <tr>
                                                            <td>{{config('tariff_parameter.'.$key)}}</td>
                                                            <td>{{ $value }}</td>
                                                        </tr>
                                                    @endforeach
                                                    <tr>
                                                        <td width="15%">需求描述</td>
                                                        <td width="65%">
                                                            {{ $demand_detail->demand_describe }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>进展</td>
                                                        <td>
                                                            @if($demand_detail->is_deal == 1)
                                                                已报价
                                                            @else
                                                                未处理
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        {{--@if($demand_detail->is_deal != 1)--}}
                                                            {{--<td><button id="btn">提交</button></td>--}}
                                                        {{--@endif--}}
                                                        <td>
                                                            <a href="/backend/demand/check_offer/{{ $demand_detail->id }}"><button class="btn btn-success" >查看当前组合报价</button></a></td>
                                                        <td><a href="/backend/demand/offer/{{ $demand_detail->id }}"><butto class="btn btn-success" >产品组合报价</butto></a></td>
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


    </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    <script src="/js/jquery-3.1.1.min.js"></script>
    <script>
        var btn = $('#btn');
        var form = $('#form');
        var offer = $('#offer');
        btn.click(function(){
            var offer_val = offer.val();
            if(!offer_val){
                alert('请输入报价');
            }else {
                form.submit();
            }
        })
    </script>
@stop

