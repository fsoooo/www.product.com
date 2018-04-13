@extends('backend.layout.base')
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <div class="panel">
                <div class="panel-heading">
                    <p class="panel-title">编辑试算因子</p>
                </div>
                <div class="panel-body">
                    @include('backend.layout.alert_info')
                    <form action="{{ route('restrict_genes.update', $restrict_gene->id) }}" method="post" class="form-horizontal">
                        {{ csrf_field() }}
                        {{ method_field('PUT') }}
                        <div class="form-group">
                            <label for="name" class="text-right col-md-1 control-label">试算因子名</label>
                            <div class="col-md-4">
                                <?php $key_array= []; ?>
                                @if(!isset($quote_base))
                                    <input type="text" name="name" id="name" placeholder="" class="form-control" value="{{$restrict_gene->name}}">
                                @elseif(!empty($quote_base))
                                    <select name="name" id="name" class="form-control">
                                        @foreach($quote_base as $key=>$value)
                                            <?php $key_array[]= $key; ?>
                                            @if($key==$restrict_gene->name)
                                                <option value="{{$key}}" data-value="{{$value}}">{{$restrict_gene->name}}</option>
                                            @endif
                                        @endforeach
                                        @foreach($quote_base as $key=>$value)
                                            @if($key!=$restrict_gene->name)
                                                <option value="{{$key}}" data-value="{{$value}}">{{$key}}</option>
                                            @endif
                                        @endforeach
                                        @if(!in_array($restrict_gene->name, $key_array))
                                            <option value="自定义" selected>自定义</option>
                                                <input type="text" name="name" class="form-control custom_name" value="{{$restrict_gene->name}}">
                                            @else
                                                <option value="自定义">自定义</option>
                                        @endif
                                    </select>
                                @else
                                    <input type="text" name="name" id="name" placeholder="" class="form-control custom_name" value="{{$restrict_gene->name}}">
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="ty_key" class="text-right col-md-1 control-label">内部统一key</label>
                            <div class="col-md-4">
                                @if(!isset($quote_base))
                                    <input type="text" name="ty_key" id="ty_key"  class="form-control" value="{{ $restrict_gene->ty_key }}" >
                                @elseif(!in_array($restrict_gene->name, $key_array))
                                    <input type="text" name="ty_key" id="ty_key"  class="form-control" value="{{$restrict_gene->ty_key}}" >
                                @else
                                    <input type="text" name="ty_key" id="ty_key_on"  class="form-control" value="{{ $restrict_gene->ty_key }}" readonly >
                                {{--@else--}}
                                    {{--<input type="text" name="ty_key" id="ty_key"  class="form-control" value="{{ $restrict_gene->ty_key }}">--}}
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="key" class="text-right col-md-1 control-label">试算因子对应属性Key</label>
                            <div class="col-md-4">
                                <input type="text" name="key" id="key" placeholder="" class="form-control" value="{{ $restrict_gene->key }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="default_value" class="text-right col-md-1 control-label">内部默认值</label>
                            <div class="col-md-4">
                                <input type="text" name="default_value" id="default_value" placeholder="需要带上单位" class="form-control" value="{{ $restrict_gene->default_value }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="type" class="text-right col-md-1 control-label">类型</label>
                            <div class="col-md-4">
                                <select name="type" id="type" class="form-control">
                                    <option value="0" {{ $restrict_gene->type == 0 ? 'selected' : '' }}>下拉框</option>
                                    <option value="1" {{ $restrict_gene->type == 1 ? 'selected' : '' }}>日历</option>
                                    <option value="2" {{ $restrict_gene->type == 2 ? 'selected' : '' }}>日历+下拉框</option>
                                    <option value="3" {{ $restrict_gene->type == 3 ? 'selected' : '' }}>文本输入框 </option>
                                    <option value="4" {{ $restrict_gene->type == 4 ? 'selected' : '' }}>地区</option>
                                    <option value="5" {{ $restrict_gene->type == 5 ? 'selected' : '' }}>职业</option>
                                    <option value="6" {{ $restrict_gene->type == 6 ? 'selected' : '' }}>文本</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="type" class="text-right col-md-1 control-label">关联条款</label>
                            <div class="col-md-4">
                                <select name="clause_id" id="type" class="form-control">
                                    <option value="0">无</option>
                                    @foreach($clauses as $k => $v)
                                        <option value="{{$v->id}}" @if($v->id == $restrict_gene->clause_id) selected @endif>{{$v->name}}</option>
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
                                <a href="{{ route('restrict_genes.index', $restrict_gene->bind_id) }}" class="btn btn-primary  ">返回</a>
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
                $('#ty_key').val('');
                $('#ty_key_on').attr("readonly", false);
            } else {
                var value = $(this).find("option:selected").attr('data-value');
                if(value!=''||value!=null){
                    $('#ty_key_on').val(value);
                    $('#ty_key').val(value);
                }
                $(".custom_name").remove();
            }
        });
    </script>
@endsection