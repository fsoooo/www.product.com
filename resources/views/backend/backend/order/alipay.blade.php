@extends('backend.layout.base')
@section('content')
<a onclick="doReply()"><img src="http://ty.laravel2.com/image/alipay.png"/></a>
<div id="checkreply"></div>




<script type="application/javascript">
    function doReply(){
        alert('支付成功');


        var order = "{{$data}}";
        var company = "{{$company}}";
        var status = '1';
//        var params  = {status:status,order:order};
//        console.log(order);

        $.ajax({
            url:'http://ty.laravel2.com/backend/doreply?order='+order+'&status='+status,
            type:'get',
            dataType:'json',

            success:function(msg){
                if(msg.status == 0){
//                    alert(msg.message);
//                    window.history.go(-1);  //返回发短信的一页
//                    location.reload();
                    window.location.href="http://www.product.com/backend/dopay?type=sms&id="+company;
//                    window.history.back();
//                    $('#checkreply').html('<font color="green">'+msg.message+'</font>');
                }else{
                    alert(msg.message);
                    $('#checkreply').html('<font color="red">'+msg.message+'</font>');
                }
            }

        },'JSON');
    }
@stop