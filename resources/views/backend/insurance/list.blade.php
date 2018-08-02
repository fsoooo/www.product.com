@extends('backend.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('r_backend/css/libs/nifty-component.css')}}"/>
@endsection
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <div class="panel">
                <div class="panel-heading">
                    <p class="panel-title">产品与API来源绑定列表</p>
                </div>
                <div class="panel-body">
                    @include('backend.layout.alert_info')
                    <table class="table table-responsive table-hover">
                    <tr>
                        <th>产品</th>
                        <th>API来源</th>
                        <th>产品码</th>
                        <th>内部产品码</th>
                        {{--<th>绑定状态</th>--}}
                        <th>接口支持</th>
                        <th>产品状态</th>
                        <th>操作</th>
                    </tr>
                    @foreach($lists as $list)
                        <tr>
                            <td>{{ $list->insurance_name }}</td>
                            <td>{{ $list->api_from_name }}</td>
                            <td>{{ $list->p_code }}</td>
                            <td>{{ $list->private_p_code }}</td>
                            {{--<td>--}}
                                {{--{{ $list->status == 1 ? '绑定' : '解绑' }}--}}
                            {{--</td>--}}
                            <td><a target="_blank" href="/backend/product/insurance/other_support/{{$list->bind_id}}">前往配置</a></td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary">
                                        {{$list->sell_status == '0' ? '待调试' : ($list->sell_status == '1' ? '调试中' : '已上架')}}
                                    </button>
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu" role="menu">
                                        <li class='status_li md-trigger' ins_status="0" insurance_id="{{$list->insurance_id}}" data-modal="modal-1"><a>待调试</a></li>
                                        <li class='status_li md-trigger' ins_status="1" insurance_id="{{$list->insurance_id}}" data-modal="modal-1"><a>调试中</a></li>
                                        <li class='status_li md-trigger' ins_status="2" insurance_id="{{$list->insurance_id}}" data-modal="modal-1"><a>已上架</a></li>
                                        {{--<li class="divider"></li>--}}
                                        {{--<li><a href="#">Separated link</a></li>--}}
                                    </ul>
                                </div>
                            </td>
                            <td>
                                @if(empty($list->private_p_code))
                                    <a href="{{ route('insurance.bind.pcode', [$list->bind_id]) }}" class="btn btn-primary btn-sm">录入产品码</a>
                                @endif
                                @if ($list->private_p_code)
                                        <a href="{{ route('insurance.bind.pcode', [$list->bind_id]) }}" class="btn btn-primary btn-sm">更新产品码</a>
                                    @if($list->uuid != 'Qx') {{--惠泽-齐心云服产品不需配置--}}
                                        <a href="{{ route('insurance_attributes.modules.index', $list->bind_id) }}" class="btn btn-primary btn-sm">投保属性</a>
                                        <a href="{{ route('restrict_genes.index', $list->bind_id) }}" class="btn btn-primary btn-sm">试算因子</a>
                                        <a href="{{ url('backend/product/insurance/health/'. $list->insurance_id) }}" class="btn btn-primary btn-sm">产品告知</a>
                                    @endif
                                        <a href="{{ url('backend/product/brokerage/show/'. $list->bind_id) }}" class="btn btn-primary btn-sm">佣金设置</a>

                                @endif
                                @if($list->insurance_type == 2)
                                    <a href="{{ route('insurance.bind.template', [$list->insurance_id]) }}" class="btn btn-primary btn-sm">上传团险模板</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </table>
                    {{ $lists->links() }}
                </div>
            </div>
        </div>
    </div>

    <div class="md-modal md-effect-8 md-hide" id="modal-1">
        <div class="md-content">
            <div class="modal-header">
                <button class="md-close close">×</button>
                <h4 class="modal-title">修改确认</h4>
            </div>
            <div class="modal-body">
                <form role="form" id="" action='{{url('backend/product/insurance/do_sell_status')}}' method="get">
                    {{ csrf_field() }}
                    <input type="hidden" name="sell_status" id="sell_status">
                    <input type="hidden" name="id" id="ins_id">
                    <div class="form-group" style="margin:10px;">
                        <label for="exampleInputEmail1">确定修改产品状态？</label>
                        <button style="float: right;" type="button" id="close" class="btn btn-default">取消</button>
                        <button style="float: right;" class="btn btn-primary">确认</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="md-overlay"></div>
@endsection
@section('foot-js')
    <script charset="utf-8" src="/r_backend/js/modernizr.custom.js"></script>
    <script charset="utf-8" src="/r_backend/js/classie.js"></script>
    <script charset="utf-8" src="/r_backend/js/modalEffects.js"></script>
    <script type="text/javascript">
        $("#close").click(function(){
            $("#modal-1").removeClass('md-show');
            $("#modal-1").addClass('md-hide');
        });
        $(".status_li").click(function(){
            sell_status = $(this).attr('ins_status');
            insurance_id = $(this).attr('insurance_id');
            $("#sell_status").val(sell_status);
            $("#ins_id").val(insurance_id);
        });
    </script>

@stop