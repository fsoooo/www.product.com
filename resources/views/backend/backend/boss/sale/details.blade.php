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
                            <li class=""><a href="{{url('/backend/boss/sale/index/5')}}"><span>销售统计</span></a></li>
                            <li class="active"><span>销售详情</span></li>
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
                        <h2 class="pull-left">销售详情</h2>

                    </header>
                    <div class="main-box-body clearfix">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th><span>订单号</span></th>
                                    <th><span>用户名称</span></th>
                                    <th><span>活动名称</span></th>
                                    <th><span>产品名称</span></th>
                                </tr>
                                </thead>
                                <tbody>
                                @if( $count == 0 )
                                    <tr>
                                        <td colspan="8" style="text-align: center;">暂无产品</td>
                                    </tr>
                                @else
                                    @foreach ( $product_list as $value )
                                        <tr>
                                            <td>
                                                {{ $value->order_code }}
                                            </td>
                                            <td>
                                                {{ $value->real_name}}
                                            </td>
                                            <td>
                                                @if($value->competition_id == 0)
                                                    未在活动中购买
                                                @else
                                               <?php
                                                        $name=\Illuminate\Support\Facades\DB::table('competition')->select('name')->where('id','=',$value->competition_id)->get();
                                               ?>
                                                    {{$name[0]->name}}
                                               @endif
                                            </td>
                                            <td>
                                              {{$value->product_name}}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                        {{--@if( $count != 0 )--}}
{{--                            {{ $claim->links() }}--}}
                        {{--@endif--}}
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
                        <script type="text/javascript" src="{{ URL::asset('js/jquery-3.1.1.min.js') }}"></script>
                        {{--点击查询周期的js--}}
                        <script>
                            $(document).ready(function(){
                                $('#period').change(function(){
                                    var period=$('#period').val();
                                    location.href=period;
                                })
                            })

                        </script>
@stop

