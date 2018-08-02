@extends('backend.layout.base')
@section('content')
	<meta name="_token" content="{{ csrf_token() }}"/>
	<style>

	</style>
	<div id="content-wrapper">
		<div class="big-img" style="display: none;">
			<img src="" alt="" id="big-img" style="width: 75%;height: 90%;">
		</div>
		<div class="row">
			<div class="col-lg-12">
				<div class="row">
					<div class="col-lg-12">
						<ol class="breadcrumb">
							<li><a href="/backend/">主页</a></li>
							<li ><span>消息管理</span></li>
							<li><span><a href="/backend/sms/emails">邮件管理</a></span></li>
							<li class="active"><a href="{{ url('/backend/sms/emailinfos') }}">代理公司邮件</a></li>
						</ol>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-12">
						<div class="main-box clearfix" style="min-height: 1100px;">
							<div class="tabs-wrapper tabs-no-header">
								<ul class="nav nav-tabs">
									<li><a href="{{ url('/backend/sms/emails') }}">发送邮件</a></li>
									<li ><a href="{{ url('/backend/sms/emaillists') }}">已发邮件</a></li>
									<li class="active"><a href="{{ url('/backend/sms/emailinfos') }}">代理公司邮件</a></li>
									<li><a href="{{ url('/backend/sms/emailmodels') }}">邮件模板</a></li>

								</ul>


								<div class="tab-content">
									<div class="tab-pane fade in active" id="tab-accounts">
										{{--<h3><p><a href="addemailmodels" class="off">已发邮件列表</a></p></h3>--}}
										@include('backend.layout.alert_info')
										<div class="panel-group accordion" id="operation">
											<div class="panel panel-default">
												<form >
													{{ csrf_field() }}
													<table id="user" class="table table-hover" style="clear: both">

														<tbody>
														<tr>
															<th width="5%"></th>
															<th width="20%">代理公司名称</th>
															<th width="10%">时间</th>
															<th width="20%"></th>
														</tr>
														@foreach($res as $keys => $value)
															<tr>
																<td></td>
																			<td><a href="emaillistinfo?data={{$keys}}">{{$keys}}</a></td>
																<td>2017-07-12 12:30:26</td>
																<td></td>
															</tr>
														@endforeach
														{{--<script>--}}
                                                            {{--function  a(mid) {--}}
                                                                {{--var mid = mid;--}}
                                                                {{--alert(mid);--}}
{{--//                                                                window.location = 'emaillistinfo?id='+mid;--}}
                                                            {{--}--}}
														{{--</script>--}}
														</tbody>

													</table>
												</form>
											</div>

										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<footer id="footer-bar" class="row">
					<p id="footer-copyright" class="col-xs-12">
						&copy; 2014 <a href="http://www.adbee.sk/" target="_blank">Adbee digital</a>. Powered by Centaurus Theme.
					</p>
				</footer>
			</div>
		</div>
	</div>

@stop

