<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="rtl">
<head>
    <meta charset="UTF-8">



</head>
<body>
<div style="background:#ECECEC; width: 100%; height: 100%; margin: 0px" bgcolor="#ECECEC" dir="rtl">



    <table style="font-family:'Open Sans','Lucida Grande','Segoe UI',Arial,Verdana,'Lucida Sans Unicode',Tahoma,'Sans Serif'" align="center" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f8f8f8" bgcolor="#ECECEC">
        <tbody><tr>
            <td height="50">&nbsp;</td>
        </tr>
        <tr>
            <td height="60" valign="top">
                <table cellspacing="0" align="center" width="460" cellpadding="0">
                    <tbody>
                    <tr>
                        <td valign="middle" width="40" height="60" style="text-align: center; font-size: 30px;">
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td height="20">&nbsp;</td>
        </tr>
        <tr>
            <td valign="top">
                <table style="font-family:'Open Sans','Lucida Grande','Segoe UI',Arial,Verdana,'Lucida Sans Unicode',Tahoma,'Sans Serif';border:1px solid #eaeff2;border-radius:3px" bgcolor="#ffffff" cellpadding="0" cellspacing="0" border="0" align="center" width="550">
                    <tbody><tr>
                        <td>
                            <table style="font-family:'Open Sans','Lucida Grande','Segoe UI',Arial,Verdana,'Lucida Sans Unicode',Tahoma,'Sans Serif';padding:0" cellpadding="0" cellspacing="0" border="0" align="center" width="100%">
                                <tbody><tr>
                                    <td valign="top" colspan="2" height="30">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td width="30">&nbsp;</td>
                                    <td valign="top" colspan="2">
                                        <p style="margin-top:0;font-size:11pt;line-height:26px; color:#3C8DBC; text-align: center">
                                            <span style="font-size: 14px; font-weight: 600; color:#cd4c78">

                                            </span>
                                        </p>

                                        <p style="margin-top:0;font-size:11pt;line-height:26px;color:#323b43; text-align: right;">



                                        <h1 style="text-align: center;">{{ $details['title'] }}</h1>

                                        <p>المحتوى: {{ $details['body']['body'] }}</p>
                                        <p>البريد الإلكتروني: {{ $details['body']['email'] }}</p>
                                        <a href="{{$details['body']['url']}}"></a>

                                    </td>
                                    <td width="30">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td width="30">&nbsp;</td>
                                    <td colspan="2">
                                        <p style="text-align: center; font-size:14px; margin-bottom: 30px">
                                            تمنياتنا بيوم سعيد
                                        </p>
                                        <p style="color:#cd4c78; text-align: left; font-size:14px; margin-bottom: 30px">

                                        </p>
                                    </td>
                                    <td width="30">&nbsp;</td>

                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td height="50">&nbsp;</td>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>
