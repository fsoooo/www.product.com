@extends('backend.layout.base')
@section('css')

@endsection
@section('content')
    <div id="content-wrapper">
        <div class="row">
            <div class="col-lg-10" style="width:100%; height:100%;">
                <div class="row">
                    <div class="col-lg-12">
                        <ol class="breadcrumb">
                            <li><a href="{{ url('/backend') }}">主页</a></li>
                            <li ><span>销售管理</span></li>
                            <li ><span>代理人渠道管理</span></li>
                            <li class="active"><span><a href="/backend/sell/ditch_agent/ditch_bind_agent">代理人渠道关联</a></span></li>
                        </ol>
                    </div>
                </div>
                <div class="main-box" style="min-height:1200px">
                    <header class="main-box-header clearfix">
                        <h2>渠道代理人绑定</h2>
                    </header>
                    @include('backend.layout.alert_info')
                    <div class="main-box-body clearfix">
                        <form role="form" id='select_role' action="{{asset('backend/sell/ditch_agent/ditch_find_agent')}}" method="post">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label>选择渠道</label>
                                <select id="role_select" class="form-control" name="ditch_id" style="width:70%;">
                                    <option value="0" selected disabled>--请选择渠道--</option>
                                    @foreach($ditches as $rk => $rv)
                                        <option value="{{$rv->id}}" @if(!empty($ditch) && $ditch->id == $rv->id)selected @endif>{{$rv->display_name}}----{{$rv->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                        <form role="form" id="bind" action="{{asset('backend/sell/ditch_agent/attach_agents')}}" method="post">
                            {{ csrf_field() }}
                            <label><h4>关联代理人</h4></label>
                            @if(!empty($ditch))
                                <label for="" style="margin-left: 20px;">
                                    <button type="button" id="attach_submit" class="btn btn-success">提交绑定关系</button>
                                    (若所选代理人为空则标识清空改渠道下的代理人)
                                </label>
                            <div class="form-group">
                                    <input type="hidden" name="check_ditch_id" value="{{$ditch->id}}">
                                    @foreach($agents as $pk => $pv)
                                        <div class="checkbox-nice" style="float:left;margin-left: 20px;">
                                            <input id="checkbox-{{$pk}}" name="agent_ids[]" @if(!empty($ditch_agent_ids) && in_array($pv->id, $ditch_agent_ids)) checked @endif
                                            type="checkbox" value="{{$pv->id}}">
                                            <label for="checkbox-{{$pk}}">
                                                {{$pv->user->name}}----{{$pv->user->real_name}}
                                            </label>
                                        </div>
                                    @endforeach
                            </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('foot-js')
    <script>
        $('#role_select').change(function(){
            var role_id = $(this).val();
            if(role_id != 0){
                $('#select_role').submit();
            }
        });
        $("#attach_submit").click(function(){
            $('#bind').submit();
        })
    </script>
@stop

