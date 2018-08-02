@extends('backend.layout.base')
@section('content')
	<div id="content-wrapper">
		<div class="big-img" style="display: none;">
			<img src="" alt="" id="big-img" style="width: 75%;height: 90%;">
		</div>
		<div class="row">
			<div class="col-lg-12">
				<div class="row">
					<div class="col-lg-12">
						<ol class="breadcrumb">
							<li><a href="{{ url('/backend') }}">主页</a></li>
							<li><span>消息管理</span></li>
							<li><span><a href="{{ url('/backend/sms/email')}}">邮件管理</a></span></li>
							<li class="active"><a href="{{ url('/backend/sms/emailmodels')}}">邮件模板</a></li>
						</ol>
					</div>
				</div><br/>
				<div class="row">
					<div class="col-lg-12">
						<div class="main-box clearfix" style="min-height: 1100px;">
							<div class="tabs-wrapper tabs-no-header">
								<ul class="nav nav-tabs">
									<li><a href="{{ url('/backend/sms/email')}}">发送邮件</a></li>
									<li><a href="{{ url('/backend/sms/hassendemail') }}">已发邮件</a></li>
									<li class="active"><a href="{{ url('/backend/sms/emailmodels') }}">邮件模板</a></li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane fade in active" id="tab-accounts">
										<h3><p><button id="send-message-btn" class="btn btn-success" onclick="add()">添加模板</button><br/></p></h3>
										@include('backend.layout.alert_info')
										<div class="panel-group accordion" id="operation">
											<div class="panel panel-default">
													<table id="user" class="table table-hover" style="clear: both">
														<tbody>
														<tr>
															<th width="10%">模板编码</th>
															<th width="80%">模板名称</th>
															<th width="10%">创建时间</th>
														</tr>
														@foreach($res as $value)
														<tr onclick="a({{$value['id']}})">
															<td>{{$value['model_id']}}</td>
															<td>{{$value['model_name']}}</td>
															<td>{{$value['created_at']}}</td>
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
			<script>
				function  add() {
				    window.location = 'addemailmodels';
                }
                function  a(mid) {
                    window.location = 'emailmodelsinfo?id='+mid;
                }
			</script>
		</div>
	</div>
@stop

