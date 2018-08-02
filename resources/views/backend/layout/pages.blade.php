@if($currentPage== '1')
    @if($currentPage==$pages)
        <a onclick='getData("{{$currentPage}}")'><button>当前页:{{$currentPage }}</button></a>
        {{--<a onclick='getData("{{$currentPage+1}}")'><button>下一页</button></a>--}}
        {{--<a onclick='getData("{{$pages}}")'><button>尾页</button></a>--}}
        <a><button>总页数{{$pages }}</button></a>
    @elseif($currentPage>$pages)
        暂无数据
        {{--<a onclick='getData("{{$currentPage}}")'><button>当前页:{{$currentPage }}</button></a>--}}
        {{--<a onclick='getData("{{$currentPage+1}}")'><button>下一页</button></a>--}}
        {{--<a onclick='getData("{{$pages}}")'><button>尾页</button></a>--}}
        {{--<a><button>总页数{{$pages }}</button></a>--}}
    @else
        <a onclick='getData("{{$currentPage}}")'><button>当前页:{{$currentPage }}</button></a>
        <a onclick='getData("{{$currentPage+1}}")'><button>下一页</button></a>
        <a onclick='getData("{{$pages}}")'><button>尾页</button></a>
        <a><button>总页数{{$pages }}</button></a>
    @endif
@elseif($currentPage==$pages)
    <a onclick='getData("1")'><button>首页</button></a>
    <a onclick='getData("{{$currentPage-1}}")'><button>上一页</button></a>
    <a onclick='getData("{{$currentPage}}")'><button>当前页:{{$currentPage }}</button></a>
    <a><button>总页数{{$pages }}</button></a>
@else
    <a onclick='getData("1")'><button>首页</button></a>
    <a onclick='getData("{{$currentPage-1}}")'><button>上一页</button></a>
    <a onclick='getData("{{$currentPage}}")'><button>当前页:{{$currentPage }}</button></a>
    <a onclick='getData("{{$currentPage+1}}")'><button>下一页</button></a>
    <a onclick='getData("{{$pages}}")'><button>尾页</button></a>
    <a><button>总页数{{$pages }}</button></a>
@endif
<script type="text/javascript">
    function getData(page){
        var url =  "{{url('')}}";
        var params = "{{$params}}";

        var end = params.substr(params.length-1,1);
        if(end==";"){
            param=params.replace("amp;","");
            window.location.href =  url+param+'page'+'='+page;
        }else{
            window.location.href =  url+params+'?page'+'='+page;
        }

    }
</script>