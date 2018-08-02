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
							<li ><span>产品管理</span></li>
							<li><span><a href="/backend/product/productlist">产品列表</a></span></li>
							<li class="active"><span>贴标签</span></li>
						</ol>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12">
						<div class="main-box clearfix" style="min-height: 1100px;">
							<div class="tabs-wrapper tabs-no-header">
								<div class="tab-content">
									<div class="tab-pane fade in active" id="tab-accounts">
										<h3><p>贴标签</p></h3>
										<div>已选标签：<span id="showlabel" style="font-family: 微软雅黑;font-size: 15px">0</span></div>
										@include('backend.layout.alert_info')
										<div class="panel-group accordion" id="operation">
											<div class="panel panel-default">
													<table id="user" class="table table-hover" style="clear: both">
														<tbody>
														<tr>
															<td width="15%">产品名称</td>
															<td width="60%">
																<input type="text" class="form-control" id="name" name="name" value="{{$res['product_name']}}">
															</td>
														</tr>
														<tr>
															<td>选择标签</td>
															<td id="data">
																<form id="form">
																@foreach($labels as $value)
																<input type="checkbox" name="label_id[]"  value="{{$value['id']}}" onclick="showRadio()">{{$value['name']}}
																@endforeach
																</form>
															</td>
														</tr>
														<tr>
															<td></td>
															{{--<td>@include('backend.layout.pages')</td>--}}
														</tr>
														<tr>
															<td>查看标签</td>
															<td>
																<select name="label_id" id="label_id"  class="form-control">
																	<option value="0" selected="selcted" onclick="doClean()">请选择标签</option>
																	@foreach($labels as $value)
																		<option value="{{$value['id']}}" onclick="a({{$value['id']}})">{{$value['name']}}</option>
																	@endforeach
																</select>
															</td>
														</tr>
														<tr>
															<td>标签描述</td>
															<td>
																<textarea class="form-control" id="description" name="description" cols="30" rows="10"></textarea>
															</td>
														</tr>
														</tbody>
													</table>
											</div>
											<button id="send-message-btn" class="btn btn-success" onclick="doLabel()">确认添加</button>
										</div>
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
        function doClean(){
            $("#content").text(' ');
        }
        function a(id){
            var id = id;
            $.ajax({
                type: "get",
                dataType: "json",
                async: true,
                url: "getlabelinfo?id="+id,
                success: function(data){
                    var status = data['status'];
                    var model = data['label'];
                    if(status ==   0){
                        $("#description").text(model);

                    }else {
                        alert('error');
                    }
                }
            });

        }

        function doLabel(){
            var product_id = "{{$res['product_id']}}";
            var choose = document.getElementsByName("label_id[]");
            var num = choose.length;
            var id = "";
            for(var index =0 ; index<num ; index++){
                if(choose[index].checked){
                    id += choose[index].value + ",";
                }
            }
            var params = {id:id,product_id:product_id};
                    $.ajax( {
                        type : "get",
                        url : 'doaddproductlabel',
                        dataType : 'json',
                        data :  params,
                        success:function(msg){
                            if(msg.status == 1){
                                alert(msg.message);
                            }else{
                                alert(msg.message);
                                window.location = location;

                            }

                        }
                    });
                }
        function showRadio(){
            var result = {};
            var fieldArray = $('#form').serializeArray();
            for (var i = 0; i < fieldArray.length; i++) {
                var field = fieldArray[i];
                if (field.name in result) {
                    result[field.name] += ',' + field.value;
                } else {
                    result[field.name] = field.value;
                }
            }
            $.ajax({
                type: "post",
                dataType: "json",
                async: true,
                url: "showproductlables",
                data:result,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (msg) {
                    if (msg.status == '0') {
                        var labels = msg.labels;
                        $('#showlabel').html('<font color="black">' + labels + '</font>');
                    }else if(msg.status == '1') {
                        alert('error');
                    }else if(msg.status == '2'){
                        $('#showlabel').html('<font color="black">' + '0' + '</font>');
                    }
                }
            });
//                                                                            $('#check').html('<font color="black">'+tariff+'</font>');
        }
	</script>
@stop

