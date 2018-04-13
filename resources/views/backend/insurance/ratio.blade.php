@extends('backend.layout.base')
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <div class="panel">
                <div class="panel-heading">
                    <p class="panel-title">佣金设置->{{$bind->insurance->display_name}}  \  {{$bind->apiFrom->name}}</p>
                </div>
                <form action="{{url('/backend/product/brokerage/edit/do_submit')}}" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="bind_id" value="{{$bind->id}}">
                    <div class="panel-body">
                        @include('backend.layout.alert_info')
                        <table class="table table-responsive table-hover">
                            {{--<tr>--}}
                                {{--<select name="premium_count">--}}
                                    {{--<option></option>--}}
                                {{--</select>--}}
                            {{--</tr>--}}
                            <tr>
                                <th>缴期方式 (0为趸交)</th>
                                <th>单位</th>
                                <th>内部收益佣金比</th>
                                <th>渠道支出佣金比</th>
                                <th><button id="add-list" type="button" class="btn btn-primary btn-sm">增加行</button></th>
                            </tr>
                            @if(count($bind->insApiBrokerage) < 1)
                                <tr class="ratios">
                                    <td><input name="pay_type[]" type="text"></td>
                                    <td>
                                        <select class='unit' name="pay_type_unit[]">
                                            <option value="年">年</option>
                                            <option value="月">月</option>
                                            <option value="天">天</option>
                                        </select>
                                    </td>
                                    <td><input name="ratio_for_us[]" type="text"></td>
                                    <td><input name="ratio_for_out[]" type="text"></td>
                                    <td><button type="button" class="btn btn-primary btn-sm delete-list">删除</button></td>
                                </tr>
                            @else
                                @foreach($bind->insApiBrokerage as $k => $v)
                                    <tr class="ratios">
                                        <td><input name="pay_type[]" value="{{$v->by_stages_way}}" type="text"></td>
                                        <td>
                                            <select class='unit' name="pay_type_unit[]">
                                                <option value="年" @if($v->pay_type_unit == '年') selected @endif>年</option>
                                                <option value="月" @if($v->pay_type_unit == '月') selected @endif>月</option>
                                                <option value="天" @if($v->pay_type_unit == '天') selected @endif>天</option>
                                            </select>
                                        </td>
                                        <td><input name="ratio_for_us[]" value="{{$v->ratio_for_us}}" type="text"></td>
                                        <td><input name="ratio_for_out[]" value="{{$v->ratio_for_agency}}" type="text"></td>
                                        <td><button type="button" class="btn btn-primary btn-sm delete-list">删除</button></td>
                                    </tr>
                                @endforeach
                            @endif
                        </table>
                        <div style="text-align: center">
                            <button class="btn btn-primary btn-sm">提交</button>
                            <button type="button" class="btn btn-default btn-sm" onclick="window.location.href='/backend/product/insurance/bind/list'">返回</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection
@section('foot-js')
    <script>
        var html = '<tr class="ratios">' +
                '<td><input name="pay_type[]" type="text"></td> ' +
                '<td> ' +
                    '<select class="unit" name="pay_type_unit[]">' +
                        '<option value="年">年</option> ' +
                        '<option value="月">月</option> ' +
                        '<option value="天">天</option> ' +
                    '</select> ' +
                '</td>' +
                '<td><input name="ratio_for_us[]" type="text"></td> ' +
                '<td><input name="ratio_for_out[]" type="text"></td>' +
                '<td><button type="button" class="btn btn-primary btn-sm delete-list">删除</button></td>' +
                '</tr>';
        $("#add-list").click(function(){
            $('.table').last().append($(html));
        })
        $('.table').on('click', ".delete-list", function(){
            $(this).parent().parent().remove();
        })
    </script>
@stop