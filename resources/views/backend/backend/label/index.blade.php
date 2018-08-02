@extends('backend.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('r_backend/css/libs/nifty-component.css')}}"/>
@endsection
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2 class="pull-left">分类列表</h2>
                    <div class="filter-block pull-right" style="margin-right: 20px;">
                        <button class="md-trigger btn btn-primary mrg-b-lg" data-modal="modal-8">新建主分类</button>
                    </div>
                </header>
                @include('backend.layout.alert_info')
                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        <table class="table user-list table-hover">
                            <thead>
                            <tr>
                                <th></th>
                                <th><span>分类名称</span></th>
                                <th><span>唯一标识</span></th>
                                <th class="text-center">父类ID</th>
                                <th class="text-center">关系结构</th>
                                <th class="text-center"><span>层级</span></th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($categories as $k => $v)
                                <tr class="sort-{{ $v->sort }}">
                                    <td class="id">{{$v->id}}</td>
                                    <td class="name">
                                        <?php echo str_repeat('|—————' , $v['sort']) . $v['name'] ?>
                                    </td>
                                    <td>{{$v->slug}}</td>
                                    <td class="text-center pid">
                                        {{$v->pid}}
                                    </td>
                                    <td class="text-center">
                                        {{$v->path}}
                                    </td>
                                    <td class="text-center">
                                        {{$v->sort}}
                                    </td>

                                    <td style="width: 15%;">
                                        <a href="#" class="btn md-trigger add_son" data-modal="modal-9" id="{{$v['id']}}" name="{{$v['name']}}">
                                            <i class="fa fa-plus-circle fa-lg"></i>
                                        </a>
                                        <a href="#" class="table-link alter md-trigger" data-modal="modal-10" id="{{$v['id']}}" name="{{$v['name']}}" slug="{{$v->slug}}">
                                        <span class="fa-stack">
                                            <i class="fa fa-square fa-stack-2x"></i>
                                            <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                                        </span>
                                        </a>
                                        <a href="category/omit?id={{$v['id']}}" class="table-link danger">
                                        <span class="fa-stack">
                                            <i class="fa fa-square fa-stack-2x"></i>
                                            <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                        </span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                    

                </div>
            </div>
        </div>

        <div class="md-modal md-effect-8 md-hide" id="modal-8">
            <div class="md-content">
                <div class="modal-header">
                    <button class="md-close close">×</button>
                    <h4 class="modal-title">主分类添加</h4>
                </div>
                <div class="modal-body">
                    <form role="form" id="add_category" action='{{url('backend/product/category/add')}}' method="post">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="exampleInputEmail1">分类名称</label>
                            <input class="form-control" name="name" placeholder="保险公司名称（5-50个字符长度）" type="text">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">唯一标识</label>
                            <input class="form-control" name="slug" placeholder="唯一标识缩略名（2-50个字符长度）" type="text">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="form-submit" class="btn btn-primary">确认提交</button>
                </div>
            </div>
        </div>

        <div class="md-modal md-effect-8 md-hide" id="modal-9">
            <div class="md-content">
                <div class="modal-header">
                    <button class="md-close close">×</button>
                    <h4 class="modal-title">子分类添加</h4>
                </div>
                <div class="modal-body">
                    <form role="form" id="add_son_category" action='{{url('backend/product/category/add')}}' method="post">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="exampleInputEmail1">父级分类名称</label>
                            <input id="p_category_name" class="form-control" placeholder="保险公司名称（5-50个字符长度）" type="text" disabled>
                        </div>
                        <input id="p_category_id" type="hidden" name="pid">
                        <div class="form-group">
                            <label for="exampleInputEmail1">分类名称</label>
                            <input class="form-control" name="name" placeholder="保险公司名称（5-50个字符长度）" type="text">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">唯一标识</label>
                            <input class="form-control" name="slug" placeholder="唯一标识缩略名（2-50个字符长度）" type="text">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="form-son-submit" class="btn btn-primary">确认提交</button>
                </div>
            </div>
        </div>
        <div class="md-overlay"></div>
    </div>

    <div class="md-modal md-effect-8 md-hide" id="modal-10">
        <div class="md-content">
            <div class="modal-header">
                <button class="md-close close">×</button>
                <h4 class="modal-title">分类的修改</h4>
            </div>
            <div class="modal-body">
                <form role="form" id="alter_category" action='{{url('backend/product/category/alter')}}' method="get">
                    {{ csrf_field() }} 
                    <div class="form-group">
                        <label for="exampleInputEmail1">分类名称</label>
                        <input id="alter_category_name" class="form-control" name="name" placeholder="保险公司名称（5-50个字符长度）" type="text">
                        <input id="alter_category_id" type="hidden" name="pid">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">唯一标识</label>
                        <input class="form-control" id="alter_slug" name="slug" placeholder="唯一标识缩略名（2-50个字符长度）" type="text">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="alter-submit" class="btn btn-primary">确认提交</button>
            </div>
        </div>
    </div>
    <div class="md-overlay"></div>
</div>

@stop
@section('foot-js')
    <script charset="utf-8" src="/r_backend/js/modernizr.custom.js"></script>
    <script charset="utf-8" src="/r_backend/js/classie.js"></script>
    <script charset="utf-8" src="/r_backend/js/modalEffects.js"></script>
    <script>
        $(function(){
            $submit = $("#-submit");
            $submit.click(function(){
                var $name = $("input[name=name]").val();
                var $name_role = /^[（）\u4e00-\u9fa5a-zA-Z\s\-_()]{4,50}$/;
                var $name_check = $name_role.test($name);
                changeClass($("input[name=name]"), $name_check);

                var $slug = $("input[name=slug]").val();
                var $slug_role = /^[（）\u4e00-\u9fa5a-zA-Z\s\-_()]{2,50}$/;
                var $slug_check = $slug_role.test($slug);
                changeClass($("input[name=slug]"), $slug_check);


                function changeClass(obj, result){
                    if(!result){
                        obj.val('');
                        obj.parent().addClass("has-error");
                    }else{
                        obj.parent().removeClass("has-error");
                    }
                }

                if($name_check && $slug){
                    $("#add_category").submit();
                }else{
                    alert("请根据输入框提示填写相关内容");
                }

            })


            $(".add_son").click(function(){
                var pid = $(this).attr('id');
                var name = $(this).attr('name');
                $("#p_category_name").val(name);
                $("#p_category_id").val(pid);
            })

            $("#form-son-submit").click(function(){
                $("#add_son_category").submit();
            });

            // 多级分类展示
            $("tr").hide();
            $("tr.sort-0").show();
            $("tr").on("click", function () {
                var $sort = get_sort($(this));
                var $next = parseInt($sort) + 1;
                if (!$(this).prop("checked")) {
                    $(this).nextUntil("tr.sort-"+$sort).filter("tr.sort-"+$next).show();
                    $(this).find("td.name i").removeClass("fa-caret-right").addClass("fa-caret-down");
                    $(this).prop("checked", true);
                } else {
                    $(this).nextUntil("tr.sort-"+$sort).hide();
                    $(this).find("td.name i").removeClass("fa-caret-down").addClass("fa-caret-right");
                    $(this).prop("checked", false);
                }
            });

            $("tr").each(function () {
                var $current_id = trim($(this).children("td.id").text());
                var $next_pid = trim($(this).next().children("td.pid").text());
                if ($current_id == $next_pid) {
                    $(this).children("td.name").append('<i class="fa fa-caret-right"></i>');
                }
            });
        })


        // 修改  
        $(function(){
            $submit = $("#-submit");
            $submit.click(function(){
                var $name = $("input[name=name]").val();
                var $name_role = /^[（）\u4e00-\u9fa5a-zA-Z\s\-_()]{4,50}$/;
                var $name_check = $name_role.test($name);
                changeClass($("input[name=name]"), $name_check);

                var $slug = $("input[name=slug]").val();
                var $slug_role = /^[（）\u4e00-\u9fa5a-zA-Z\s\-_()]{2,50}$/;
                var $slug_check = $slug_role.test($slug);
                changeClass($("input[name=slug]"), $slug_check);

                function changeClass(obj, result){
                    if(!result){
                        obj.val('');
                        obj.parent().addClass("has-error");
                    }else{
                        obj.parent().removeClass("has-error");
                    }
                }

                if($name_check && $slug){
                    $("#alter_category").submit();
                }else{
                    alert("请根据输入框提示填写相关内容");
                }

            })

            $(".alter").click(function(){
                var pid = $(this).attr('id');
                var name = $(this).attr('name');
                var slug = $(this).attr('slug');
                $("#alter_category_name").val(name);
                $("#alter_category_id").val(pid);
                $("#alter_slug").val(slug);
            })

            $("#alter-submit").click(function(){
                $("#alter_category").submit();
            });
        })

        function get_sort(obj) {
            return obj.attr("class").split("-")[1];
        }

        function trim(str) {
            return str.replace(/(^\s*)|(\s*$)/g, "");
        }
    </script>
@stop