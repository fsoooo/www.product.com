@extends('backend.layout.base')
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <div class="panel">
                <div class="panel-heading">
                    <p class="panel-title">属性值录入</p>
                </div>
                <div class="panel-body">
                @include('backend.layout.alert_info')
                    <form action="{{ route('insurance_attributes.values.store') }}" class="form-horizontal" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" value="{{ $aid }}" name="aid">
                        <div class="form-group">
                            <label for="value" class="text-right col-md-1 control-label">属性值名称</label>
                            <div class="col-md-4">
                                @if(!isset($ins_key_value))
                                    <input type="text" name="value" id="value" placeholder="" class="form-control">
                                @elseif(!empty($ins_key_value))
                                    <select name="value" id="value" class="form-control">
                                        @foreach($ins_key_value as $key=>$value)
                                            <option value="{{$key}}" data-value="{{$value}}">{{$key}}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="text" name="value" id="value" placeholder="" class="form-control">
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="control_value" class="text-right col-md-1 control-label">控件值</label>
                            <div class="col-md-4">
                                <input type="text" name="control_value" id="control_value" placeholder="" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="ty_value" class="text-right col-md-1 control-label">内部控件值</label>
                            <div class="col-md-4">
                                @if(!isset($ins_key_value))
                                    <input type="text" name="ty_value" id="ty_value"  class="form-control" >
                                @elseif(!empty($ins_key_value))
                                    <input type="text" name="ty_value" id="ty_value_on"  class="form-control" readonly >
                                @else
                                    <input type="text" name="ty_value" id="ty_value"  class="form-control" >
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="conditions" class="text-right col-md-1 control-label">约束条件</label>
                            <div class="col-md-4">
                                <select name="conditions" class="form-control">
                                    <option value="5">等于</option>
                                    <option value="0">大于</option>
                                    <option value="1">大于等于</option>
                                    <option value="2">小于</option>
                                    <option value="3">小于等于</option>
                                    <option value="4">不等于</option>
                                    <option value="6">包含</option>
                                    <option value="7">不包含</option>
                                    <option value="8">提示</option>
                                    <option value="9">隐藏</option>
                                    <option value="10">正则</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="regex" class="text-right col-md-1 control-label">属性值校验正则表达式</label>
                            <div class="col-md-4">
                                <input type="text" name="regex" id="regex" placeholder="" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="remind" class="text-right col-md-1 control-label">正则约束条件验证失败提示</label>
                            <div class="col-md-4">
                                <input type="text" name="remind" id="remind" placeholder="" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="attribute_type" class="text-right col-md-1 control-label">所限制属性的控件类型</label>
                            <div class="col-md-4">
                                <input type="text" name="attribute_type" id="attribute_type" placeholder="" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="unit" class="text-right col-md-1 control-label">单位</label>
                            <div class="col-md-4">
                                <select name="unit" class="form-control">
                                    <option value="0">无</option>
                                    <option value="1">份</option>
                                    <option value="2">万元</option>
                                    <option value="3">元</option>
                                    <option value="4">0元</option>
                                    <option value="5">00元</option>
                                    <option value="6">000元</option>
                                    <option value="7">岁</option>
                                    <option value="8">年</option>
                                    <option value="9">月</option>
                                    <option value="10">天</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-offset-1 col-md-2">
                                <button type="submit" class="btn btn-primary">
                                    确认
                                </button>
                                <a href="{{ route('insurance_attributes.values.index', $aid) }}" class="btn btn-primary">返回</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        var value =  $('#value').find("option:selected").attr('data-value');
        if(value!=''||value!=null){
            $('#ty_value_on').val(value);
        }
        $('#value').on('change',function () {
            var name = $('#value').val();
            var value = $(this).find("option:selected").attr('data-value');
            if(value!=''||value!=null){
                $('#ty_value_on').val(value);
            }
        });
    </script>
@endsection