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
                                        <li class="active"><span>产品列表</span></li>
                                    </ol>

                                    <header id="email-header" class="clearfix">
                                        <div id="email-header-tools">
                                            <div class="btn-group">
                                                <input type="checkbox" name="allChecked" id="allChecked" onclick="DoCheck()"/>全选/取消
                                                <script type="text/javascript">
                                                                function DoCheck()
                                                                {
                                                                var ch=document.getElementsByName("choose");
                                                                if(document.getElementsByName("allChecked")[0].checked==true)
                                                                {
                                                                for(var i=0;i<ch.length;i++)
                                                                {
                                                                ch[i].checked=true;
                                                                }
                                                                }else{
                                                                for(var i=0;i<ch.length;i++)
                                                                {
                                                                ch[i].checked=false;
                                                                }
                                                                }
                                                                }

                                                                function refresh(){
                                                                    window.location.href=location;
                                                                }

                                                            </script>
                                            </div>
                                            <div class="btn-group">
                                                <a onclick="refresh()"><button class="btn btn-primary" type="button" title="Refresh"  >
                                                    <i class="fa fa-refresh"></i>
                                                </button></a>
                                             
                                                <a onclick="doSelectedBack()"><button class="btn btn-primary" type="button" title="选择还原" data-toggle="tooltip" data-placement="bottom">
                                                        <i class="fa fa-circle"></i>
                                                </button></a>
                                                 <a href="productlist"><button class="btn btn-primary" type="button" title="产品列表" data-toggle="tooltip" data-placement="bottom">
                                                        <i class="fa"></i>
                                                </button></a>
                                                

                                            </div>
                                                    <span id="check"></span>        
                                            <div class="btn-group">
                                                <button data-toggle="dropdown" class="btn btn-primary dropdown-toggle has-tooltip" type="button" title="产品状态">
                                                    <i class="fa fa-tag"></i> <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a href="#"><i class="fa fa-circle green"></i> 已上架</a></li>
                                                    <li><a href="#"><i class="fa fa-circle red"></i> 上架</a></li>
                                                    <!-- <li><a href="#"><i class="fa fa-circle yellow"></i> Personal</a></li>
                                                    <li><a href="#"><i class="fa fa-circle purple"></i> Documents</a></li> -->
                                                </ul>
                                            </div>
                                        </div>
                                        <div id="email-header-pagination" class="pull-right">
                                            <div class="btn-group pagination pull-right">
                                                <button class="btn btn-primary" type="button" title="Previous" data-toggle="tooltip" data-placement="bottom">
                                                    <i class="fa fa-chevron-left"></i>
                                                </button>
                                                <button class="btn btn-primary" type="button" title="Next" data-toggle="tooltip" data-placement="bottom">
                                                    <i class="fa fa-chevron-right"></i>
                                                </button>
                                            </div>
                                            <div class="num-items pull-right hidden-xs">
                                                1-50 from 5,912
                                            </div>
                                        </div>
                                    </header>
                                </div>
                            </div>
                            <script type="text/javascript">
                                                function refresh(){
                                                                    window.location.href=location;
                                                                }
                                                
                                               
                                                                    function doSelectedBack(){  
                                                                    var choose = document.getElementsByName("choose");  
                                                                    var num = choose.length;  
                                                                    var id = "";  
                                                                    for(var index =0 ; index<num ; index++){  
                                                                        if(choose[index].checked){  
                                                                            id += choose[index].value + ",";                
                                                                        }  
                                                                    }  
                                                                    if(id!=""){  
                                                                        if(window.confirm("确定还原所选产品？")){ 
                                                                        // alert(1);
                                                                        console.log(id); 
                                                                            $.ajax( {  
                                                                                type : "get",  
                                                                                url : 'productback?id=' + id,
                                                                                dataType : 'json',  
                                                                               success:function(msg){
                                                                                if(msg.status == 1){
                                                                                  alert(msg.message);
                                                                                  $('#check').html('<font color="red">'+msg.message+'</font>');
                                                                                }else{
                                                                                    alert(msg.message);
                                                                                  // $('#check').html('<font color="green">'+msg.message+'</font>');
                                                                                 window.location.href=location;
                                                                                }
                                                                                
                                                                              }
                                                                            });  
                                                                        }  
                                                                    }else{  
                                                                        alert("请选择要还原的商品");  
                                                                        }  
                                                                    }  
                                             </script>
                            <div class="row">
                                <div class="col-lg-12">
                                    
                                    <div id="email-content" class="email-content" style="height:830px">
                                        <div class="email-content-nano-content">
                                            <ul id="email-list">

                                               @foreach($res as $key => $mail)
                                               
                                                <li class="unread clickable-row">
                                                    <div class="chbox">
                                                     
                                                            <input type="checkbox" name="choose" value="{{$mail['id']}}" />

                                                           
                                                       
                                                    </div>
                                                      <a href="productinfo?id={{$mail['id']}}" title="查看详情和修改">
                                                    <div class="name">
                                                           </div>
                                                          产品ID：{{$mail['id']}}-------
                                                          名称：{{$mail['name']}}------
                                                          公司ID：{{$mail['company_id']}}------
                                                          险种：{{$mail['display_name']}}
                                                          标签：{{$mail['type']}}
                                                          产品上架时间：{{$mail['created_at']}}

                                                                                                             
                                                        
                                                   
                                                    <!--  -->
                                                    <div class="meta-info">
                                                        <a href="" class="attachment">
                                                            <i class="fa fa-paperclip"></i>
                                                        </a>
                                                       <!--  -->
                                                    </div>
                                                    </a>
                                                </li>

                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>




                <footer id="footer-bar" class="row hidden-md hidden-lg">
                    <p id="footer-copyright" class="col-xs-12">
                        &copy; 2014 <a href="http://www.adbee.sk/" target="_blank">Adbee digital</a>. Powered by Centaurus Theme.
                    </p>
                </footer>
            </div>
         @stop