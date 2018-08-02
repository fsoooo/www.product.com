@extends('backend.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('r_backend/css/libs/nifty-component.css')}}"/>
@endsection
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2 class="pull-left">条款：{{$clause->name}}——费率列表</h2>
                    <div class="filter-block pull-right" style="margin-right: 20px;">
                        <a href="{{url('backend/product/clause')}}"><button class="btn btn-primary mrg-b-lg">返回条款</button></a>
                    </div>
                </header>
                @include('backend.layout.alert_info')
                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        <table class="table user-list table-hover">
                            <thead>
                            <tr>
                                @foreach($ins_options as $k => $v)
                                    @if(!in_array($v->name, ['clause_id', 'old_file_name', 'new_file_name']))
                                        <th class="text-center"><span>{{$v->comment}}</span></th>
                                    @endif
                                @endforeach
                                {{--<th>操作</th>--}}
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($tariff as $tk => $tv)
                                <tr>
                                    @foreach($ins_options as $ik => $iv)
                                        @if(!in_array($iv->name, ['clause_id', 'old_file_name', 'new_file_name']))
                                        <td class="text-center">
                                            {{$tv[$iv['name']]}}
                                        </td>
                                        @endif
                                    @endforeach

                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                    {{--分页--}}
                    {{--<div style="text-align: center;">--}}
                        {{--{{ $companies->links() }}--}}
                    {{--</div>--}}

                </div>
            </div>
        </div>

        {{--<div class="md-modal md-effect-8 md-hide" id="modal-8">--}}
            {{--<div class="md-content">--}}
                {{--<div class="modal-header">--}}
                    {{--<button class="md-close close">×</button>--}}
                    {{--<h4 class="modal-title">费率添加</h4>--}}
                {{--</div>--}}
                {{--<div class="modal-body">--}}
                    {{--<form role="form" id="add_tariff" action='{{url('backend/product/company/add')}}' method="post" enctype="multipart/form-data">--}}
                        {{--{{ csrf_field() }}--}}

                        {{--<div class="form-group">--}}
                            {{--<label for="exampleInputPassword1">.....</label>--}}
                            {{--<input class="form-control" name="logo" placeholder="费率logo" type="file">--}}
                        {{--</div>--}}

                    {{--</form>--}}
                {{--</div>--}}
                {{--<div class="modal-footer">--}}
                    {{--<button type="button" id="form-submit-tariff" class="btn btn-primary">确认提交</button>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}

        <div class="md-modal md-effect-8 md-hide" id="modal-9">
            <div class="md-content">
                <div class="modal-header">
                    <button class="md-close close">×</button>
                    <h4 class="modal-title">费率添加</h4>
                </div>
                <div class="modal-body">
                    <form role="form" id="add_excel" action='{{url('backend/product/tariff/push_excel_post')}}' method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="exampleInputPassword1">上传excel文件</label>
                            <input class="form-control" name="excel" placeholder="费率logo" type="file">
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="form-submit-excel" class="btn btn-primary">确认提交</button>
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
        $("#form-submit-excel").click(function(){
            $("#add_excel").submit();
        });
    </script>
@stop

