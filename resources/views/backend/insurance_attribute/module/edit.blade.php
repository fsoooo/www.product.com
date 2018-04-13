@extends('backend.layout.base')
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <div class="panel">
                <div class="panel-heading">
                    <p class="panel-title">编辑模块</p>
                </div>
                <div class="panel-body">
                @include('backend.layout.alert_info')
                    <form action="{{ route('insurance_attributes.modules.update', $module->id) }}" class="form-horizontal" method="post">
                        {{ csrf_field() }}
                        {{ method_field('PUT') }}
                        <div class="form-group">
                            <label for="name" class="text-right col-md-1 control-label">模块名称</label>
                            <div class="col-md-4">
                                <input type="text" name="name" id="name" placeholder="" class="form-control" value="{{ $module->name }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="module_key" class="text-right col-md-1 control-label">模块Key</label>
                            <div class="col-md-4">
                                <input type="text" name="module_key" id="module_key" placeholder="" class="form-control" value="{{ $module->module_key }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="remark" class="text-right col-md-1 control-label">模块说明</label>
                            <div class="col-md-4">
                                <input type="text" name="remark" id="remark" placeholder="" class="form-control" value="{{ $module->remark }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-offset-1 col-md-2">
                                <button type="submit" class="btn btn-primary">
                                    确认
                                </button>
                                <a href="{{ route('insurance_attributes.modules.index', $module->bind_id) }}" class="btn btn-primary">返回</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection