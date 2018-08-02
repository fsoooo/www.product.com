@extends('backend.layout.base')
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <div class="panel">
                <div class="panel-heading">
                    <p class="panel-title">添加试算因子选项</p>
                </div>
                <div class="panel-body">
                    @include('backend.layout.alert_info')
                    <form action="{{ route('restrict_genes.values.store') }}" method="post" class="form-horizontal">
                        {{ csrf_field() }}
                        <input type="hidden" name="rid" value="{{ $rid }}">
                        <div class="form-group">
                            <label for="name" class="text-right col-md-1 control-label">选项值名称</label>
                            <div class="col-md-4">
                                <input type="text" name="name" id="name" placeholder="" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="value" class="text-right col-md-1 control-label">选项值</label>
                            <div class="col-md-4">
                                <input type="text" name="value" id="value" placeholder="" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="ty_value" class="text-right col-md-1 control-label">内部统一值</label>
                            <div class="col-md-4">
                                <input type="text" name="ty_value" id="ty_value" placeholder="" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="type" class="text-right col-md-1 control-label">类型</label>
                            <div class="col-md-4">
                                <select name="type" id="type" class="form-control">
                                    <option value="1">普通选项</option>
                                    <option value="2">最小值到最大值步长值</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="min" class="text-right col-md-1 control-label">最小值</label>
                            <div class="col-md-4">
                                <input type="text" name="min" id="min" placeholder="" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="max" class="text-right col-md-1 control-label">最大值</label>
                            <div class="col-md-4">
                                <input type="text" name="max" id="max" placeholder="" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="step" class="text-right col-md-1 control-label">步长</label>
                            <div class="col-md-4">
                                <input type="text" name="step" id="step" placeholder="" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="unit" class="text-right col-md-1 control-label">单位</label>
                            <div class="col-md-4">
                                <input type="text" name="unit" id="unit" placeholder="" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-offset-1 col-md-2">
                                <button class="btn btn-primary">确定</button>
                                <a href="{{ route('restrict_genes.values.index', $rid) }}" class="btn btn-primary  ">返回</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection