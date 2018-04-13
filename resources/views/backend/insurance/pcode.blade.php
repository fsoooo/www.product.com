@extends('backend.layout.base')
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <div class="panel">
                <div class="panel-heading">
                    <p class="panel-title">录入产品码</p>
                </div>
                <div class="panel-body">
                    @include('backend.layout.alert_info')
                    <form action="{{ route('insurance.bind.pcode.store') }}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="bind_id" value="{{ $relation->bind_id }}">
                        @include('backend.insurance.relation')
                        <div class="form-group">
                            <label for="p_code">绑定状态</label>
                            <p class="form-control">{{ $relation->status == 1 ? '绑定' : '解绑' }}</p>
                        </div>
                        <div class="form-group">
                            <label for="p_code">产品码</label>
                            <input type="text" name="p_code" id="p_code" class="form-control" value="{{ $relation->p_code }}">
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary">确定</button>
                            <a href="{{ route('insurance.bind.list') }}" class="btn btn-primary">返回</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection