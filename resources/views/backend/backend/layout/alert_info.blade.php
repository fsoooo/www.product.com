@if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif
@if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            <li><h4>错误信息：</h4></li>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif