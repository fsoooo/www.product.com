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
                <li class="active"><span>接口来源</span></li>
            </ol>
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2 class="pull-left">投保参数列表</h2>
                    <div class="filter-block pull-right" style="margin-right: 20px;">
                        <button class="md-trigger btn btn-primary mrg-b-lg" data-modal="modal-8">新建接口来源</button>
                    </div>
                </header>
                @include('backend.layout.alert_info')
                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        <table class="table user-list table-hover">
                            <thead>
                            <tr>
                                <th class="text-center col-md-1">接口来源</th>
                                <th class="text-center col-md-1">唯一标识(作为内部产品唯一码前缀)</th>
                                {{--<th class="text-center col-md-2">算费API</th>--}}
                                {{--<th class="text-center col-md-2">投保API</th>--}}
                                {{--<th class="text-center col-md-2">核保查询API</th>--}}
                                {{--<th class="text-center col-md-2">支付API</th>--}}
                                {{--<th class="text-center col-md-2">出单API</th>--}}
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($api_from as $k => $v)
                                <tr>
                                    <td class="text-center col-md-1">{{$v->name}}</td>
                                    <td class="text-center col-md-1">{{$v->uuid}}</td>
                                    {{--<td class="text-center col-md-2">{{$v->count_api}}</td>--}}
                                    {{--<td class="text-center col-md-2">{{$v->toubao_api}}</td>--}}
                                    {{--<td class="text-center col-md-2">{{$v->hebao_api}}</td>--}}
                                    {{--<td class="text-center col-md-2">{{$v->pay_api}}</td>--}}
                                    {{--<td class="text-center col-md-2">{{$v->issue_api}}</td>--}}
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{--分页--}}
                    <div style="text-align: center;">
                        {{ $api_from->links() }}
                    </div>

                </div>
            </div>
        </div>


		{{--更改--}}
		@foreach($api_from as $k => $v)
		<div class="md-modal md-effect-8 md-hide" id="modal-10{{$v->id}}">
			<div class="md-content">
				<div class="modal-header">
					<button class="md-close close">×</button>
					<h4 class="modal-title">数据更改</h4>
				</div>
				<div class="modal-body">
					<form role="form" id="edit_option" action='{{url('backend/product/api_from/edit/'.$v->id)}}' method="post">
						{{ csrf_field() }}
						<div class="form-group">
							<label for="exampleInputPassword1">来源名称</label>
							<input class="form-control" name="name" placeholder="来源名称" type="text" value="{{$v->name}}">
						</div>
						<div class="form-group">
							<label for="exampleInputPassword1">唯一英文标识 (作为内部产品唯一码前缀)</label>
							<input class="form-control" name="uuid" placeholder="唯一英文标识" type="text" value="{{$v->uuid}}">
						</div>
						<div class="form-group">
							<label for="exampleInputPassword1">算费API</label>
							<input class="form-control" name="count_api" placeholder="算费API" type="text" value="{{$v->count_api}}">
						</div>
						<div class="form-group">
							<label for="exampleInputPassword1">核保API</label>
							<input class="form-control" name="hebao_api" placeholder="核保API" type="text" value="{{$v->hebao_api}}">
						</div>
						<div class="form-group">
							<label for="exampleInputPassword1">投保API</label>
							<input class="form-control" name="toubao_api" placeholder="投保API" type="text" value="{{$v->toubao_api}}">
						</div>
						<div class="form-group">
							<label for="exampleInputPassword1">支付API</label>
							<input class="form-control" name="pay_api" placeholder="支付API" type="text" value="{{$v->pay_api}}">
						</div>
						<div class="form-group">
							<label for="exampleInputPassword1">出单API</label>
							<input class="form-control" name="issue_api" placeholder="出单API" type="text" value="{{$v->issue_api}}">
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="submit"  class="btn btn-primary form-submit">确认提交</button>
				</div>
			</div>
		</div>
		@endforeach
		{{--添加--}}
        <div class="md-modal md-effect-8 md-hide" id="modal-8">
            <div class="md-content">
                <div class="modal-header">
                    <button class="md-close close">×</button>
                    <h4 class="modal-title">来源添加</h4>
                </div>
                <div class="modal-body">
                    <form role="form" id="add_option" action='{{url('backend/product/api_from/add_post')}}' method="post">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="exampleInputPassword1">来源名称</label>
                            <input class="form-control" name="name" placeholder="来源名称" type="text">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">唯一英文标识 (作为内部产品唯一码前缀)</label>
                            <input class="form-control" name="uuid" placeholder="唯一英文标识" type="text">
                        </div>
                        {{--<div class="form-group">--}}
                            {{--<label for="exampleInputPassword1">算费API</label>--}}
                            {{--<input class="form-control" name="count_api" placeholder="核保API" type="text">--}}
                        {{--</div>--}}
                        {{--<div class="form-group">--}}
                            {{--<label for="exampleInputPassword1">核保API</label>--}}
                            {{--<input class="form-control" name="hebao_api" placeholder="核保API" type="text">--}}
                        {{--</div>--}}
                        {{--<div class="form-group">--}}
                            {{--<label for="exampleInputPassword1">投保API</label>--}}
                            {{--<input class="form-control" name="toubao_api" placeholder="投保API" type="text">--}}
                        {{--</div>--}}
                        {{--<div class="form-group">--}}
                            {{--<label for="exampleInputPassword1">支付API</label>--}}
                            {{--<input class="form-control" name="pay_api" placeholder="支付API" type="text">--}}
                        {{--</div>--}}
                        {{--<div class="form-group">--}}
                            {{--<label for="exampleInputPassword1">出单API</label>--}}
                            {{--<input class="form-control" name="issue_api" placeholder="查询API" type="text">--}}
                        {{--</div>--}}
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

			$(".form-submit").click(function(){
				$("#edit_option").submit();
			})
        })
    </script>
@stop

