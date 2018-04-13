@extends('backend.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('r_backend/css/libs/nifty-component.css')}}"/>
@endsection
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="/backend/">主页</a></li>
                <li ><span>产品管理</span></li>
                <li class="active"><span>条款</span></li>
            </ol>

                <div class="main-box" style="width: 100%">
                    @include('backend.layout.alert_info')
                    <header class="main-box-header clearfix">
                        <h2>添加条款</h2>
                    </header>
                    <div class="main-box-body clearfix" style="width: 60%">
                        <form role="form" action="{{asset('backend/product/clause/addPost')}}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label for="exampleInputEmail1"><span class="red">*</span>条款简称</label>
                                <input class="form-control" placeholder="条款简称" name="display_name" type="text">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1"><span class="red">*</span>条款全称</label>
                                <input class="form-control" placeholder="条款全称" name="name" type="text">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">险别代码</label>
                                <input class="form-control" placeholder="条款全称" name="clause_code" type="text">
                            </div>
                            <div class="form-group">
                                <label><span class="red">*</span>条款分类</label>
                                <select name="category_id" class="form-control">
                                    <option value="" disabled selected>--选择分类--</option>
                                    @foreach($categories as $k => $v)
                                        <option value="{{$v->id}}"><?php echo str_repeat('|----' , $v['sort']) . $v['name'] ?></option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label><span class="red">*</span>所属公司</label>
                                <select name="company_id" class="form-control">
                                    <option value="" disabled selected>--选择公司--</option>
                                    @foreach($companies as $k => $v)
                                        <option value="{{$v->id}}">{{$v->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label><span class="red">*</span>主附类型</label>
                                <select name="type" id="type" class="form-control">
                                        <option value="" disabled selected>--选择类型--</option>
                                        <option value="main">主险条款</option>
                                        <option value="attach">附加险条款</option>
                                </select>
                            </div>
                            <div class="form-group duty duty_main hide">
                                <label><span class="red">*</span>关联主险责任</label>
                                @foreach($duties as $pk => $pv)
                                    @if($pv->type == 'main')
                                    <div class="checkbox-nice" style="margin-left: 20px;">
                                        <input id="checkbox-{{$pk}}" name="duty_main_ids[]" type="checkbox" value="{{$pv->id}}">
                                        <label for="checkbox-{{$pk}}">
                                            {{$pv->name}}:&nbsp;&nbsp;&nbsp;&nbsp;{{$pv->description}}
                                        </label>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                            <div class="form-group duty duty_attach hide">
                                <label>关联附加险责任</label>
                                @foreach($duties as $pk => $pv)
                                    @if($pv->type == 'attach')
                                    <div class="checkbox-nice" style="margin-left: 20px;">
                                        <input id="checkbox-{{$pk}}" name="duty_attach_ids[]" type="checkbox" value="{{$pv->id}}">
                                        <label for="checkbox-{{$pk}}">
                                            {{$pv->name}}：&nbsp;&nbsp;&nbsp;&nbsp;{{$pv->description}}
                                        </label>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                            {{--<div class="form-group">--}}
                                {{--<label for="exampleTextarea">保额倍数(多个用英文字符 , 隔开)</label>--}}
                                {{--<input class="form-control" placeholder="基础保额,最大不得超过100W" name="coverage_bs" type="text">--}}
                            {{--</div>--}}
                            <div class="form-group">
                                <label for="exampleTextarea"><span class="red">*</span>说明附件上传</label>
                                <input class="form-control" placeholder="附件上传" name="file" type="file">
                            </div>
                            <div class="form-group">
                                <label for="exampleTextarea"><span class="red">*</span>条款说明</label>
                                <textarea class="form-control" id="exampleTextarea" name="content" rows="3">详情见条款附件</textarea>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" id="form-submit" class="btn btn-primary">确认提交</button>
                            </div>
                        </form>
                    </div>
                </div>

        </div>
    </div>
@stop
@section('foot-js')
    <script charset="utf-8" src="/r_backend/js/modernizr.custom.js"></script>
    <script charset="utf-8" src="/r_backend/js/classie.js"></script>
    <script charset="utf-8" src="/r_backend/js/modalEffects.js"></script>
    <script>
        $("#type").change(function(){
            $(".duty").addClass('hide');
            $('input:checkbox').attr('checked',false);

            var val = $(this).val();
            if(val == 'main'){
                $(".duty_main").removeClass('hide');

            } else {
                $(".duty_attach").removeClass('hide');
            }
        });

        $("#b_add_bao_e").click(function(){
            var $bt = $(this);
            var $v = $("#jb_bao_e").val(); //基本保额
            var $c = $("#m_num").val(); //基数
            var $num = $("#add_num").val(); //填充数量
            if(!$v || !$c || !$num){
                alert('请输入基本保额、基数和填充数量');
            } else {
                $str = '';
                for(var $i=1; $i<=$num; $i++){
                    var $val = $c * $i * $v;
                    if($val == $v){
                        $num ++;
                        continue;
                    }

                    $str += '<input type="input" class="other_bao_e" name="bao_e[]" placeholder="保额基数" value="'+ $val +'">';
                    $str += '<button class="del_bao_e" type="button">-</button><br>';
                }
                $bt.prev().prev().before($str);
            }
        });
        $("body").on('click', '.del_bao_e', function(){
            $(this).next().remove();
            $(this).prev().remove();
            $(this).remove();
        });
        $("#jb_bao_e").focus(function(){
            var $old_val = $(this).val();
            $("#jb_bao_e").blur(function(){
                if($old_val != $(this).val()){
                    $('.other_bao_e').remove();
                    $('.del_bao_e').next().remove();
                    $('.del_bao_e').remove();
                }
            });
        });

    </script>
@stop

