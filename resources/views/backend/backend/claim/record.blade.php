@extends('backend.layout.base')
@section('content')
    <meta name="_token" content="{{ csrf_token() }}"/>
    <style>
        th,td{
            text-align: center;
        }
    </style>
    <div id="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <ol class="breadcrumb">
                            <li><a href="{{ url('/backend') }}">主页</a></li>
                            <li ><span>售后管理</span></li>
                            <li class="active"><span>理赔管理</span></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    <li><a href="{{ url('/backend/claim/get_detail/'.$cid) }}">详细信息</a></li>
                                    <li><a href="{{ url('/backend/claim/operation/'.$cid) }}">操作</a></li>
                                    <li class="active"><a href="{{ url('/backend/claim/get_record/'.$cid) }}" >操作记录</a></li>
                                </ul>
                                <div class="tab-content">
                                    @include('backend.layout.alert_info')
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        <h3><p>操作记录</p></h3>
                                        <div class="panel-group accordion" id="account">
                                            <div class="panel panel-default">
                                                <table id="user" class="table table-hover" style="clear: both">
                                                    <thead>
                                                    <tr>
                                                        <th><span>操作人</span></th>
                                                        <th><span>操作时间</span></th>
                                                        <th><span>修改状态</span></th>
                                                        <th><span>操作说明</span></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @if( $count == 0 )
                                                        <tr>
                                                            <td colspan="4" style="text-align: center;">暂无联系记录</td>
                                                        </tr>
                                                    @else
                                                        @foreach ( $record as $value )
                                                            <tr>
                                                                <td>{{ $value->operation_name }}</td>
                                                                <td>{{ $value->created_at }}</td>
                                                                <td>{{ $value->status_name }}</td>
                                                                <td>{{ $value->claim_remarks }}</td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                    </tbody>
                                                </table>
                                                {{ $record->links() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <footer id="footer-bar" class="row">
                <p id="footer-copyright" class="col-xs-12">
                    &copy; 2014 <a href="http://www.adbee.sk/" target="_blank">Adbee digital</a>. Powered by Centaurus Theme.
                </p>
            </footer>
        </div>
    </div>





    <script src="/js/jquery-3.1.1.min.js"></script>
    <script>
//        var first_operation = $('#first-operation');
//        var second_operation = $('#second-operation');
//
//        first_operation.change(function() {
//            //获得选择的子分类的id
//            var fid=$('#first-operation option:selected').val();
//            $.ajax({
//                type: "post",
//                dataType: "json",
//                async: true,
//                //修改的地址，
//                url: "/backend/classify/getClassify",
//                data: 'fid='+fid,
//                headers: {
//                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
//                },
//            success: function(data){
//                console.log(data[0].status_name);
//                console.log(data)
////                console.log(data);
////                if(data["status"]=="200"){
////                    alert("发送成功");
////                    window.location.href="__CONTROLLER__/";
////                }else {
////                    alert("发送失败");
////                }
//            },error: function () {
//                alert("发送失败");
//            }
//            })
//        })
        var form = $('#operation-form');
        var operation = $('#operation');
        var remarks = $('input[name=remarks]');
        var claim_status = $('#claim-status');
        var save_operation = $('#save-operation');
        save_operation.click(function(){
            //获取当前状态并进行判断
            var remarksval = remarks.val();
            var status = claim_status.attr('index');
            var status_change = $('#operation option:selected').val();
            if(status == ''){
                alert('请选择状态');
                return false;
            }else if(status == status_change){
                alert('状态和原状态相同，没有改变！')
                return false;
            }else if(remarksval == ''){
                alert('请输入备注说明');
                return false;
            }else{
                //请求通过，开始传递ajax
                var formval = form.serialize();
                $.ajax({
                    type: "post",
                    dataType: "json",
                    async: true,
                    //修改的地址，
                    url: "/backend/claim/addRecord",
                    data: formval,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    success: function(data){
                        if(data){
                            alert('操作成功');
                            window.location.reload();
                        }else{
                            alert('添加失败');
                        }
                    },error: function () {
                        alert("添加失败");
                    }
                });
            }
            console.log(status_change);
        })


    </script>
@stop

