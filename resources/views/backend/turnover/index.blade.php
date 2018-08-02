@extends('backend.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('r_backend/css/libs/nifty-component.css')}}"/>
@endsection
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2 class="pull-left">产品交易统计</h2>

                </header>
                @include('backend.layout.alert_info')
                <div class="main-box-body clearfix">
                    <div class="table-responsive"> 
                        <table class="table user-list table-hover">
                            <thead>
                            <tr>
                                <th class="text-center">产品码</th>
                                <th class="text-center">产品数量</th>
                                <th class="text-center">总保费</th>
                                <th class="text-center">佣金</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($turnover as $result)
                                <tr>
                                    <td class="text-center">{{ $result->p_code }}</td>
                                    <td class="text-center">{{ $result->p_num }}</td>
                                    <td class="text-center">{{ $result->total_premium }}</td>
                                    <td class="text-center">{{ $result->income }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{--分页--}}
                    <div style="text-align: center;">
                        {{ $turnover->links() }}
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














