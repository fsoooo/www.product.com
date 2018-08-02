<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<style>
    .tr_content td{padding:5px 30px; border-bottom: 1px solid gainsboro;}
</style>
<body style="font-size: 16px;">
    <table  style="margin-top: 20px;text-align: center;">
        <tr style="height:30px; text-align: center;">
            <td  colspan="4" style="font-weight: bold">
                {{$data['day']}}投保人员列表&nbsp;&nbsp;&nbsp;&nbsp;共：{{count($data['email_data'])}} 人
            </td>
        </tr>
        <tr class="tr_content">
            <td></td>
            <td>姓名</td>
            <td>身份证号</td>
            <td>保障期间</td>
        </tr>
        @foreach($data['email_data'] as $k => $v)
        <tr class="tr_content">
            <td>{{$k + 1}}</td>
            <td>{{$v->chinesename}}</td>
            <td>{{$v->idcredit}}</td>
            <td>{{$v->insure_time}}&nbsp;&nbsp;--&nbsp;&nbsp;{{date('Y-m-d H:i:s', strtotime($v->insure_time. '+ 24 hours'))}}</td>
        </tr>
        @endforeach
    </table>

    <table style="margin-top: 20px;text-align: center;">
        <tr style="height:30px;">
            <td  colspan="3" style="font-weight: bold">
                {{$data['day']}}拒保人员列表&nbsp;&nbsp;&nbsp;&nbsp;共：{{count($data['refuse_data'])}} 人
            </td>
        </tr>
        <tr class="tr_content">
            <td></td>
            <td>姓名</td>
            <td>身份证号</td>
        </tr>
        @foreach($data['refuse_data'] as $k => $v)
            <tr class="tr_content">
                <td>{{$k + 1}}</td>
                <td>{{$v->chinesename}}</td>
                <td>{{$v->idcredit}}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>