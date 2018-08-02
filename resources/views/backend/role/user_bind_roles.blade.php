@extends('backend.layout.base')
@section('css')

@endsection
@section('content')
    <div id="content-wrapper">
        <div class="row">
            <div class="col-lg-10">
                <div class="main-box" style="min-height:600px">
                    <header class="main-box-header clearfix">
                        <h2>账户角色绑定</h2>
                    </header>
                    @include('backend.layout.alert_info')
                    <div class="main-box-body clearfix">
                        <form role="form" id='select_user' action="{{asset('backend/role/user_find_roles')}}" method="post">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label>选择账户</label>
                                <select id="user_select" class="form-control" name="role_id">
                                    <option value="0" selected disabled>--请选择账户--</option>
                                    @foreach($users as $rk => $rv)
                                        <option value="{{$rv->id}}" @if(!empty($user) && $user->id == $rv->id)selected @endif>{{$rv->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                        <form role="form" id="bind" action="{{asset('backend/role/attach_roles')}}" method="post">
                            {{ csrf_field() }}
                            <label><h4>关联角色</h4></label>
                            @if(!empty($user))
                                <label for="" style="margin-left: 20px;">
                                    <button type="button" id="attach_submit" class="btn btn-success">提交绑定关系</button>
                                </label>
                            <div class="form-group">
                                    <input type="hidden" name="check_user_id" value="{{$user->id}}">
                                    @foreach($roles as $pk => $pv)
                                        <div class="checkbox-nice" style="float:left;margin-left: 20px;">
                                            <input id="checkbox-{{$pk}}" name="role_ids[]" @if(!empty($user_role_ids) && in_array($pv->id, $user_role_ids)) checked @endif
                                            type="checkbox" value="{{$pv->id}}">
                                            <label for="checkbox-{{$pk}}">
                                                {{$pv->name}}----{{$pv->display_name}}
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
        $('#user_select').change(function(){
            var role_id = $(this).val();
            if(role_id != 0){
                $('#select_user').submit();
            }
        });
        $("#attach_submit").click(function(){
            $('#bind').submit();
        })
    </script>
@stop

