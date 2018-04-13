@extends('backend.layout.base')
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <div class="panel">
                <div class="panel-heading">
                    <p class="panel-title">
                        属性列表
                        <a href="{{ route('insurance_attributes.attributes.create', $module->id) }}" class="btn btn-primary btn-sm">添加属性</a>
                        <a href="{{ route('insurance_attributes.modules.index', $module->bind_id) }}" class="btn btn-primary btn-sm">返回模块列表</a>
                        <a href="{{ route('insurance.bind.list') }}" class="btn btn-primary btn-sm">返回绑定列表</a>
                    </p>
                </div>
                <div class="panel-body">
                    @include('backend.layout.alert_info')
                    @include('backend.insurance.relation')
                    <table class="table table-condensed">
                        <tr>
                            <th>属性名称</th>
                            <th>api接口请求参数名</th>
                            <th>内部Key</th>
                            <th>属性类型</th>
                            <th>属性校验正则表达式</th>
                            <th>默认提醒信息</th>
                            <th>出错提醒信息</th>
                            <th>排序</th>
                            <th>是否必填 </th>
                            <th>操作</th>
                        </tr>
                        @foreach($attributes as $attribute)
                            <tr>
                                <td>{{ $attribute->name }}</td>
                                <td>{{ $attribute->api_name }}</td>
                                <td>{{ $attribute->ty_key }}</td>
                                <td>
                                    {{ $attribute->type == 0 ? '下拉框' : '' }}
                                    {{ $attribute->type == 1 ? '日历' : '' }}
                                    {{ $attribute->type == 2 ? '日历+下拉框' : '' }}
                                    {{ $attribute->type == 3 ? '文本输入框' : '' }}
                                    {{ $attribute->type == 4 ? '地区' : '' }}
                                    {{ $attribute->type == 5 ? '职业' : '' }}
                                    {{ $attribute->type == 6 ? '文本' : '' }}
                                </td>
                                <td>{{ $attribute->regex }}</td>
                                <td>{{ $attribute->default_remind }}</td>
                                <td>{{ $attribute->error_remind }}</td>
                                <td>{{ $attribute->sort }}</td>
                                <td>{{ $attribute->required == 1 ? '是' : '否' }}</td>
                                <td>
                                    <a href="{{ route('insurance_attributes.attributes.edit', $attribute->id) }}" class="btn btn-primary btn-sm">
                                        编辑
                                    </a><a href="{{ route('insurance_attributes.values.index', $attribute->id) }}" class="btn btn-primary btn-sm">
                                        属性值列表
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