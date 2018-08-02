@extends('backend.layout.base')
@section('content')
	<meta name="_token" content="{{ csrf_token() }}"/>
	<style>

	</style>
	<div id="content-wrapper">
		<div class="big-img" style="display: none;">
			<img src="" alt="" id="big-img" style="width: 75%;height: 90%;">
		</div>
		<form action="{{ url('/backend/product/doupdatelabel') }}" method="post" id="send-message-form" enctype="multipart/form-data">
		<div class="row">
			<form class="col-lg-12">
				<div class="row">
					<div class="col-lg-12">
						<ol class="breadcrumb">
							<li><a href="{{ url('/backend') }}">主页</a></li>
							<li ><span>产品管理</span></li>
							<li ><span>产品标签</span></li>
							<li ><a href="{{ url('/backend/product/productlabels')}}">标签列表</a></li>
							<li class="active"><a href="#">修改标签</a></li>
						</ol>
					</div>
				</div>
				@foreach($data as $value)
					<div class="row">
						<div class="col-lg-12">
							<div class="main-box clearfix" style="min-height: 1100px;">
								<div class="tabs-wrapper tabs-no-header">
									<ul class="nav nav-tabs">
										<li class="active"><a href="#">修改标签</a></li>
									</ul>

									<div class="tab-content">
										<div class="tab-pane fade in active" id="tab-accounts">
											{{--<h3><p>添加标签</p></h3>--}}
											@include('backend.layout.alert_info')
											<div class="panel-group accordion" id="operation">
												<div class="panel panel-default">
														{{ csrf_field() }}
													<input type="hidden" name="id" value="{{$value['id']}}">
														<table id="user" class="table table-hover" style="clear: both">
															<tbody>
															<tr>
																<td width="15%">标签名称</td>
																<td width="60%">
																	<input type="text" class="form-control" placeholder="请输入标签名称"  id="name" name="name" value="{{$value['name']}}">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="15%">标签排序(不选择，随机生成排序)</td>
																<td width="60%">
																	<input type="text" class="form-control" id="orderby" name="order_by"  value="{{$value['order_by']}}">
																</td>
															</tr>
															@if(!empty($value['cover']))
																<tr>
																	<td width="15%">标签封面</td>
																	<td width="60%" >
																		<img src="/{{$value['cover']}}" style="height: 100px;width: 150px">
																		<input type="hidden" name="old_file" value="{{$value['cover']}}">
																	</td>
																</tr>
																<tr>
																	<td>图片预览</td>
																	<td>
																		<div class="ge_pic_icon_Infor">
																			<img src="/view_1/image/show_logo.jpg" style="height: 100px;width: 150px"/>
																		</div>
																	</td>
																</tr>
																<tr>
																	<td width="15%">修改封面</td>
																	<td width="60%" >
																		<input type="button" class="form-control" value="修改封面" onclick="dofile()">
																		<input type="file" class="form-control" id="file" name="file" style="display: none" onchange="getPhoto(this)">
																	</td>
																</tr>
															@else
																<tr>
																	<td width="15%">标签封面</td>
																	<td width="60%" >
																		暂无封面
																	</td>
																</tr>
																<tr>
																	<td>图片预览</td>
																	<td>
																		<div class="ge_pic_icon_Infor">
																			<img src="/view_1/image/show_logo.jpg" style="width: 150px;height: 100px"/>
																		</div>
																	</td>
																</tr>
																<tr>
																	<td width="15%">上传封面</td>
																	<td width="60%" >
																		<input type="button" class="form-control" value="上传封面" onclick="dofile()">
																		<input type="file" class="form-control" id="file" name="file" style="display: none" onchange="getPhoto(this)">
																	</td>
																</tr>
															@endif

															<tr>
																<td>标签描述</td>
																<td>
																	<textarea class="form-control" id="description" name="description" cols="30" rows="10">{{$value['description']}}</textarea>
																</td>
															</tr>
															{{--<tr>--}}
																{{--<td>选择所属标签组(不选择，默认添加标签组)</td>--}}
																{{--<td>--}}
																	{{--<select name="parent_id" id="parent_id"  class="form-control">--}}
																		{{--@foreach($res as $values)--}}
																			{{--@if($value['parent_id']==$values['id'])--}}
																				{{--<option value="{{$values['id']}}" selected>{{$values['name']}}</option>--}}
																			{{--@else--}}
																				{{--<option value="{{$values['id']}}">{{$values['name']}}</option>--}}
																			{{--@endif--}}
																		{{--@endforeach--}}
																	{{--</select>--}}
																{{--</td>--}}
															{{--</tr>--}}
															</tbody>
															@endforeach
														</table>
													</form>
												</div>
												<button id="send-message-btn" class="btn btn-success" onclick="doLabel()">确认修改</button>
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
        var imgurl = "";
        function getPhoto(node) {
            var imgURL = "";
            try{
                var file = null;
                if(node.files && node.files[0] ){
                    file = node.files[0];
                }else if(node.files && node.files.item(0)) {
                    file = node.files.item(0);
                }
                //Firefox 因安全性问题已无法直接通过input[file].value 获取完整的文件路径
                try{
                    imgURL =  file.getAsDataURL();
                }catch(e){
                    imgRUL = window.URL.createObjectURL(file);
                }
            }catch(e){
                if (node.files && node.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        imgURL = e.target.result;
                    };
                    reader.readAsDataURL(node.files[0]);
                }
            }
            creatImg(imgRUL);
            return imgURL;
        }

        function creatImg(imgRUL){
            var textHtml = "<img src='"+imgRUL+"'width='150px' height='100px'/>";
            $(".ge_pic_icon_Infor").html(textHtml);
        }
        function dofile(){
            return  $("#file").click();
        }
	</script>
@stop

