@extends('backend.layout.base')
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <div class="panel">
                <div class="panel-heading">
                    <p class="panel-title">编辑属性值</p>
                </div>
                <div class="panel-body">
                    @include('backend.layout.alert_info')
                    <form action="{{ route('insurance_attributes.values.update', $value->id) }}" class="form-horizontal" method="post">
                        {{ csrf_field() }}
                        {{ method_field('PUT') }}
                        <input type="hidden" value="{{ $vid }}" name="vid">
                        <div class="form-group">
                            <label for="value" class="text-right col-md-1 control-label">属性值名称</label>
                            <div class="col-md-4">
                                <input type="text" name="value" id="value" value="{{ $value->value }}" placeholder="" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="control_value" class="text-right col-md-1 control-label">控件值</label>
                            <div class="col-md-4">
                                <input type="text" name="control_value" id="control_value" value="{{ $value->control_value }}" placeholder="" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="ty_value" class="text-right col-md-1 control-label">内部控件值</label>
                            <div class="col-md-4">
                                <input type="text" name="ty_value" id="ty_value" placeholder="" class="form-control" value="{{ $value->ty_value }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="conditions" class="text-right col-md-1 control-label">约束条件</label>
                            <div class="col-md-4">
                                <select name="conditions" class="form-control">
                                    <option value="0" {{ $value->conditions == 0 ? 'selected' : '' }}>大于</option>
                                    <option value="1" {{ $value->conditions == 1 ? 'selected' : '' }}>大于等于</option>
                                    <option value="2" {{ $value->conditions == 2 ? 'selected' : '' }}>小于</option>
                                    <option value="3" {{ $value->conditions == 3 ? 'selected' : '' }}>小于等于</option>
                                    <option value="4" {{ $value->conditions == 4 ? 'selected' : '' }}>不等于</option>
                                    <option value="5" {{ $value->conditions == 5 ? 'selected' : '' }}>等于</option>
                                    <option value="6" {{ $value->conditions == 6 ? 'selected' : '' }}>包含</option>
                                    <option value="7" {{ $value->conditions == 7 ? 'selected' : '' }}>不包含</option>
                                    <option value="8" {{ $value->conditions == 8 ? 'selected' : '' }}>提示</option>
                                    <option value="9" {{ $value->conditions == 9 ? 'selected' : '' }}>隐藏</option>
                                    <option value="10" {{ $value->conditions == 10 ? 'selected' : '' }}>正则</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="regex" class="text-right col-md-1 control-label">属性值校验正则表达式</label>
                            <div class="col-md-4">
                                <input type="text" name="regex" id="regex" placeholder="" value="{{ $value->regex }}" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="remind" class="text-right col-md-1 control-label">正则约束条件验证失败提示</label>
                            <div class="col-md-4">
                                <input type="text" name="remind" id="remind" placeholder="" value="{{ $value->remind }}" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="attribute_type" class="text-right col-md-1 control-label">所限制属性的控件类型</label>
                            <div class="col-md-4">
                                <input type="text" name="attribute_type" id="attribute_type" value="{{ $value->attribute_type }}" placeholder="" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="unit" class="text-right col-md-1 control-label">单位</label>
                            <div class="col-md-4">
                                <select name="unit" class="form-control">
                                    <option value="0" {{ $value->unit == 0 ? 'selected' : '' }}>无</option>
                                    <option value="1" {{ $value->unit == 0 ? 'selected' : '' }}>份</option>
                                    <option value="2" {{ $value->unit == 0 ? 'selected' : '' }}>万元</option>
                                    <option value="3" {{ $value->unit == 0 ? 'selected' : '' }}>元</option>
                                    <option value="4" {{ $value->unit == 0 ? 'selected' : '' }}>0元</option>
                                    <option value="5" {{ $value->unit == 0 ? 'selected' : '' }}>00元</option>
                                    <option value="6" {{ $value->unit == 0 ? 'selected' : '' }}>000元</option>
                                    <option value="7" {{ $value->unit == 0 ? 'selected' : '' }}>岁</option>
                                    <option value="8" {{ $value->unit == 0 ? 'selected' : '' }}>年</option>
                                    <option value="9" {{ $value->unit == 0 ? 'selected' : '' }}>月</option>
                                    <option value="10" {{ $value->unit == 0 ? 'selected' : '' }}>天</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-offset-1 col-md-2">
                                <button type="submit" class="btn btn-primary">
                                    确认
                                </button>
                                <a href="{{ route('insurance_attributes.values.index', $value->aid) }}" class="btn btn-primary">返回</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
