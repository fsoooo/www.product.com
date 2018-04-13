@extends('backend.layout.base')
@section('content')
    <div id="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                @include('backend.layout.alert_info')
                <div class="row">
                    <div class="col-lg-12">
                        <ol class="breadcrumb">
                            <li><a href="{{ url('/backend') }}">主页</a></li>
                            <li class="active"><span>这里应该是主页</span></li>
                        </ol>
                        <h1>这里应该是主页</h1>
                            {{--@if(isset($onlines))--}}
                            {{--@foreach($onlines as $online)--}}
                                {{--<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&site=qq&menu=yes&uin={{$online['number']}}">--}}
                                    {{--<img border="0" src="http://wpa.qq.com/pa?p=2:1554231908:3" alt="" title=""/><br/>--}}
                                    {{--<span style="height: 20px;width: 70px">{{$online['name']}}</span>--}}
                                {{--</a><br/>--}}
                            {{--@endforeach--}}
                            {{--@endif--}}
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

