@extends('backend.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('r_backend/css/libs/nifty-component.css')}}"/>
@endsection
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2 class="pull-left">代理商</h2>
                    <div class="filter-block pull-right" style="margin-right: 20px;">
                        <!-- <button class="md-trigger btn btn-primary mrg-b-lg" data-modal="modal-8"></button> -->
                    </div>
                </header>
                @include('backend.layout.alert_info')
                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        <table class="table user-list table-hover">
                            <thead>
                            <tr>
                                
                                <th class="text-center">代理公司</th>
                                <th class="text-center">成功交易量</th>
                                <th class="text-center">佣金</th>
                                <th>详情</th>
                            </tr>
                            </thead>
                            <tbody>
                                 @foreach($result as $val)
                                <tr>
                                    <td class="text-center">{{ $val->name }}</td>
                                    <td class="text-center">{{ $val->p_num }}</td>
                                    <td class="text-center">{{ $val->income/100 }}</td>
                                    <td style="width: 15%;">
                                       <a href="details?id={{ $val->account_id }}">查看详情</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{--分页--}}
                    <div style="text-align: center;">
                        {{ $result->links() }}
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













