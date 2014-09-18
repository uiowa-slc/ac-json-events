                <table align="left" border="0" cellpadding="0" cellspacing="0" width="282" class="mcnTextContentContainer" style="border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
                    <tbody><tr>
                        
                        <td valign="top" class="mcnTextContent" style="padding-top: 9px;padding-left: 18px;padding-bottom: 9px;padding-right: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #eeeeee;font-family: Helvetica;font-size: 14px;line-height: 150%;text-align: left;">
                        
                            <h2 class="null" style="margin: 0;padding: 0;display: block;font-family: Helvetica;font-size: 26px;font-style: normal;font-weight: bold;line-height: 125%;letter-spacing: normal;text-align: left;color: #ffffff !important;"><a href="{$AfterClassLink}" target="_blank" style="word-wrap: break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #ffffff;font-weight: normal;text-decoration: underline;">$Title</a></h2>
<% loop $Dates.First %><% include LocalistDateLongNoLinks %><% end_loop %><br>
<% include LocalistLocationLongNoLinks %><br>
$Cost
                        </td>
                    </tr>
                </tbody></table>