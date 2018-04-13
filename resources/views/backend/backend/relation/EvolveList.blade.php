@extends('backend.layout.base')
@section('content')
    <meta name="_token" content="{{ csrf_token() }}"/>
    <style>
    </style>
    <div id="content-wrapper">
        <div class="big-img" style="display: none;">
            <img src="" alt="" id="big-img" style="width: 75%;height: 90%;">
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <ol class="breadcrumb">
                            <li><a href="{{ url('/backend') }}">主页</a></li>
                            <li><span>客户管理</span></li>
                            <li class="active"><span>客户池</span></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="{{ url('backend/relation/cust/all') }}">客户池</a></li>
                                    <li><a href="{{ url('backend/relation/cust/person') }}">个人客户</a></li>
                                    <li><a href="{{ url('backend/relation/cust/company') }}">企业客户</a></li>
                                    {{--<li class="active"><a href="">联系记录</a></li>--}}
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        <h3><p>客户 {{ $name }} 的联系记录</p></h3>
                                        @include('backend.layout.alert_info')
                                        <div class="panel-group accordion" id="operation">
                                            <div class="panel panel-default">
                                                {{ csrf_field() }}
                                                <table id="user" class="table table-hover" style="clear: both">
                                                    <div class="tabs-wrapper profile-tabs">
                                                        <div class="tab-content">
                                                            <div class="tab-pane fade in active" id="tab-newsfeed">
                                                                @if($count == 0)
                                                                    <div id="newsfeed">
                                                                        暂无联系记录
                                                                    </div>
                                                                @else
                                                                    @foreach($list as $value)
                                                                    <div id="newsfeed">
                                                                        <div class="story">
                                                                        <div class="story-content" style="padding-left: 0">
                                                                            <header class="story-header">
                                                                                <div class="story-author" style="font-size: 15px;">
                                                                                    <span href="#" class="story-author-link">
                                                                                        联系人 : {{ $value->evolve_person }}
                                                                                    </span>&nbsp&nbsp&nbsp&nbsp
                                                                                    <span href="#" class="story-author-link">
                                                                                         联系方式 : {{ $value->evolve_way }}
                                                                                    </span>
                                                                                </div>
                                                                                <div class="story-time">
                                                                                    <i class="fa fa-clock-o"></i> {{ $value->created_at }}
                                                                                </div>
                                                                            </header>
                                                                            <div class="story-inner-content" style="font-size: 15px;padding-top: 20px">
                                                                                修改状态 : {{ $value->status_name }}
                                                                            </div>
                                                                            <div class="story-inner-content" style="font-size: 15px;">
                                                                                备注信息 : {{ $value->remarks }}
                                                                            </div>
                                                                            <footer class="story-footer" style="font-size: 15px;">
                                                                                {{--<a href="#" class="story-comments-link">--}}
                                                                                    {{--<button type="button" class="btn btn-primary" data-action="expand-all">确认状态修改</button>--}}
                                                                                {{--</a>--}}
                                                                                <a href="#" class="story-comments-link" style="float: right;">
                                                                                    代理人 : {{ $value->agent_name }}
                                                                                </a>
                                                                            </footer>
                                                                        </div>
                                                                    </div>
                                                                    @endforeach
                                                                @endif
                                                                </div>
                                                                        {{ $list->links() }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </table>
                                                </form>
                                            </div>
                                            {{--@if($controller == 'apply')--}}
                                            {{--<button id="btn" class="btn btn-success">申请</button>--}}
                                            {{--@elseif($controller == 'edit')--}}
                                            {{--<button id="btn" class="btn btn-success">修改</button>--}}
                                            {{--@elseif($controller == 'add')--}}
                                            {{--<button id="btn" class="btn btn-success">添加</button>--}}
                                            {{--@endif--}}
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
    </div>

    <script src="/js/jquery-3.1.1.min.js"></script>
    <script>


        $(function(){

            //进行验证发送
            var name = $('input[name=name]');
            var code = $('input[name=code]');
            var btn = $('#btn');
            var form = $('#form');
            btn.click(function(){
                var name_val = name.val();
                var code_val = code.val();
                if(name_val == ''){
                    name.parent().addClass("has-error");
                    alert('名称不能为空');
                    return false;
                }else{
                    name.parent().removeClass("has-error");
                    console.log(name_val);
                    if(code_val == ''){
                        code.parent().addClass("has-error");
                        alert('证件号不为空');
                        return false;
                    }else {
                        code.parent().removeClass("has-error");
                    }
                }
//                console.log(form.serialize());
                form.submit();
            })


        })

    </script>
@stop

