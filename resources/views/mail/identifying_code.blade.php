<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
　   <head>
    　　<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    　　<title>HTML Email编写指南</title>
    　　<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <style>
            img {outline:none; text-decoration:none; -ms-interpolation-mode: bicubic;}
            a img {border:none;}
        </style>
    　</head>
        <body style="margin: 0; padding: 0;">
            <table align="center" border="0" cellpadding="20" cellspacing="15" width="900" style="border-collapse: collapse;">
                    {{--<p style="margin-top: 1em; margin-bottom: 1em; margin-left: 0; margin-right: 0;">--}}
                    　   <tr>
                            <td>你好，</td>
                        </tr>
                    　   <tr>
                        　　<td>您再进行***操作，验证码{{$data}}，5分钟内有效。如非本人操作请忽略本邮件。</td>
                        　</tr>
                    　   <tr>
                    　　<td>天眼互联</td>
                    　　<td>{{date("Y-m-d H:i:s",time())}}</td>
                        </tr>
            </table>
        </body>
</html>