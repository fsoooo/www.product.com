@extends('backend.layout.base')
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <div class="panel">
                <div class="panel-heading">
                    <p class="panel-title">团险模板上传</p>
                </div>
                <div class="panel-body">
                    @include('backend.layout.alert_info')
                    <form action="{{ route('insurance.bind.upload.template') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ $relation->id }}">
                        <div class="form-group">
                            <label for="template_url">上传模板</label>
                            <input type="file" name="template_url" id="template_url"  value="{{ $relation->template_url }}">
                        </div>
                        <div class="form-group">
                            <a href="{{ env('APP_URL').'/'.$relation->template_url }}">原模板地址</a>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary">确定</button>
                            <a href="{{ route('insurance.bind.upload.template') }}" class="btn btn-primary">返回</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection