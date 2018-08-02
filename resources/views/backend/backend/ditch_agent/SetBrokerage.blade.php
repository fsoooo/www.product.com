@extends('backend.layout.base')
@section('css')
<link rel="stylesheet" type="text/css" href="{{asset('r_backend/css/libs/nifty-component.css')}}"/>
@endsection
@section('content')
    <style>
        th,tr,td{
            text-align: center;
        }
        input{
            text-align: center;
        }
    </style>
<div id="content-wrapper">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-12">
                <ol class="breadcrumb">
                    <li><a href="{{ url('/backend') }}">主页</a></li>
                    <li ><span>销售管理</span></li>
                    <li ><span>代理人渠道管理</span></li>
                    <li ><span><a href="/backend/sell/ditch_agent/brokerage">佣金设置</a></span></li>
                    <li class="active"><span><a>佣金设置详情</a></span></li>
                </ol>
            </div>
        </div>
        <div class="main-box clearfix">
            <header class="main-box-header clearfix">
                <h2 class="pull-left">佣金设置</h2>
                <div class="filter-block pull-right" style="margin-right: 20px;">
                </div>
            </header>
            {{--产品搜索--}}
            {{--产品搜索--}}
            <div class="main-box-body clearfix">
                <div class="table-responsive">
                    <form action="{{ url('/backend/sell/ditch_agent/search_product') }}" method="post">
                        {{ csrf_field() }}
                        <table class="table user-list table-hover">
                            <tr>
                                <label for="">产品id</label>
                            </tr>
                           <tr>
                               <input type="text" value="{{ $product_id }}" placeholder="" name="product_id">
                           </tr>
                            <tr>
                                <button id="product-btn">搜索</button>
                            </tr>

                        </table>
                    </form>
                </div>
                <div class="tab-content">
                    @include('backend.layout.alert_info')
                    <div class="tab-pane fade in active" id="tab-accounts">
                        <h3><p>产品详情</p></h3>
                        <div class="panel-group accordion" id="account">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        {{--<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion2" href="#account-message">--}}
                                            产品基本信息
                                        {{--</a>--}}
                                    </h4>
                                </div>
                                <div id="account-message" class="panel-collapse ">
                                    <form action="{{ url('/backend/sell/ditch_agent/set_brokerage') }}" method="post">
                                        <table class="table user-list table-hover">
                                            {{ csrf_field() }}
                                            <input type="text" name="type" value="product" hidden>
                                            <input type="text" name="product_id" value="{{ $product_id }}" hidden>
                                            <tr>
                                                <td>产品名称</td>
                                                <td>{{ $product_detail->product_name }}</td>
                                            </tr>
                                            <tr>
                                                <td>产品唯一码</td>
                                                <td>{{ $product_detail->product_number }}</td>
                                            </tr>
                                            <tr>
                                                <td>产品统一佣金</td>
                                                <td>
                                                    @if(!$product_detail)
                                                        <input type="text" name="earnings" value="">
                                                    @else
                                                        <input type="text" name="earnings" value="{{ $product_detail->brokerage }}">
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>产品统一折标系数</td>
                                                <td>
                                                    @if(!$product_detail)
                                                        <input type="text" name="scaling" value="">
                                                    @else
                                                        <input type="text" name="scaling" value="{{ $product_detail->scaling }}">
                                                    @endif
                                                </td>

                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <button>设置</button>
                                                </td>
                                            </tr>
                                        </table>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="tab-content">
                    <div class="tab-pane fade in active" id="tab-accounts">
                        <h3><p>渠道</p></h3>
                        @foreach($ditch_agent as $value)
                            <div class="panel-group accordion" id="account">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion2" href="#ditch{{ $value->id }}">
                                                {{ $value->name }}
                                            </a>
                                        </h4>
                                    </div>
                                    <form action="{{ url('/backend/sell/ditch_agent/set_brokerage') }}" method="post">
                                        <table class="table user-list table-hover">
                                            {{ csrf_field() }}
                                            <input type="text" name="type" value="ditch" hidden>
                                            <input type="text" name="ditch_id" value="{{ $value->id }}" hidden>
                                            <input type="text" name="product_id" value="{{ $product_id }}" hidden>
                                            <tr>
                                                <td>渠道统一佣金</td>
                                                <td>
                                                    @if(!$value->brokerage)
                                                        <input type="text" name="earnings" vlaue="">
                                                    @else
                                                        <input type="text" name="earnings" value="{{ $value->brokerage }}">
                                                    @endif
                                                </td>
                                                <td>渠道统一折标系数</td>
                                                <td>
                                                    @if(!$value->scaling)
                                                        <input type="text" name="scaling" value="">
                                                    @else
                                                        <input type="text" name="scaling" value="{{ $value->scaling }}">
                                                    @endif
                                                </td>
                                                <td>
                                                    <button>设置</button>
                                                </td>
                                            </tr>
                                        </table>
                                    </form>
                                    <div id="ditch{{ $value->id }}" class="panel-collapse collapse">
                                        <table class="table user-list table-hover">
                                            <tr>
                                                <th>代理人姓名</th>
                                                <th>设置代理人佣金</th>
                                                <th>设置代理人折标系数</th>
                                                <th>操作</th>
                                            </tr>
                                            @foreach($value->agents as $agent_value)
                                                <form action="{{ url('/backend/sell/ditch_agent/set_brokerage') }}" method="post">
                                                    {{ csrf_field() }}
                                                    <input type="text" name="type" value="agent" hidden>
                                                    <input type="text" name="agent_id" value="{{ $agent_value->id }}" hidden>
                                                    <input type="text" name="ditch_id" value="{{ $value->id }}" hidden>
                                                    <input type="text" name="product_id" value="{{ $product_id }}" hidden>
                                                    <tr>
                                                        <td>{{ $agent_value->user->name }}</td>
                                                        <td>
                                                            @if(!$agent_value->brokerage)
                                                                <input type="text" name="earnings" value="">
                                                            @else
                                                                <input type="text" name="earnings" value="{{ $agent_value->brokerage }}">
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if(!$agent_value->scaling)
                                                                <input type="text" name="scaling" value="">
                                                            @else
                                                                <input type="text" name="scaling" value="{{ $agent_value->scaling }}">
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <button>设置</button>
                                                        </td>
                                                    </tr>
                                                </form>
                                            @endforeach
                                        </table>
                                    </div>
                                    <div class="link" style="text-align: center;">
                                        {{ $ditch_agent->links() }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="tab-content">
                    <div class="tab-pane fade in active" id="tab-accounts">
                        <h3><p>未绑定渠道的代理人</p></h3>
                            <div class="panel-group accordion" id="account">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion2" href="#no_ditch{{ $product_detail->id }}">
                                                未绑定渠道的代理人
                                            </a>
                                        </h4>
                                    </div>
                                    <form action="{{ url('/backend/sell/ditch_agent/set_brokerage') }}" method="post">
                                        <table class="table user-list table-hover">
                                            {{ csrf_field() }}
                                            <input type="text" name="type" value="other" hidden>
                                            <input type="text" name="agent_id" value="0" hidden>
                                            <input type="text" name="product_id" value="{{ $product_id }}" hidden>
                                            <tr>
                                                <td width="28%">统一佣金</td>
                                                <td>
                                                    @if(!$no_ditch_agent['brokerage'])
                                                        <input type="text" name="earnings" vlaue="">
                                                    @else
                                                        <input type="text" name="earnings" value="{{ $no_ditch_agent['brokerage'] }}">
                                                    @endif
                                                </td>
                                                <td>统一折标系数</td>
                                                <td>
                                                    @if(!$no_ditch_agent['scaling'])
                                                        <input type="text" name="scaling" value="">
                                                    @else
                                                        <input type="text" name="scaling" value="{{ $no_ditch_agent['scaling'] }}">
                                                    @endif
                                                </td>
                                                <td>
                                                    <button>设置</button>
                                                </td>
                                            </tr>
                                        </table>
                                    </form>
                                    <div id="no_ditch{{ $product_detail->id }}" class="panel-collapse collapse">
                                        <table class="table user-list table-hover">
                                            <tr>
                                                <th>代理人姓名</th>
                                                <th>设置代理人佣金</th>
                                                <th>设置代理人折标系数</th>
                                                <th>操作</th>
                                            </tr>
                                            @foreach($no_ditch_agent['agent'] as $no_ditch_value)
                                                <form action="{{ url('/backend/sell/ditch_agent/set_brokerage') }}" method="post">
                                                    {{ csrf_field() }}
                                                    <input type="text" name="type" value="other" hidden>
                                                    <input type="text" name="agent_id" value="{{ $no_ditch_value->id }}" hidden>
                                                    <input type="text" name="product_id" value="{{ $product_id }}" hidden>
                                                    <tr>
                                                        <td>{{ $no_ditch_value->user->name }}</td>
                                                        <td>
                                                            @if(!$no_ditch_value->brokerage)
                                                                <input type="text" name="earnings" value="">
                                                            @else
                                                                <input type="text" name="earnings" value="{{ $no_ditch_value->brokerage }}">
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if(!$no_ditch_value->scaling)
                                                                <input type="text" name="scaling" value="">
                                                            @else
                                                                <input type="text" name="scaling" value="{{ $no_ditch_value->scaling }}">
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <button>设置</button>
                                                        </td>
                                                    </tr>
                                                </form>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="md-overlay"></div>
</div>
@stop
@section('foot-js')
<script charset="utf-8" src="/r_backend/js/modernizr.custom.js"></script>
<script charset="utf-8" src="/r_backend/js/classie.js"></script>
<script charset="utf-8" src="/r_backend/js/modalEffects.js"></script>
<script>

</script>
@stop

