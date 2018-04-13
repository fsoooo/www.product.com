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
							<li class="active"><a href="{{ url('/backend/sms/hassendemail')}}">已发邮件</a></li>
						</ol>
					</div>
				</div><br/>
				<div class="row">
					<div class="col-lg-12">
						<div class="main-box clearfix" style="min-height: 1100px;">
							<div class="tabs-wrapper tabs-no-header">
								<ul class="nav nav-tabs">
									<li><a href="{{ url('/backend/sms/email')}}">发送邮件</a></li>
									<li class="active"><a href="{{ url('/backend/sms/hassendemail') }}">已发邮件</a></li>
									<li><a href="{{ url('/backend/sms/emailmodels') }}">邮件模板</a></li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane fade in active" id="tab-accounts">
										<h3><p>已发邮件列表</p></h3>
										@include('backend.layout.alert_info')
										<div class="panel-group accordion" id="operation">
											<div class="panel panel-default">
													<table id="user" class="table table-hover" style="clear: both">
														<tbody>
														<tr>
															<th width="20%">收件人地址</th>
															<th width="60%">邮件标题</th>
															<th width="10%">创建时间</th>
														</tr>
														@foreach($res as $value)
														<tr onclick="a({{$value['id']}})">
															<td>{{$value['receive']}}</td>
															<td>{{$value['title']}}</td>
															<td>{{$value['created_at']}}</td>
														</tr>
														<script>
                                                            function  a(mid) {
                                                                window.location = 'hasemailinfo?id='+mid;
                                                            }
														</script>
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
@stop

