@extends('backend.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('r_backend/css/libs/nifty-component.css')}}"/>
@endsection
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2 class="pull-left">详情页</h2>

                </header>
                @include('backend.layout.alert_info')
                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        <table class="table user-list table-hover">
                            <thead>
                            <tr>

                                <th class="text-center">订单号</th>
                                <th class="text-center">产品码</th>
                                <th class="text-center">总保费</th>
                                <th class="text-center">产品数量</th>
                                <th class="text-center">被保人数量</th>

                            </tr>
                            </thead>
                            <tbody>
                            @foreach($res as $val)
                                <tr>
                                    <td class="text-center">{{ $val->order_no }}</td>
                                    <td class="text-center">{{ $val->p_code }}</td>
                                    <td class="text-center">{{ $val->total_premium }}</td>
                                    <td class="text-center">{{ $val->p_num }}</td>
                                    <td class="text-center">{{ $val->insured_num }}</td>

                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{--分页--}}
                    <div style="text-align: center;">
                        {{ $res->appends(['create_account_id'=>$id])->links() }}
                    </div>

                </div>
            </div>
        </div>


    </div>
@stop
@section('foot-js')
    <script charset="utf-8" src="/r_backend/js/modernizr.custom.js"></script>
    <script charset="utf-8" src="/r_backend/js/classie.js"></script>
    <script charset="utf-8" src="/r_backend/js/modalEffects.js"></script>
    <script>
        $(function(){
            $("#form-submit").click(function(){
                $("#add_option").submit();
            })
        })
    </script>
@stop













