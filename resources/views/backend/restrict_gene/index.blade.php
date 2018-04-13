@extends('backend.layout.base')
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <div class="panel">
                <div class="panel-heading">
                    <p class="panel-title">
                        试算因子
                        <a href="{{ route('restrict_genes.create', $bind_id) }}" class="btn btn-primary btn-sm">添加试算因子</a>
                        <a href="{{ route('insurance.bind.list') }}" class="btn btn-primary btn-sm">返回绑定列表</a>
                    </p>
                </div>
                <div class="panel-body">
                    @include('backend.layout.alert_info')
                    @include('backend.insurance.relation')
                    <table class="table table-condensed">
                        <tr>
                            <th>试算因子对应属性Key</th>
                            <th>内部统一Key</th>
                            <th>试算因子名</th>
                            <th>内部默认值</th>
                            <th>类型</th>
                            <th>是否显示</th>
                            <th>展示顺序</th>
                            <th>操作</th>
                        </tr>
                        @foreach($restrict_genes as $restrict_gene)
                            <tr>
                                <td>{{ $restrict_gene->key }}</td>
                                <td>{{ $restrict_gene->ty_key }}</td>
                                <td>{{ $restrict_gene->name }}</td>
                                <td>{{ $restrict_gene->default_value }}</td>
                                <td>
                                    {{ $restrict_gene->type == 0 ? '下拉框' : '' }}
                                    {{ $restrict_gene->type == 1 ? '日历' : '' }}
                                    {{ $restrict_gene->type == 2 ? '日历+下拉框' : '' }}
                                    {{ $restrict_gene->type == 3 ? '文本输入框' : '' }}
                                    {{ $restrict_gene->type == 4 ? '地区' : '' }}
                                    {{ $restrict_gene->type == 5 ? '职业' : '' }}
                                    {{ $restrict_gene->type == 6 ? '文本' : '' }}
                                </td>
                                <td>{{ $restrict_gene->display }}</td>
                                <td>{{ $restrict_gene->sort }}</td>
                                <td>
                                    <a href="{{ route('restrict_genes.edit', $restrict_gene->id) }}" class="btn btn-primary btn-sm">
                                        编辑
                                    </a>
                                    <a href="{{ route('restrict_genes.values.index', $restrict_gene->id) }}" class="btn btn-primary btn-sm">
                                        试算因子选项列表
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