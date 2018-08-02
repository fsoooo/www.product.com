@extends('backend.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('r_backend/css/libs/nifty-component.css')}}"/>
@endsection
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2 class="pull-left">订单</h2>

                </header>
                @include('backend.layout.alert_info')
                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        <table class="table user-list table-hover">
                            <thead>
                            <tr>
                                <th class="text-center">代理商账户名称</th>
                                <th class="text-center">账户ID</th>
                                <th class="text-center">订单数</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td class="text-center">{{ $user->name }}</td>
                                    <td class="text-center">{{ $user->account_id }}</td>
                                    <td class="text-center">{{ $user->orders_count }}</td>
                                    <td>
                                        <a type="button" href="{{ url('backend/order/list', ['account_id' => $user->account_id]) }}" class="btn btn-primary">详细</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{--分页--}}
                    {{ $users->links() }}
                </div>
            </div>
        </div>

        {{--<div class="md-modal md-effect-8 md-hide" id="modal-8">--}}
            {{--<div class="md-content">--}}
                {{--<div class="modal-header">--}}
                    {{--<button class="md-close close">×</button>--}}
                    {{--<h4 class="modal-title">参数添加</h4>--}}
                {{--</div>--}}
                {{--<div class="modal-body">--}}
                    {{--<form role="form" id="add_option" action='{{url('backend/product/insure_option/addPost')}}' method="post">--}}
                        {{--{{ csrf_field() }}--}}
                        {{--<div class="form-group">--}}
                            {{--<label for="exampleInputPassword1">唯一英文标识</label>--}}
                            {{--<input class="form-control" name="name" placeholder="唯一英文标识" type="text">--}}
                        {{--</div>--}}

                        {{--<div class="form-group">--}}
                            {{--<label for="exampleInputEmail1">备注信息</label>--}}
                            {{--<input class="form-control" name="comment" type="text">--}}
                        {{--</div>--}}

                        {{--<div class="form-group">--}}
                            {{--<label for="exampleTextarea">是否允许为空</label>--}}
                            {{--<select name="nullable" class="form-control">--}}
                                {{--<option value="yes">可空</option>--}}
                                {{--<option value="no">必填</option>--}}
                            {{--</select>--}}
                        {{--</div>--}}
                    {{--</form>--}}
                {{--</div>--}}
                {{--<div class="modal-footer">--}}
                    {{--<button type="button" id="form-submit" class="btn btn-primary">确认提交</button>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
        <div class="md-overlay"></div>


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

