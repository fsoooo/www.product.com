@extends('backend.layout.base')
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <div class="panel">
                <div class="panel-heading">
                    <p class="panel-title">产品与API来源绑定</p>
                </div>
                <div class="panel-body">
                    @include('backend.layout.alert_info')
                    <form action="{{ route('insurance.bind.store') }}" method="post">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="insurance_id">产品</label>
                            <select name="insurance_id" id="insurance_id" class="form-control">
                                <option value="0">请选择产品</option>
                                @foreach($insurances as $insurance)
                                    <option value="{{ $insurance->id }}" id="insurance_id-{{ $insurance->id }}">{{ $insurance->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($bind_insurance)
                            <label><h4>关联API来源</h4></label>
                            <label for="" style="margin-left: 20px;">
                                <button type="submit" id="attach_submit" class="btn btn-success">提交绑定关系</button>
                            </label>
                            <div class="form-group">
                                    <label class="radio-inline">
                                        <input id="radio-0" name="api_from_id" type="radio" value="0">
                                        无
                                    </label>
                                @foreach ($api_froms as $api_from)
                                        <label class="radio-inline">
                                            <input id="radio-{{ $api_from->id }}" name="api_from_id" type="radio" value="{{ $api_from->id }}">
                                            {{ $api_from->name }}
                                        </label>
                                @endforeach
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('foot-js')
    <script>
        $(function () {
            var url = '{{ route('insurance.bind.index') }}' + '/';
            @if (isset($bind_api_from->id))
                $('#radio-' + '{{ $bind_api_from->id }}').prop('checked', true);
            @endif
            @if (isset($bind_insurance->id))
                $('#insurance_id-' + '{{ $bind_insurance->id }}').prop('selected', true);
            @endif
            $('#insurance_id').on('change', function() {
                location.href = url + $(this).val();
            });
        });
    </script>
@endsection