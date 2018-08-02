@extends('backend.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('r_backend/css/libs/nifty-component.css')}}"/>
    <style>
        .dty{float:left;width:120px;text-align:right;margin-right:10px;}
    </style>
@endsection
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="/backend/">主页</a></li>
                <li ><span>产品管理</span></li>
                <li class="active"><span>保险产品</span></li>
            </ol>

            <div class="main-box" style="width: 100%">
                @include('backend.layout.alert_info')
                <header class="main-box-header clearfix">
                    <h2>添加保险产品</h2>
                </header>
                <div class="main-box-body clearfix" style="width: 60%">
                    <form role="form" action="{{asset('backend/product/insurance/editSubmit')}}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="insurance_id" value="{{$data->id}}">
                        <div class="form-group">
                            <label for="exampleInputEmail1"><span class="red">*</span>保险产品简称</label>
                            <input class="form-control" placeholder="保险产品简称" name="display_name" type="text" value="{{ $data->display_name }}">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1"><span class="red">*</span>保险产品全称</label>
                            <input class="form-control" placeholder="保险产品全称" name="name" type="text" value="{{ $data->name }}">
                        </div>

                        <div class="form-group">
                            <label><span class="red">*</span>所属公司</label>
                            <select name="company_id" class="form-control" id="company">
                                <option value="" selected>--选择公司--</option>
                                @foreach($companies as $k => $v)
                                    <option value="{{$v->id}}" @if($data->company_id == $v->id) selected @endif>
                                        {{$v->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label><span class="red">*</span>产品类型</label>
                            <select name="insurance_type" id="tx" class="form-control">
                                <option value="1" @if($data->type == 1) selected @endif>个险</option>
                                <option value="2" @if($data->type == 2) selected @endif>团险</option>
                            </select>
                        </div>

                        <div class="form-group tx" @if($data->type == 1) style="display: none" @endif>
                            <label for="exampleInputEmail1">团险最小投保人数</label>
                            <input class="form-control" placeholder="团险最小投保人数" name="min_math" type="text" value="{{ $data->min_math }}">
                        </div>
                        <div class="form-group tx" @if($data->type == 1) style="display: none" @endif>
                            <label for="exampleInputEmail1">团险最大投保人数</label>
                            <input class="form-control" placeholder="团险最大投保人数" name="max_math" type="text" value="{{ $data->max_math }}">
                        </div>
                        <div class="form-group">
                            <label><span class="red">*</span>保险产品分类</label>
                            <select name="category_id" class="form-control">
                                <option value="" disabled selected>--选择分类--</option>
                                @foreach($categories as $k => $v)
                                    <option value="{{$v->id}}" @if($data->category_id == $v->id) selected @endif><?php echo str_repeat('|----' , $v['sort']) . $v['name'] ?></option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">基础保费</label>
                            <input class="form-control" placeholder="基础保费（元）" name="base_price" type="text" value="{{ $data->base_price / 100 }}">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">基础佣金</label>
                            <input class="form-control" placeholder="缴别（例：5年 10年 15年）" name="base_stages_way" type="text" value="{{ $data->base_stages_way }}">
                            <input class="form-control" placeholder="佣金比（单位：%）" name="base_ratio" type="text" value="{{ $data->base_ratio }}">
                        </div>
                        <div class="form-group">
                            {{--<label for="exampleInputEmail1">投保日期限制</label>--}}
                            最小起保天数(参照当前日期)：<input class="form-control" placeholder="例：1  T+1至少明天可投" name="first_date" type="text" value="{{ $data->first_date }}" >
                            最大起保天数(参照当前日期)：<input class="form-control" placeholder="例：30  T+30最多一个月内可投" name="latest_date" type="text" value="{{ $data->latest_date }}">
                            观察期(天)：<input class="form-control" placeholder="例：7" name="observation_period" type="text" value="{{ $data->observation_period }}">
                            犹豫期（天）：<input class="form-control" placeholder="例：7" name="period_hesitation" type="text" value="{{ $data->period_hesitation }}">
                        </div>
                        @foreach($companies as $ck => $cv)
                            <div class="{{'company_clause_' . $cv->id}} clause_list @if($data->company_id != $cv->id)hide @endif">
                                {{--{{dd($cv->clauses)}}--}}
                                <div class="form-group duty duty_main">
                                    <label><span class="red">*</span>关联 {{ $cv->display_name }} 主险条款:</label>
                                    @foreach($cv->clauses as $pk => $clause)
                                        @if($clause->type == 'main')
                                            <div class="checkbox-nice" style="margin-left: 20px;">
                                                <input id="checkbox-main-{{$cv->id}}-{{$pk}}" class='clause_main_{{$cv->id}}' name="clause_main_ids[{{$cv->id}}][]" type="checkbox" value="{{$clause->id}}" @if(in_array($clause->id, $clause_ids)) checked @endif>
                                                <label for="checkbox-main-{{$cv->id}}-{{$pk}}">
                                                    {{$clause->display_name}}
                                                </label>
                                                @if(count($clause->duties))
                                                    <span style="margin-left: 10px;">保额倍数：</span>
                                                    <input type="text" placeholder="多选项用英文字符 , 号隔开" name="coverage_bs[{{$clause->id}}]" @if(in_array($clause->id, $clause_ids) && count($clause_coverage_bs)) value="{{ $clause_coverage_bs[$clause->id] }}" @endif>
                                                @endif
                                            </div>
                                            <ul style="margin-left: 40px;">
                                                @if(count($clause->duties))
                                                    <div>责任保额：</div>
                                                    @foreach($clause->duties as $dk => $dv)
                                                        <ul style="margin-left: 10px;list-style: none;">
                                                            <li class="dty">{{$dv->name}}:</li>
                                                            <li class="dty">
                                                                <input type="text" placeholder="输入基本保额" name="duty_coverage[{{$clause->id}}][{{$dv->id}}]" @if(in_array($clause->id, $clause_ids) && count($coverage_jc)) value="{{ $coverage_jc[$clause->id][$dv->id] }}" @endif >
                                                            </li>
                                                            <div style="clear:both;"></div>
                                                        </ul>
                                                    @endforeach
                                                @endif
                                            </ul>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="form-group duty duty_attach">
                                    <label>关联 {{ $cv->display_name }} 附加险条款:</label>
                                    @foreach($cv->clauses as $pk => $clause)
                                        @if($clause->type == 'attach')
                                            <div class="checkbox-nice" style="margin-left: 20px;">
                                                <input id="checkbox-attach-{{$cv->id}}-{{$pk}}" class='clause_attach_{{$cv->id}}' name="clause_attach_ids[{{$cv->id}}][]" type="checkbox" value="{{$clause->id}}" @if(in_array($clause->id, $clause_ids)) checked @endif>
                                                <label for="checkbox-attach-{{$cv->id}}-{{$pk}}">
                                                    {{$clause->display_name}}
                                                </label>
                                                <span style="margin-left: 10px;">保额倍数：</span>
                                                <input type="text" placeholder="多选项用英文字符 , 号隔开" name="coverage_bs[{{$clause->id}}]" @if(in_array($clause->id, $clause_ids) && count($clause_coverage_bs)) value="{{ $clause_coverage_bs[$clause->id] }}" @endif>
                                            </div>
                                            <ul style="margin-left: 40px;">
                                                <div>责任保额：</div>
                                                @foreach($clause->duties as $dk => $dv)
                                                    <ul style="margin-left: 10px;list-style: none;">
                                                        <li class="dty">{{$dv->name}}:</li>
                                                        <li class="dty">
                                                            <input type="text" placeholder="输入基本保额" name="duty_coverage[{{$clause->id}}][{{$dv->id}}]" @if(in_array($clause->id, $clause_ids) && count($coverage_jc)) value="{{ $coverage_jc[$clause->id][$dv->id] }}" @endif >
                                                        </li>
                                                        <div style="clear:both;"></div>
                                                    </ul>
                                                @endforeach
                                            </ul>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <div class="form-group">
                            <label for="exampleTextarea"><span class="red">*</span>保险产品说明</label>
                            <textarea class="form-control" id="exampleTextarea" name="content" rows="3">{{ $data->content }}</textarea>
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
        $(":input[name=company_id]").change(function(){
            $(".clause_list").addClass('hide');
            var id = $(this).val();
            $(".clause_main_"+ id).removeAttr('checked');
            $(".clause_attach_"+ id).removeAttr('checked');
            var show_clauses_name = 'company_clause_' + id;
            $("." + show_clauses_name).removeClass('hide');
        });
        //        $("#type").change(function(){
        //            $(".duty").addClass('hide');
        //            $('input:checkbox').attr('checked',false);
        //
        //            var val = $(this).val();
        //            if(val == 'main'){
        //                $(".duty_main").removeClass('hide');
        //
        //            } else {
        //                $(".duty_attach").removeClass('hide');
        //            }
        //        });
    </script>
    <script>
        $(document).ready(function(){
            $("#tx").change(function(){
                if ($('#tx').val() == 2){
                    $('.tx').show();
                }else{
                    $('.tx').hide();
                }
            })
        })
    </script>

@stop

