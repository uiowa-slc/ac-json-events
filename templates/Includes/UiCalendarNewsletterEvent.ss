

<table align="left" border="0" cellpadding="0" cellspacing="0" class="mcnCaptionBottomContent" width="282" style="border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
    <tbody><tr>
        <td class="mcnCaptionBottomImageContent" align="center" valign="top" style="padding: 0 9px 9px 9px;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
        
            
            <a href="{$AfterClassLink}" title="" class="" target="_blank" style="word-wrap: break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
            

            <img alt="$Title" src="$Image.URL" width="264" height="165" style="max-width: 837px;border: 0;outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;vertical-align: bottom;" class="mcnImage">
            </a>
        
        </td>
    </tr>
    <tr>
        <td class="mcnTextContent" valign="top" style="padding: 0px 9px;text-align: left;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #eeeeee;font-family: Helvetica;font-size: 14px;line-height: 150%;" width="264">
            <h2 class="null" style="margin: 0;padding: 0;display: block;font-family: Helvetica;font-size: 26px;font-style: normal;font-weight: bold;line-height: 125%;letter-spacing: normal;text-align: left;color: #ffffff !important;"><a href="{$AfterClassLink}" target="_blank" style="word-wrap: break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #ffffff;font-weight: normal;text-decoration: underline;">$Title</a></h2>
<% loop $Dates.First %><% include UiCalendarDateLongNoLinks %><% end_loop %><% if $Dates.Count > "1" %><a href="{$AfterClassLink}" target="_blank">more dates</a><% end_if %><br>
<% include UiCalendarLocationLongNoLinks %><% if $Cost %><br>Cost: $Cost<% end_if %>
        </td>
    </tr>
</tbody></table>
