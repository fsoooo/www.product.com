@extends('backend.layout.base')
@section('css')

@endsection
@section('content')
    <div id="content-wrapper">
        <div class="row">
            <div class="col-lg-10">
                <div class="main-box" style="min-height:600px">
                    <header class="main-box-header clearfix">
                        <h2>角色权限绑定</h2>
                    </header>
                    @include('backend.layout.alert_info')
                    <div class="main-box-body clearfix">
                        <form role="form" id='select_role' action="{{asset('backend/role/role_find_permissions')}}" method="post">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label>选择角色</label>
                                <select id="role_select" class="form-control" name="role_id">
                                    <option value="0" selected disabled>--请选择角色--</option>
                                    @foreach($roles as $rk => $rv)
                                        <option value="{{$rv->id}}" @if(!empty($role) && $role->id == $rv->id)selected @endif>{{$rv->name}}----{{$rv->display_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                        <form role="form" id="bind" action="{{asset('backend/role/attach_permissions')}}" method="post">
                            {{ csrf_field() }}
                            <label><h4>关联权限</h4></label>
                            @if(!empty($role))
                                <label for="" style="margin-left: 20px;">
                                    <button type="button" id="attach_submit" class="btn btn-success">提交绑定关系</button>
                                </label>
                            <div class="form-group">
                                    <input type="hidden" name="check_role_id" value="{{$role->id}}">
                                    @foreach($permissions as $pk => $pv)
                                        <div class="checkbox-nice" style="float:left;margin-left: 20px;">
                                            <input id="checkbox-{{$pk}}" name="permission_ids[]" @if(!empty($role_permission_ids) && in_array($pv->id, $role_permission_ids)) checked @endif
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

