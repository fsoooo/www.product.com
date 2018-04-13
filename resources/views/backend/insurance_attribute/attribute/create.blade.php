@extends('backend.layout.base')
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <div class="panel">
                <div class="panel-heading">
                    <p class="panel-title">模块属性录入</p>
                </div>
                <div class="panel-body">
                @include('backend.layout.alert_info')
                    <form action="{{ route('insurance_attributes.attributes.store') }}" class="form-horizontal" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" value="{{ $mid }}" name="mid">
                        <div class="form-group">
                            <label for="name" class="text-right col-md-1 control-label">属性名称</label>
                            <div class="col-md-4">
                                @if(!isset($module_res))
                                    <input type="text" name="name" id="name" placeholder="" class="form-control">
                                @elseif(!empty($module_res))
                                    <select name="name" id="name" class="form-control">
                                        @foreach($module_res as $key=>$value)
                                            <option value="{{$key}}" data-value="{{$value}}">{{$key}}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="text" name="name" id="name" placeholder="" class="form-control">
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="remark" class="text-right col-md-1 control-label">api接口请求参数名</label>
                            <div class="col-md-4">
                                <input type="text" name="api_name" id="api_name" placeholder="" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="ty_key" class="text-right col-md-1 control-label">内部Key</label>
                            <div class="col-md-4">
                                @if(!isset($module_res))
                                    <input type="text" name="ty_key" id="ty_key"  class="form-control" >
                                @elseif(!empty($module_res))
                                    <input type="text" name="ty_key" id="ty_key_on"  class="form-control" readonly >
                                @else
                                    <input type="text" name="ty_key" id="ty_key"  class="form-control" >
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="type" class="text-right col-md-1 control-label">属性类型</label>
                            <div class="col-md-4">
                                <select name="type" id="type" class="form-control">
                                    <option value="0">下拉框</option>
                                    <option value="1">日历</option>
                                    <option value="2">日历+下拉框</option>
                                    <option value="3">文本输入框 </option>
                                    <option value="4">地区</option>
                                    <option value="5">职业</option>
                                    <option value="6">文本</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="regex" class="text-right col-md-1 control-label">属性校验正则表达式</label>
                            <div class="col-md-4">
                                <input type="text" name="regex" id="regex" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="default_remind" class="text-right col-md-1 control-label">默认提醒信息</label>
                            <div class="col-md-4">
                                <input type="text" name="default_remind" id="default_remind" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="error_remind" class="text-right col-md-1 control-label">出错提醒信息</label>
                            <div class="col-md-4">
                                <input type="text" name="error_remind" id="error_remind" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="required" class="text-right col-md-1 control-label">是否必填</label>
                            <div class="col-md-4">
                                <select name="required" id="required" class="form-control">
                                    <option value="1">是</option>
                                    <option value="0">否</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="required" class="text-right col-md-1 control-label">排序</label>
                            <div class="col-md-4">
                                <input type="text" name="sort" id="sort" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-offset-1 col-md-2">
                                <button type="submit" class="btn btn-primary">
                                    确认
                                </button>
                                <a href="{{ route('insurance_attributes.attributes.index', $mid) }}" class="btn btn-primary">返回</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        var value =  $('#name').find("option:selected").attr('data-value');
        if(value!=''||value!=null){
            $('#ty_key_on').val(value);
        }
       $('#name').on('change',function () {
           var name = $('#name').val();
           var value = $(this).find("option:selected").attr('data-value');
           if(value!=''||value!=null){
               $('#ty_key_on').val(value);
           }
       });
    </script>
@endsection