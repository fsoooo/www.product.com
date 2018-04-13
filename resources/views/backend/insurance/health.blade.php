@extends('backend.layout.base')
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <div class="panel">
                <div class="panel-heading">
                    <p class="panel-title">
                        健康告知
                        <a href="" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal">添加健康告知</a>
                        <a href="{{ route('insurance.bind.list') }}" class="btn btn-primary btn-sm">返回绑定列表</a>
                    </p>
                </div>
                <div class="panel-body">
                    @include('backend.layout.alert_info')
                    <table class="table table-condensed" style=" table-layout:fixed;word-wrap:break-word;">
                        <tr>
                            <th>告知内容</th>
                            <th>默认选中值</th>
                            <th>展示顺序</th>
                            <th>限制条件</th>
                            <th>限制条件值</th>
                            <th>操作</th>
                        </tr>
                        @if(count($health_res)!=0)
                            @foreach($health_res  as $value)
                                <tr>
                                    <td style="white-space:nowrap;overflow:hidden;text-overflow: ellipsis;">{{$value['content']}}</td>
                                    <td>{{$value['checked']}}</td>
                                    <td>{{$value['order']}}</td>
                                    <td>{{$value['condition']}}</td>
                                    <td>{{$value['condition_value']}}</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal{{$value['id']}}">查看详情</button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- 模态框 -->
    <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="myModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">编辑健康告知</h4>
                </div>
                <form role="form" action="{{asset('backend/product/insurance/health_submit')}}" method="post">
                    {{ csrf_field() }}
                <div class="modal-body">
                    <table class="table">
                        <tr>
                            <th>告知内容</th>
                            <th>默认选中值</th>
                            <th>展示顺序</th>
                            <th>限制条件</th>
                            <th>限制条件值</th>
                        </tr>
                        <tr>
                            <td><textarea cols="25" rows="15" name="content"></textarea></td>
                            <td><input type="text" name="checked"></td>
                            <td><input type="text" name="order"></td>
                            <td><input type="text" name="condition"></td>
                            <td><input type="text" name="condition_value"></td>
                            <td><input type="hidden" name="insurance_id" value="{{$id}}"></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button  class="btn btn-primary">确认提交</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <!-- 模态框 -->
    @if(count($health_res)!=0)
        @foreach($health_res  as $value)
            <!-- 模态框 -->
            <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="myModal{{$value['id']}}">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">编辑健康告知</h4>
                        </div>
                        <form role="form" action="{{asset('backend/product/insurance/health_submit')}}" method="post">
                            {{ csrf_field() }}
                            <div class="modal-body">
                                <table class="table">
                                    <tr>
                                        <th>告知内容</th>
                                        <th>默认选中值</th>
                                        <th>展示顺序</th>
                                        <th>限制条件</th>
                                        <th>限制条件值</th>
                                    </tr>
                                    <tr>
                                        <td><textarea cols="25" rows="15" name="content">{{$value['content']}}</textarea></td>
                                        <td><input type="text" name="checked" value="{{$value['checked']}}"></td>
                                        <td><input type="text" name="order" value="{{$value['order']}}"></td>
                                        <td><input type="text" name="condition" value="{{$value['condition']}}"></td>
                                        <td><input type="text" name="condition_value" value="{{$value['condition_value']}}"></td>
                                        <td><input type="hidden" name="insurance_id" value="{{$id}}"></td>
                                        <td><input type="hidden" name="health_id" value="{{$value['id']}}"></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button  class="btn btn-primary">确认提交</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
@endsection