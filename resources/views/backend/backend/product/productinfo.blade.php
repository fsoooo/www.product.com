@extends('backend.layout.base')
@section('content')
<div id="content-wrapper" class="email-inbox-wrapper">
<div class="row">
<div class="col-lg-12">
<div id="email-box" class="clearfix">
<div class="row">
<div class="col-lg-12">
    <ol class="breadcrumb">
        <li><a href="{{ url('/backend') }}">主页</a></li>
        <li ><span>产品管理</span></li>
        <li><span><a href="/backend/product/productlist">产品列表</a></span></li>
        <li class="active"><span>我的产品详情</span></li>
    </ol>

    <div id="email-detail" class="email-detail-nano" style="height: 830px">
<div class="email-detail-nano-content">
<div id="email-detail-inner">
    <div id="email-detail-subject" class="clearfix">
        @include('backend.layout.alert_info')
        <div id="email-detail-subject" class="clearfix">
            <span class="subject">
                <img  src="{{config('curl_product.company_logo_url')}}{{$json['company']['logo']}}", style="height: 40px;width:80px;">
                保险公司:<a href="{{$json['company']['url']}}">{{$json['company']['name']}}</a></span>
            <span class="subject">保险公司Email:{{$json['company']['email']}}</span>
            <span class="subject">保险公司编号:{{$json['company']['code']}}</span>
            {{--<span id="check"></span>--}}
        </div>
    </div>
    <div id="email-detail-sender" class="clearfix">
        <div id="email-detail-sender" class="clearfix">
            <div class="users">
                <div class="from clearfix">
                    <span> <a onclick="add()">产品上架</a></span>

                    @if($status==1)
                        <span class="label label-success">已上架</span>
                    @elseif($status==0)
                        <span class="label label-danger">未上架</span>
                    @endif
                    <span id="check"></span>
                    <div class="name">
                      <h1>
                            @if(isset($json['cover']))
                              <img src="/{{$json['cover']}}" style="height: 40px;width: 75px">
                            @endif
                          产品名称：{{$json['name']}}</h1>
                    </div>

                </div>
            </div>
            <div class="tools">
                <div class="date">
                    产品发布时间：{{$json['created_at']}}
                </div>
            </div>
        </div>
        <div id="email-body">
            <ul>
                <li>产品ID：{{$json['id']}}</li>
                <li>产品全称：{{$json['display_name']}}</li>
                <li>产品唯一码：{{$json['p_code']}}</li>
                <li>产品介绍：{{$json['content']}}</li>
                <li>险种：{{$json['category']['name']}}</li>
                <li>产品佣金比：{{$json['brokerage']}}</li>
                <li>产品来源：{{$json['api_from_uuid']}}</li>
            </ul>
            <div style="font-family: 黑体;font-size: 18px;">
                @if(!empty($labels))标签：
                @foreach($labels as $label)
                    <h6><b>{{$label['name']}}</b></h6>
                @endforeach
                @endif
            </div>
        </div>

        <div class="users">
            <div class="from clearfix">

            <div class="email hidden-xs">

                <script type="text/javascript">
                    function add(){
                        var id = "{{$json['id']}}";
                         $.ajax( {
                            type : "get",
                            url : 'productup?id=' + id,
                            dataType : 'json',
                            success:function(msg){
                            if(msg.status == 1){
                            $('#check').html('<font color="red">'+msg.message+'</font>');
                            }else{
                            alert(msg.message);
                            window.location.href=location;
                            // $('#check').html('<font color="green">'+msg.messages-listage+'</font>');
                              }

                             }
                        });
                    }
                </script>
</div>
</div>

        </div>
        <form action="{{ url('backend/product/productpersonal') }}" method="post" id="p-form" enctype="multipart/form-data">
            <input type="hidden" name="product_id" value="{{$json['id']}}">
            {{ csrf_field() }}
            <div class="ge_pic_icon_Infor">
                <img src="/view_1/image/show_logo.jpg" style="height: 100px;width: 150px"/>
                <a onclick="dofile()">封面上传</a>
            </div>
            <div>
                @if(isset($json['cover']))
                    <input type="hidden" name="file" value="{{$json['cover']}}">
                @endif
                <input type="file" name="file" id="file"  onchange="getPhoto(this)" style="display: none"/>
            </div>

        <div id="email-body">
            <p><h2>个性化定制</h2>
                @if(empty($object['personal']))
                <textarea name="data" id="myEditor" cols="150" rows="3"></textarea>
                @else
                <textarea name="data" id="myEditor" cols="150" rows="3">{{$object['personal']}}</textarea>
                @endif
                <script type="text/javascript">
                    var um = UM.getEditor('myEditor');
                    function createEditor() {
                        enableBtn();
                        um = UM.getEditor('myEditor');
                    }
                </script>
            <br/>
                {{--<button onclick="doText()" style="background:rgb(52,152,219);border: none;color:white;height:35px;width:100px">保存改变</button>--}}
                {{--<script type="text/javascript">--}}
                    {{--function doText() {--}}
                        {{--var um = UM.getEditor('myEditor');--}}
                        {{--var data = um.getContent();--}}
                        {{--var product_id =  "{{$json['id']}}";--}}
                        {{--var params = {data:data,product_id:product_id};--}}
                        {{--$.ajax({--}}
                            {{--type : "post",--}}
                            {{--url : 'productpersonal',--}}
                            {{--dataType : 'json',--}}
                            {{--data:params,--}}
                            {{--headers: {--}}
                                {{--'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
                            {{--},--}}
                            {{--success:function(msg){--}}
                            {{--if(msg.status == 1){--}}
                                {{--alert(msg.message);--}}
                                {{--$('#check').html('<font color="red">'+msg.message+'</font>');--}}
                            {{--}else{--}}
                                {{--alert(msg.message);--}}
                                {{--window.location.href=location;--}}
                            {{--}--}}

                        {{--}--}}
                    {{--});--}}


                    {{--}--}}
                {{--</script>--}}
            </p>
            <button id="send-message-btn" class="btn btn-success">保存改变</button>
        </div>
        </form>
        <div id="email-detail-attachments">
            <div id="email-attachments-header" class="clearfix">
                    <div class="headline">

                        @foreach($claus as $v)


                            <div class="headline">
                                <i class="fa fa-paperclip"></i>
                                <span>条款:</span>
                                {{--@foreach($v as $value)--}}
                                <ul>
                                    <li>名称{{$v['name']}}</li>
                                    <li>条款全称{{$v['display_name']}}</li>
                                    <li>条款类型{{$v['type']}}</li>
                                    <li><a href="{{config('curl_product.company_logo_url')}}{{$v['file_url']}}">文件地址 </a></li>
                                    <li>条款保额{{$v['coverage']}}</li>
                                    <li>创建时间{{$v['created_at']}}</li>
                                </ul>
                                @if(!empty($v['duties']))
                                    <table>
                                        <tr>
                                            <th>责任名称</th>
                                            <th>责任描述</th>
                                            <th>责任细节</th>
                                            <th>责任类型</th>
                                            <th>创建时间</th>
                                        </tr>
                                        @foreach($v['duties'] as $duty)
                                            <tr>
                                                <td>{{$duty['name']}}</td>
                                                <td>{{$duty['description']}}</td>
                                                <td>{{$duty['detail']}}</td>
                                                <td>{{$duty['type']}}</td>
                                                <td>{{$duty['created_at']}}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                @endif
                                @if(!empty($v['tariff']))
                                    <div style="height: 250px;overflow-y: scroll;">
                                    <table>
                                        <tr>
                                            <th>费率</th>
                                            <th>年龄</th>
                                            <th>性别</th>
                                            <th>缴费方式</th>
                                            <th>缴费期间</th>
                                            <th><有无社保></有无社保></th>
                                        </tr>
                                        @foreach($v['tariff'] as $tariff)
                                            <tr >
                                                <td>{{$tariff['tariff']}}</td>
                                                <td>{{$tariff['age']}}</td>
                                                <td>{{$tariff['sex']}}</td>
                                                <td>{{$tariff['period']}}</td>
                                                <td>{{$tariff['by_stages']}}</td>
                                                <td>{{$tariff['shebao']}}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                    </div>
                                @endif

                            </div>

                            @endforeach
</li>
</ul>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<footer id="footer-bar" class="row hidden-md hidden-lg">
<p id="footer-copyright" class="col-xs-12">
&copy; Powered by Centaurus Theme.
</p>
</footer>
</div>


    <script>
        var imgurl = "";
        function getPhoto(node) {
            var imgURL = "";
            try{
                var file = null;
                if(node.files && node.files[0] ){
                    file = node.files[0];
                }else if(node.files && node.files.item(0)) {
                    file = node.files.item(0);
                }
                //Firefox 因安全性问题已无法直接通过input[file].value 获取完整的文件路径
                try{
                    imgURL =  file.getAsDataURL();
                }catch(e){
                    imgRUL = window.URL.createObjectURL(file);
                }
            }catch(e){
                if (node.files && node.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        imgURL = e.target.result;
                    };
                    reader.readAsDataURL(node.files[0]);
                }
            }
            creatImg(imgRUL);
            return imgURL;
        }

        function creatImg(imgRUL){
            var textHtml = "<img src='"+imgRUL+"'width='150px' height='100px'/>";
            $(".ge_pic_icon_Infor").html(textHtml);
        }
        function dofile(){
            return  $("#file").click();
        }
    </script>
@stop