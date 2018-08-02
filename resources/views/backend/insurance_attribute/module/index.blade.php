@extends('backend.layout.base')
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <div class="panel">
                <div class="panel-heading">
                    <p class="panel-title">
                        模块列表
                        <a href="{{ route('insurance_attributes.modules.create', $bind_id) }}" class="btn btn-primary btn-sm">添加模块</a>
                        <a href="{{ route('insurance.bind.list') }}" class="btn btn-primary btn-sm">返回绑定列表</a>
                    </p>
                </div>
                <div class="panel-body">
                    @include('backend.layout.alert_info')
                    @include('backend.insurance.relation')
                    <table class="table">
                        <tr>
                            <th>模块名称</th>
                            <th>模块Key</th>
                            <th>模块描述</th>
                            <th>操作</th>
                        </tr>
                        @foreach($modules as $module)
                            <tr>
                                <td>{{ $module->name }}</td>
                                <td>{{ $module->module_key }}</td>
                                <td>{{ $module->remark }}</td>
                                <td>
                                    <a href="{{ route('insurance_attributes.modules.edit', $module->id) }}" class="btn btn-primary btn-sm">
                                        编辑
                                    </a>
                                    <a href="{{ route('insurance_attributes.attributes.index', $module->id) }}" class="btn btn-primary btn-sm">
                                        查看模块属性
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection