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
							<li ><span><a href="/backend/product/productlabels">产品标签</a></span></li>
							<li class="active"><span>标签列表</span></li>
						</ol>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12">
						<div class="main-box clearfix" style="min-height: 1100px;">
							<div class="tabs-wrapper tabs-no-header">
								<ul class="nav nav-tabs">
									<li class="active"><a href="{{ url('/backend/product/productlabels')}}">标签列表</a></li>
									<li><a href="{{ url('/backend/product/addlabel')}}">添加标签</a></li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane fade in active" id="tab-accounts">
										@include('backend.layout.alert_info')
										<div class="panel-group accordion" id="operation">
											<div class="panel panel-default">
													{{ csrf_field() }}
													<table id="user" class="table table-hover" style="clear: both">
														<tbody>
														<tr>
															<th width="10%">标签编号</th>
															<th width="40%">标签名称</th>
															<th width="20%" style="text-align:center;">标签排序</th>
															<th width="10%">操作</th>
														</tr>
														@foreach($res as $value)
															<tr>
																<td>{{$value['id']}}</a></td>
																<td>{{$value['name']}}</a></td>
																<td style="text-align:center;"><input type="text" name="listorderArr[]" value="{{$value['order_by']}}" style="width:50%;text-align:center;"/></td>
																<td><a href="updatelabel?id={{$value['id']}}">修改标签</a></td>
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

@stop

