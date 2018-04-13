@extends('backend.layout.base')
@section('content')
	<div id="content-wrapper">
		<div class="row">
			<div class="col-lg-12">
				<div class="row">
					<div class="col-lg-12">
						<ol class="breadcrumb">
							<li><a href="/backend/">主页</a></li>
							<li ><span>消息管理</span></li>
							<li ><span><a href="/backend/sms/onlineservice">在线客服</a></span></li>
							<li class="active"><span><a href="/backend/sms/onlineservice">客服列表</a></span></li>
						</ol>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12">
						<div class="main-box clearfix" style="min-height: 1100px;">
							<div class="tabs-wrapper tabs-no-header">
								<ul class="nav nav-tabs">
									<li class="active"><a href="{{ url('/backend/sms/onlineservice')}}">客服列表</a></li>
									<li ><a href="{{ url('/backend/sms/addonlines') }}">添加客服</a></li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane fade in active" id="tab-accounts">
										@include('backend.layout.alert_info')
										<div class="panel-group accordion" id="operation">
											<div class="panel panel-default">
													<table id="user" class="table table-hover" style="clear: both">
														<tbody>
														<tr>
															<th width="30%">客服名称</th>
															<th width="50%">QQ号码</th>
															<th width="20%">客服状态</th>
														</tr>
														@foreach($res as $value)
														<tr  onclick="a({{$value['id']}})">
																<td>{{$value['name']}}</td>
																<td>{{$value['number']}}</td>
																@if($value['status']=='0')
																<td><span class="label label-success">在&nbsp;&nbsp;&nbsp;线</span></td>
																@else
																<td><span class="label label-danger">下&nbsp;&nbsp;&nbsp;线</span></td>
																@endif
														</tr>
														@endforeach
														</tbody>
													</table>
											</div>
										</div>
										@include('backend.layout.pages')
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>
        function  a(mid) {
            window.location = 'onlinesinfo?id='+mid;
        }
	</script>
@stop

