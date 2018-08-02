@extends('backend.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('r_backend/css/libs/nifty-component.css')}}"/>
@endsection
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <form id="form" class="form" method="post" action="{{ url('backend/product/insurance_attribute/store', $insurance->private_p_code) }}">
            {{ csrf_field() }}
            <!-- <div class="main-box clearfix"> -->
                <div class="panel">
                    <div class="panel-heading">
                        <p class="panel-title">投保人</p>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <select class="form-control">
                                <option value="{{ $insurance->id }}">{{ $insurance->name }}</option>
                            </select>
                        </div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>表单Key</th>
                                    <th>显示名称</th>
                                    <th>显示类型</th>
                                    <th>是否必填</th>
                                    <th>默认提醒信息</th>
                                    <th>出错提醒信息</th>
                                    <th>属性值列表</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="display: none;">
                                    <td>
                                        <div class="form-group">
                                            <input type="text" name="attributes[toubaoren][_][key]" class="form-control">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" name="attributes[toubaoren][_][name]" class="form-control">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <select name="attributes[toubaoren][_][type]" class="form-control">
                                                <option value="0">下拉框</option>
                                                <option value="1">日历</option>
                                                <option value="2">日历+下拉框</option>
                                                <option value="3">文本输入框 </option>
                                                <option value="4">地区</option>
                                                <option value="5">职业</option>
                                                <option value="6">文本</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <select name="attributes[toubaoren][_][required]" class="form-control">
                                                <option value="1">是</option>
                                                <option value="0">否</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" name="attributes[toubaoren][_][defaultRemind]" class="form-control">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" name="attributes[toubaoren][_][errorRemind]" class="form-control">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <a href="javascript:;" class="btn btn-modal md-trigger" data-modal="modal-_">
                                                <i class="fa fa-plus-circle fa-lg"></i>
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <a href="javascript:;" class="table-link danger tr-delete">
                                                <span class="fa-stack">
                                                    <i class="fa fa-square fa-stack-2x"></i>
                                                    <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                                </span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @if (!empty($attributes['toubaoren']))
                                    @foreach ($attributes['toubaoren'] as $key => $attribute)
                                        <tr>
                                            <td>
                                                <div class="form-group">
                                                    <input type="text" name="attributes[toubaoren][{{ $key }}][key]" class="form-control" value="{{ $attribute['key'] }}">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <input type="text" name="attributes[toubaoren][{{ $key }}][name]" class="form-control" value="{{ $attribute['name'] }}">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <select name="attributes[toubaoren][{{ $key }}][type]" class="form-control">
                                                        <option value="0" {{ $attribute['key'] == 0 ? 'selected' : '' }}>下拉框</option>
                                                        <option value="1" {{ $attribute['key'] == 1 ? 'selected' : '' }}>日历</option>
                                                        <option value="2" {{ $attribute['key'] == 2 ? 'selected' : '' }}>日历+下拉框</option>
                                                        <option value="3" {{ $attribute['key'] == 3 ? 'selected' : '' }}>文本输入框 </option>
                                                        <option value="4" {{ $attribute['key'] == 4 ? 'selected' : '' }}>地区</option>
                                                        <option value="5" {{ $attribute['key'] == 5 ? 'selected' : '' }}>职业</option>
                                                        <option value="6" {{ $attribute['key'] == 6 ? 'selected' : '' }}>文本</option>
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <select name="attributes[toubaoren][_][required]" class="form-control">
                                                        <option value="1">是</option>
                                                        <option value="0">否</option>
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <input type="text" name="attributes[toubaoren][_][defaultRemind]" class="form-control">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <input type="text" name="attributes[toubaoren][_][errorRemind]" class="form-control">
                                                </div>
                                            </td>
                                            <td>
                                                <a href="javascript:;" class="btn">
                                                    <i class="fa fa-plus-circle fa-lg"></i>
                                                </a>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <a href="javascript:;" class="table-link danger tr-delete">
                                                        <span class="fa-stack">
                                                            <i class="fa fa-square fa-stack-2x"></i>
                                                            <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                                        </span>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        <button class="btn btn-primary button-append">追加</button>
                    </div>
                </div>
                <div class="panel">
                    <div class="panel-heading">
                        <p class="panel-title">被保人</p>
                    </div>
                    <div class="panel-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>表单Key</th>
                                    <th>显示名称</th>
                                    <th>显示类型</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="display: none;">
                                    <td>
                                        <div class="form-group">
                                            <input type="text" name="attributes[beibaoren][_][key]" class="form-control">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" name="attributes[beibaoren][_][name]" class="form-control">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <select name="attributes[beibaoren][_][type]" class="form-control">
                                                <option value="0">下拉框</option>
                                                <option value="1">日历</option>
                                                <option value="2">日历+下拉框</option>
                                                <option value="3">文本输入框 </option>
                                                <option value="4">地区</option>
                                                <option value="5">职业</option>
                                                <option value="6">文本</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <a href="javascript:;" class="table-link danger tr-delete">
                                                <span class="fa-stack">
                                                    <i class="fa fa-square fa-stack-2x"></i>
                                                    <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                                </span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @if (!empty($attributes['beibaoren']))
                                    @foreach ($attributes['beibaoren'] as $key => $attribute)
                                        <tr>
                                            <td>
                                                <div class="form-group">
                                                    <input type="text" name="attributes[beibaoren][{{ $key }}][key]" class="form-control" value="{{ $attribute['key'] }}">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <input type="text" name="attributes[beibaoren][{{ $key }}][name]" class="form-control" value="{{ $attribute['name'] }}">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <select name="attributes[beibaoren][{{ $key }}][type]" class="form-control">
                                                        <option value="0" {{ $attribute['key'] == 0 ? 'selected' : '' }}>下拉框</option>
                                                        <option value="1" {{ $attribute['key'] == 1 ? 'selected' : '' }}>日历</option>
                                                        <option value="2" {{ $attribute['key'] == 2 ? 'selected' : '' }}>日历+下拉框</option>
                                                        <option value="3" {{ $attribute['key'] == 3 ? 'selected' : '' }}>文本输入框 </option>
                                                        <option value="4" {{ $attribute['key'] == 4 ? 'selected' : '' }}>地区</option>
                                                        <option value="5" {{ $attribute['key'] == 5 ? 'selected' : '' }}>职业</option>
                                                        <option value="6" {{ $attribute['key'] == 6 ? 'selected' : '' }}>文本</option>
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <a href="javascript:;" class="btn btn-modal md-trigger" data-modal="modal-{{ $key }}">
                                                        <i class="fa fa-plus-circle fa-lg"></i>
                                                    </a>
                                                </div>
                                                <div class="md-modal md-effect-0 md-hide" id="modal-{{ $key }}">
                                                    <div class="md-content">
                                                        <div class="modal-header">
                                                            <a class="md-close close">×</a>
                                                            <h4 class="modal-title">试算因子选项</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <table class="table">
                                                                <tr>
                                                                    <th>属性值名称</th>
                                                                    <th>控件值</th>
                                                                    <th>约束条件</th>
                                                                    <th>属性值校验正则表达式</th>
                                                                    <th>正则约束条件验证失败提示</th>
                                                                    <th>所限制属性的控件类型</th>
                                                                    <th>单位</th>
                                                                    <th>操作</th>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <div class="form-group">
                                                                            <input id="value" type="text" class="form-control">
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="form-group">
                                                                            <input id="value" type="text" class="form-control">
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="form-group">
                                                                            <input id="value" type="text" class="form-control">
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="form-group">
                                                                            <input id="value" type="text" class="form-control">
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="form-group">
                                                                            <input id="value" type="text" class="form-control">
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="form-group">
                                                                            <input id="value" type="text" class="form-control">
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="form-group">
                                                                            <input id="value" type="text" class="form-control">
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <a href="javascript:;" class="table-link danger tr-delete">
                                                        <span class="fa-stack">
                                                            <i class="fa fa-square fa-stack-2x"></i>
                                                            <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                                        </span>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        <button class="btn btn-primary button-append">追加</button>
                        <button class="btn btn-primary">提交</button>
                    </div>
                </div>
            <!-- </div> -->
            </form>
        </div>
    </div>
<!-- content ends -->
<div class="md-overlay"></div>
@endsection
<style>
    .md-modal {
        max-width: 1200px !important;
        min-width: 1200px !important;
    }
</style>
@section('foot-js')
    <script charset="utf-8" src="/r_backend/js/modernizr.custom.js"></script>
    <script charset="utf-8" src="/r_backend/js/classie.js"></script>
    <script charset="utf-8" src="/r_backend/js/modalEffects.js"></script>
    <script>
        $(function () {
            $(".button-append").click(function (event) {
                var content = $(this).prev(".table").find('tbody tr:first').html();
                var index = $(this).prev(".table").find('tbody tr:first').length - 1;
                event.preventDefault();
                var tbody = $(this).prev(".table").find('tbody');
                var append = content.replace(/_/g, index);
                tbody.append('<tr>'+append+'</tr>');
            });
            $("table").on("click", '.tr-delete', function (event) {
                event.preventDefault();
                $(this).parents("tr").remove();
            });
            $("table").on("click", '.btn-modal', function (event) {
                event.preventDefault();
                var id = $(this).attr('data-modal').split('-')[1];
                var b = $("#modal-" + id).removeClass('md-hide').addClass("md-show");
            });
            $(".table").on("click", '.md-close', function (event) {
                event.preventDefault();
                $(this).parents(".md-modal").removeClass('md-show').addClass('md-hide');
            });
        });
    </script>
@endsection
