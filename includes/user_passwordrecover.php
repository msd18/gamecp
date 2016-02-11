<?php 
if( !empty($setmodules) ) 
{
    return NULL;
}

$lefttitle = "Password Recovery";
if( $this_script == $script_name ) 
{
    if( !$isuser ) 
    {
        $page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : "";
        if( $page == "recover" ) 
        {
            $iemail = antiject($_REQUEST["iemail"]);
            if( eregi("[^a-zA-Z0-9_.@]", $iemail) ) 
            {
                $out .= "<center>Invalid e-mail address<br><a href=\"" . $script_name . "?do=" . $_GET["do"] . "\">Return</a></center>";
            }
            else
            {
                $iemail = antiject($iemail);
                connectuserdb();
                $query = mssql_query("SELECT CONVERT(varchar,ID) AS account, Email, CONVERT(varchar,Password) AS passwd FROM " . TABLE_LUACCOUNT . " WHERE Email=\"" . $iemail . "\"");
                if( $iemail == "" ) 
                {
                    $query = "";
                }

                if( @mssql_num_rows($query) <= 0 ) 
                {
                    $out .= "<center>This email does not exist in our database <br><a href=\"" . $script_name . "?do=" . $_GET["do"] . "\">Return</a></center>";
                }
                else
                {
                    $to = $iemail;
                    $subject = $config["lostpass_subject"];
                    $message = str_replace("[servername]", $config["server_name"], $config["lostpass_message"]) . "\n\n";
                    while( $query2 = mssql_fetch_array($query) ) 
                    {
                        $username = ereg_replace("" . ";\$", "", $query2["account"]);
                        $username = ereg_replace("\\\\", "", $username);
                        $password = ereg_replace("" . ";\$", "", $query2["passwd"]);
                        $password = ereg_replace("\\\\", "", $password);
                        $message .= "Account: " . $username . "\n";
                        $message .= "Password: " . $password . "\n";
                        $message .= "\n";
                    }
                    @mssql_free_result($query);
                    if( isset($config["gamecp_smtp_enable"]) && $config["gamecp_smtp_enable"] == 1 ) 
                    {
                        include("./includes/main/class.phpmailer.php");
                        $mail = new PHPMailer();
                        $body = eregi_replace("[\\]", "", $message);
                        $mail->SetLanguage("en", "./includes/main/");
                        $mail->SMTPAuth = true;
                        if( $config["gamecp_smtp_enable_ssl"] == 1 ) 
                        {
                            $mail->SMTPSecure = "ssl";
                        }

                        $mail->Host = $config["gamecp_smtp_server"];
                        $mail->Port = $config["gamecp_smtp_port"];
                        $mail->Username = $config["gamecp_smtp_username"];
                        $mail->Password = $config["gamecp_smtp_password"];
                        $mail->From = $config["lostpass_email"];
                        $mail->FromName = $config["server_name"];
                        $mail->Subject = $subject;
                        $mail->Body = $body;
                        $mail->AddAddress($iemail, "Lost Password Recovery");
                        if( !$mail->Send() ) 
                        {
                            $out .= "<center>Could not send mail. Please contact a administrator.<br>" . $mail->ErrorInfo . "<br/><a href=\"" . $script_name . "\">Return</a></center>";
                        }
                        else
                        {
                            $out .= "<center>Your password has been sent to: <b>" . $iemail . "</b>.<br><a href=\"" . $script_name . "\">Return</a></center>";
                        }

                    }
                    else
                    {
                        $mime_boundary = "----RFGameCP----" . md5(time());
                        $headers = "From: " . $config["lostpass_email"] . "\n";
                        $headers .= "MIME-Version: 1.0\n";
                        $headers .= "" . "Content-Type: multipart/alternative; boundary=\"" . $mime_boundary . "\"" . "\n";
                        $mess = "" . "--" . $mime_boundary . "\n";
                        $mess .= "Content-Type: text/plain; charset=UTF-8\n";
                        $mess .= "Content-Transfer-Encoding: 8bit\n\n";
                        $mess .= $message;
                        $mess .= "" . "--" . $mime_boundary . "--\n\n";
                        if( mail($iemail, $config["lostpass_subject"], $mess, $headers) ) 
                        {
                            $out .= "<center>Your password has been sent to: <b>" . $iemail . "</b>.<br><a href=\"" . $script_name . "\">Return</a></center>";
                        }
                        else
                        {
                            $out .= "<center>Could not send mail. Please contact a administrator.<br><a href=\"" . $script_name . "\">Return</a></center>";
                        }

                    }

                    if( !empty($forum_username) ) 
                    {
                        $username = "Forum: " . $forum_username;
                    }
                    else
                    {
                        $username = "*";
                    }

                    gamecp_log(1, $username, "GAMECP - PASSWORD RECOVERY - EMAIL: " . $iemail, 1);
                }

            }

        }
        else
        {
            $navbits = array( "" . $script_name . "" => "Game CP", "" => "Password Recovery" );
            $out .= "<center>Enter your email address below and we will send you an email address with your current password.<br /> If you have not updated your email address previously, you will not be able to use this form.<br>" . "<form method=\"POST\" action=\"" . $script_name . "?do=" . $_GET["do"] . "\">" . "<table border=\"0\">" . "<tr><td align=\"left\"><strong>E-mail: </td><td align=\"left\"> <input type=\"text\" name=\"iemail\"></td></tr>" . "<tr><td colspan=\"2\" align=\"center\"><input type=\"hidden\" name=\"page\" value=\"recover\"><input type=\"submit\" value=\"Send Password\"></td></tr>" . "</table></form></center>";
        }

    }
    else
    {
        $out .= $lang["no_permission"];
    }

}
else
{
    $out .= $lang["invalid_page_load"];
}


