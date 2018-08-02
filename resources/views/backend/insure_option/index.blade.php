@extends('backend.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('r_backend/css/libs/nifty-component.css')}}"/>
@endsection
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="/backend/">主页</a></li>
                <li ><span>产品管理</span></li>
                <li class="active"><span>投保参数</span></li>
            </ol>
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2 class="pull-left">投保参数列表</h2>
                    <div class="filter-block pull-right" style="margin-right: 20px;">
                        <button class="md-trigger btn btn-primary mrg-b-lg" data-modal="modal-8">新建投保参数</button>
                    </div>
                </header>
                @include('backend.layout.alert_info')
                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        <table class="table user-list table-hover">
                            <thead>
                            <tr>
                                <th class="text-center"></th>
                                <th class="text-center">唯一标识</th>
                                <th class="text-center">备注</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($options as $option)
                                <tr>
                                    <td class="text-center">{{ $option->id }}</td>
                                    <td class="text-center">{{ $option->name }}</td>
                                    <td class="text-center">{{ $option->comment }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{--分页--}}
                    {{ $options->links() }}
                </div>
            </div>
        </div>

        <div class="md-modal md-effect-8 md-hide" id="modal-8">
            <div class="md-content">
                <div class="modal-header">
                    <button class="md-close close">×</button>
                    <h4 class="modal-title">参数添加</h4>
                </div>
                <div class="modal-body">
                    <form role="form" id="add_option" action='{{url('backend/product/insure_option/addPost')}}' method="post">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="exampleInputPassword1">唯一英文标识</label>
                            <input class="form-control" name="name" placeholder="唯一英文标识" type="text">
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1">备注信息</label>
                            <input class="form-control" name="comment" type="text">
                        </div>

                        <div class="form-group">
                            <label for="exampleTextarea">是否允许为空</label>
                            <select name="nullable" class="form-control">
                                <option value="yes">可空</option>
                                <option value="no">必填</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="form-submit" class="btn btn-primary">确认提交</button>
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
        $(function(){
            $("#form-submit").click(function(){
                $("#add_option").submit();
            })
        })
    </script>
@stop

