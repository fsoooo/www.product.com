@extends('backend.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('r_backend/css/libs/nifty-component.css')}}"/>
@endsection
@section('content')
    <div id="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <ol class="breadcrumb">
                            <li><a href="{{ url('/backend') }}">主页</a></li>
                            <li ><span>运营管理</span></li>
                            <li class="active"><span>需求管理</span></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    <li><a href="{{ url('/backend/demand/index/user') }}">客户发布</a></li>
                                    <li><a href="{{ url('/backend/demand/index/agent') }}">代理人发布</a></li>
                                    <li class="active"><a href="#">需求详情</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        <h3  class="pull-left"><p>账户信息</p></h3>

                                     <div class="filter-block" style="margin-right: 20px;">
                                </div>
                            </div>
                            <header class="main-box-header clearfix">

                            </header>
                                    @include('backend.layout.alert_info')
                            <div class="main-box-body">
                                <div class="table-responsive">
                                    <form action="/backend/demand/offer_submit" method="post" id="offer-form">
                                        {{ csrf_field() }}
                                        <input type="text" name="demand_id" value="{{ $demand_id }}" hidden>
                                        <table class="table user-list table-hover" id="offer-table">
                                            <tr>
                                                <td>产品</td>
                                                <td id="product-display">
                                                 </td>
                                            </tr>
                                            <tr>
                                                <td>报价</td>
                                                <td><input type="text" name="offer" class="form-control"></td>
                                            </tr>
                                        </table>
                                    </form>
                                    <tr>
                                        <td><a href=""></a><button class="md-trigger btn btn-primary mrg-b-lg pull-right" data-modal="modal-8">添加产品</button></td>
                                        <td><button id="offer-btn">报价</button></td>
                                    </tr>
                                </div>
                            </div>
                        </div>
                    </div>

        <div class="md-modal md-effect-8 md-hide" id="modal-8">
            <div class="md-content">
                <div class="modal-header">
                    <button class="md-close close">×</button>
                    <h4 class="modal-title">代理人添加</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="exampleInputEmail1">选择产品<span class="red">*</span></label>
                        <select name="product_id" id="product-id" class="form-control">
                            <option value="0">选择产品</option>
                            @foreach($product_list as $value)
                                <option value="{{ $value->id }}">{{ $value->product_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="form-submit" class="btn btn-primary add-product">确认</button>
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
        $(function(){console.log('sdf');
            var add_product = $('.add-product');
            var product_id = $('#product-id');
            var product_display = $('#product-display');
            var array = [];
            var offer_form = $('#offer-form');
            var offer_btn = $('#offer-btn');

            add_product.click(function()
            {
                var product_id_val = $('#product-id option:selected').val();console.log(product_id_val);console.log($.inArray(product_id_val,array));
                var product_name_val = $('#product-id option:selected').html();
                //判断是否已经添加过产品了
                if(product_id_val == 0){
                    return false;
                }
                if($.inArray(product_id_val,array) > -1){
                    alert('已经添加过了');
                }else{
                    array.push(product_id_val);//添加到判断数组中
                    var product_p = '<p>'+product_name_val+'</p>';
                    product_display.append(product_p);
                    var product_input =  '<input type="text" name="'+product_id_val+'" value="'+product_id_val+'" hidden>';
                    product_display.append(product_input);
                }

            })

            offer_btn.click(function(){
                offer_form.submit();
            })




//            array.push()

        })
    </script>
@stop

