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
							<li ><span><a href="/backend/sms/emailmodels">邮件模板</a></span></li>
							<li class="active"><a href="{{ url('/backend/sms/addemailmodels') }}">添加邮件模板</a></li>
						</ol>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12">
						<div class="main-box clearfix" style="min-height: 1100px;">
							<div class="tabs-wrapper tabs-no-header">
								<ul class="nav nav-tabs">
									<li><a href="{{ url('/backend/sms/emails') }}">发送邮件</a></li>
									<li><a href="{{ url('/backend/sms/emaillists') }}">已发邮件</a></li>
									<li><a href="{{ url('/backend/sms/emailinfos') }}">代理公司邮件</a></li>
									<li class="active"><a href="{{ url('/backend/sms/emailmodels') }}">邮件模板</a></li>

								</ul>

								<div class="tab-content">
									<div class="tab-pane fade in active" id="tab-accounts">
										<h3><p>添加模板</p></h3>
										@include('backend.layout.alert_info')
										<div class="panel-group accordion" id="operation">
											<div class="panel panel-default">
												<form action="{{ url('/backend/sms/send_message') }}" method="post" id="send-message-form">
													{{ csrf_field() }}
													<table id="user" class="table table-hover" style="clear: both">
														<tbody>
														<tr>
															<td width="15%">模板名称</td>
															<td width="60%">
																<input type="text" class="form-control" placeholder="请输入标签名称"  id="name" name="name">
															</td>
														</tr>
														
														<tr>
															<td>标签内容</td>
															<td>
																<textarea class="form-control" id="description" name="description" cols="30" rows="10"></textarea>
															</td>
														</tr>
														
														</tbody>
													</table>
												</form>
											</div>
											<button id="send-message-btn" class="btn btn-success" onclick="doLabel()">确认添加</button>
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
	<script>
        function doLabel(){
            var name = $('#name').val();
            var description = $('#description').val();
            var model_id = Math.floor(Math.random () * 9000) + 1000;

            var params = {name:name,descrition:description,model_id:model_id};
            console.log(params);
            $.ajax( {
                type : "get",
                url : 'doaddmodels',
                dataType : 'json',
                data :  params,
                success:function(msg){
                    if(msg.status == 1){
                        alert(msg.message);
                        $('#check').html('<font color="red">'+msg.message+'</font>');
                    }else{
                        alert(msg.message);
                        // $('#check').html('<font color="green">'+msg.message+'</font>');
                        window.location.href=location;
                    }

                }
            });
        }
	</script>
@stop

