@extends('backend.layout.base')
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <div class="panel">
                <div class="panel-heading">
                    <p class="panel-title">
                        属性值列表
                        <a href="{{ route('insurance_attributes.values.create', $aid) }}" class="btn btn-primary btn-sm">添加属性值</a>
                        <a href="{{ route('insurance_attributes.attributes.index', $attribute->module->id) }}" class="btn btn-primary btn-sm">返回属性列表</a>
                        <a href="{{ route('insurance_attributes.modules.index', $attribute->module->bind_id) }}" class="btn btn-primary btn-sm">返回模块列表</a>
                        <a href="{{ route('insurance.bind.list') }}" class="btn btn-primary btn-sm">返回绑定列表</a>
                    </p>
                </div>
                <div class="panel-body">
                    @include('backend.layout.alert_info')
                    @include('backend.insurance.relation')
                    <table class="table">
                        <tr>
                            <th>属性值名称</th>
                            <th>控件值</th>
                            <th>内部控件值</th>
                            <th>约束条件</th>
                            <th>属性值校验正则表达式</th>
                            <th>正则约束条件验证失败提示</th>
                            <th>所限制属性的控件类型</th>
                            <th>单位</th>
                            <th>操作</th>
                        </tr>
                        @foreach($values as $value)
                            <tr>
                                <td>{{ $value->value }}</td>
                                <td>{{ $value->control_value }}</td>
                                <td>{{ $value->ty_value }}</td>
                                <td>
                                    {{ $value->conditions == 0 ? '大于' : '' }}
                                    {{ $value->conditions == 1 ? '大于等于' : '' }}
                                    {{ $value->conditions == 2 ? '小于' : '' }}
                                    {{ $value->conditions == 3 ? '小于等于' : '' }}
                                    {{ $value->conditions == 4 ? '小于等于' : '' }}
                                    {{ $value->conditions == 5 ? '等于' : '' }}
                                    {{ $value->conditions == 6 ? '包含' : '' }}
                                    {{ $value->conditions == 7 ? '不包含' : '' }}
                                    {{ $value->conditions == 8 ? '提示' : '' }}
                                    {{ $value->conditions == 9 ? '隐藏' : '' }}
                                    {{ $value->conditions == 10 ? '正则' : '' }}
                                </td>
                                <td>{{ $value->regex }}</td>
                                <td>{{ $value->remind }}</td>
                                <td>{{ $value->attribute_type }}</td>
                                <td>
                                    {{ $value->unit == 0 ? '无' : '' }}
                                    {{ $value->unit == 1 ? '份' : '' }}
                                    {{ $value->unit == 2 ? '万元' : '' }}
                                    {{ $value->unit == 3 ? '元' : '' }}
                                    {{ $value->unit == 4 ? '0元' : '' }}
                                    {{ $value->unit == 5 ? '00元' : '' }}
                                    {{ $value->unit == 6 ? '000元' : '' }}
                                    {{ $value->unit == 7 ? '岁' : '' }}
                                    {{ $value->unit == 8 ? '年' : '' }}
                                    {{ $value->unit == 9 ? '月' : '' }}
                                    {{ $value->unit == 10 ? '天' : '' }}
                                </td>
                                <td>
                                    <a href="{{ route('insurance_attributes.values.edit', $value->id) }}" class="btn btn-primary btn-sm">
                                        编辑
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