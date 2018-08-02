@extends('backend.layout.base')
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <div class="panel">
                <div class="panel-heading">
                    <p class="panel-title">
                        试算因子选项
                        <a href="{{ route('restrict_genes.values.create', $restrict_gene->id) }}" class="btn btn-primary btn-sm">添加试算因子选项</a>
                        <a href="{{ route('restrict_genes.index', $restrict_gene->bind_id) }}" class="btn btn-primary btn-sm">返回试算因子列表</a>
                    </p>
                </div>
                <div class="panel-body">
                    @include('backend.layout.alert_info')
                    @if(empty($values))
                        <h3>没有录入的试算因子选项</h3>
                    @else
                        @include('backend.insurance.relation')
                        <table class="table table-condensed">
                            <tr>
                                <th>选项名称</th>
                                <th>选项值</th>
                                <th>内部统一值</th>
                                <th>类型</th>
                                <th>最小值</th>
                                <th>最大值</th>
                                <th>步长</th>
                                <th>单位</th>
                                <th>操作</th>
                            </tr>
                            @foreach($values as $value)
                                <tr>
                                    <td>{{ $value->name }}</td>
                                    <td>{{ $value->value }}</td>
                                    <td>{{ $value->ty_value }}</td>
                                    <td>
                                        {{ $value->type == 1 ? '普通选项 ' : '' }}
                                        {{ $value->type == 2 ? '最小值到最大值步长值' : '' }}
                                    </td>
                                    <td>{{ $value->min }}</td>
                                    <td>{{ $value->max }}</td>
                                    <td>{{ $value->step }}</td>
                                    <td>{{ $value->unit }}</td>
                                    <td>
                                        <a href="{{ route('restrict_genes.values.edit', $value->id) }}" class="btn btn-primary btn-sm">
                                            编辑
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection