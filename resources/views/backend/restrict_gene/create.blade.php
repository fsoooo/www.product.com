@extends('backend.layout.base')
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <div class="panel">
                <div class="panel-heading">
                    <p class="panel-title">添加试算因子</p>
                </div>
                <div class="panel-body">
                    @include('backend.layout.alert_info')
                    <form action="{{ route('restrict_genes.store') }}" method="post" class="form-horizontal">
                        {{ csrf_field() }}
                        <input type="hidden" name="bind_id" value="{{ $bind_id }}">
                        <div class="form-group">
                            <label for="name" class="text-right col-md-1 control-label">试算因子名</label>
                            <div class="col-md-4">
                                @if(!isset($quote_base))
                                    <input type="text" name="name" id="name" placeholder="" class="form-control">
                                @elseif(!empty($quote_base))
                                    <select name="name" id="name" class="form-control">
                                        @foreach($quote_base as $key=>$value)
                                            <option value="{{$key}}" data-value="{{$value}}">{{$key}}</option>
                                        @endforeach
                                            <option value="自定义">自定义</option>
                                    </select>
                                @else
                                    <input type="text" name="name" id="name" placeholder="" class="form-control">
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="ty_key" class="text-right col-md-1 control-label">内部统一key</label>
                            <div class="col-md-4">
                                @if(!isset($quote_base))
                                    <input type="text" name="ty_key" id="ty_key"  class="form-control" >
                                @elseif(!empty($quote_base))
                                    <input type="text" name="ty_key" id="ty_key_on"  class="form-control" readonly >
                                @else
                                    <input type="text" name="ty_key" id="ty_key"  class="form-control" >
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="key" class="text-right col-md-1 control-label">试算因子对应属性Key</label>
                            <div class="col-md-4">
                                <input type="text" name="key" id="key" placeholder="" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="default_value" class="text-right col-md-1 control-label">内部默认值</label>
                            <div class="col-md-4">
                                <input type="text" name="default_value" id="default_value" placeholder="需要带上单位" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="type" class="text-right col-md-1 control-label">类型</label>
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
                            <label for="type" class="text-right col-md-1 control-label">关联条款</label>
                            <div class="col-md-4">
                                <select name="clause_id" id="type" class="form-control">
                                    <option value="0" selected>无</option>
                                    @foreach($clauses as $k => $v)
                                        <option value="{{$v->id}}">{{$v->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="display" class="text-right col-md-1 control-label">是否显示</label>
                            <div class="col-md-4">
                                <select name="display" id="display" class="form-control">
                                    <option value="1" selected>是</option>
                                    <option value="0">否</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="sort" class="text-right col-md-1 control-label">展示顺序</label>
                            <div class="col-md-4">
                                <input type="text" name="sort" id="sort" placeholder="" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-offset-1 col-md-2">
                                <button class="btn btn-primary">确定</button>
                                <a href="{{ route('restrict_genes.index', $bind_id) }}" class="btn btn-primary  ">返回</a>
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
            if(name == '自定义'){
                $(this).after('<input type="text" name="name" placeholder="输入自定义因子名" class="form-control custom_name">');
                $('#ty_key_on').val('');
                $('#ty_key_on').attr("readonly", false);
            } else {
                var value = $(this).find("option:selected").attr('data-value');
                if(value!=''||value!=null){
                    $('#ty_key_on').val(value);
                }
                $(".custom_name").remove();
            }
        });
    </script>
@endsection