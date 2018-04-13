{{-- 佣金统计列表 --}}
@extends('backend.layout.base')
@section('content')
    <style>
        th{
            text-align: center;
        }
        td{
            text-align: center;
        }
    </style>
    <div id="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <ol class="breadcrumb">
                            <li><a href="{{ url('/backend') }}">主页</a></li>
                            <li class="active"><span>佣金统计</span></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <div class="col-lg-12">
                                    @include('backend.layout.alert_info')
                                    <div class="main-box clearfix">

                                        <header class="main-box-header clearfix">
                                            <h2 class="pull-left">佣金统计</h2>
                                        </header>
                                        <div class="main-box-body clearfix">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <tr style="height: 100px;">
                                                        <td></td>
                                                        <td>总-所获佣金值：{{$sum['us'] / 100}}</td>
                                                        <td>总-给予代理佣金值：{{$sum['agency'] /100}}</td>
                                                        <td>收益：{{ ($sum['us'] - $sum['agency']) / 100 }}</td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                        <tr>
                                                            <th></th>
                                                            <th><span>API</span></th>
                                                            <th><span>产品名称</span></th>
                                                            <th><span>内部所获佣金值</span></th>
                                                            <th><span>给予代理佣金值</span></th>
                                                            <th><span>创建时间</span></th>
                                                            <th><span>内部结算状态</span></th>
                                                            <th><span>渠道结算状态</span></th>
                                                        </tr>
                                                    <tbody>
                                                        @foreach($lists as $k => $list)
                                                            <tr>
                                                                <td>{{$k+1}}</td>
                                                                <td>{{ $list->api_from_name }}</td>
                                                                <td>{{ $list->insurance_name }}</td>
                                                                <td>{{ $list->brokerage_for_us / 100}}</td>
                                                                <td>{{ $list->brokerage_for_agency / 100 }}</td>
                                                                <td>{{ $list->c_at }}</td>
                                                                <td>
                                                                    {{ $status['us_settlement_status'][$list->us_settlement_status] }}
                                                                </td>
                                                                <td>
                                                                    {{ $status['agency_settlement_status'][$list->agency_settlement_status] }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div style="text-align: center;">
                                                {{ $lists->links() }}
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
@endsection