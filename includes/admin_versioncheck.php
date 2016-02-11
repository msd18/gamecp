<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Super Admin"]["Check Version"] = $file;
}
else
{
    $lefttitle = "Admin - Check Game CP Version";
    $time = date("F j Y G:i");
    if( $this_script == $script_name ) 
    {
        if( hasPermissions($do) ) 
        {
            if( !isset($_license_properties) ) 
            {
                $_license_properties["version"] = 0;
            }

            $license_version = str_replace(".", "", $_license_properties["version"]);
            if( $server_version = sendheartbeat() ) 
            {
                $server_version = str_replace(".", "", $server_version);
                $license_info = ioncube_file_info();
                $license_expire_time = $license_info["FILE_EXPIRY"];
                $colours = "";
                $colours = "";
                if( $license_version < $server_version ) 
                {
                    $colours = "red";
                }
                else
                {
                    if( $server_version < $license_version ) 
                    {
                        $colours = "orange";
                    }
                    else
                    {
                        if( $license_version == $server_version ) 
                        {
                            $colours = "green";
                        }
                        else
                        {
                            $colours = "grey";
                        }

                    }

                }

                $license_version = substr(str_replace(".", "", $license_version), 1);
                $license_version = str_split($license_version, 2);
                $license_version = implode(".", $license_version);
                $a = array( "/^0(\\d+)/", "/\\.0(\\d+)/" );
                $b = array( "\\1", ".\\1" );
                $license_version = preg_replace($a, $b, $license_version);
                $server_version = substr(str_replace(".", "", $server_version), 1);
                $server_version = str_split($server_version, 2);
                $server_version = implode(".", $server_version);
                $a = array( "/^0(\\d+)/", "/\\.0(\\d+)/" );
                $b = array( "\\1", ".\\1" );
                $server_version = preg_replace($a, $b, $server_version);
                $out .= "<p><b>License Expire Date:</b> " . date("F j, Y \\a\\t g:i a T", $license_expire_time) . "<br/>" . "\n";
                $out .= "<b>Latest Game CP Version:</b> <span style=\"color: green;\">" . $server_version . "</span><br/>" . "\n";
                $out .= "<b>Current Game CP Version:</b> <span style=\"color: " . $colours . ";\">" . $license_version . "</span></p>" . "\n";
                if( $colours == "red" ) 
                {
                    $out .= "<p style=\"font-weight: bold; color: red;\">This copy of the RF Game Control Panel is out of date!</p>";
                    $out .= "<p>" . "\n";
                    $out .= "Download the latest version of the RF Game CP: ";
                    $out .= "<a href=\"http://www.aarondm.com/hosted/RF_GameCP_v" . $server_version . ".rar\">RF_GameCP_v" . $server_version . "</a>" . "\n";
                    $out .= "</p>" . "\n";
                }
                else
                {
                    if( $colours == "green" ) 
                    {
                        $out .= "<p style=\"color: green;\">You have the latest RF Online Game Control Panel</p>";
                    }
                    else
                    {
                        if( $colours == "orange" ) 
                        {
                            $out .= "<p>You have a BETA or ALPHA version of the RF Online Game Control Panel</p>";
                        }
                        else
                        {
                            $out .= "<p>Unknown version retrieved of the RF Online Game Control Panel</p>";
                        }

                    }

                }

                if( !function_exists("SimpleXMLElement") ) 
                {
                    $news_xml = "http://forum.aarondm.com/syndication.php?fid=16&limit=2 ";
                    $news_xml = @file_get_contents($news_xml);
                    if( is_bool($news_xml) && $news_xml == false ) 
                    {
                        $out .= "<p>Cannot get the latest development logs from Aaron DM due to being unable to connect using file_get_contents()</p>";
                        return 1;
                    }

                    $xml_news = new SimpleXMLElement($news_xml);
                    $xml_news = $xml_news->channel->item;
                    $out .= "<h2>Latest Development Logs</h2>";
                    $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\">";
                    foreach( $xml_news as $news ) 
                    {
                        $out .= "\t<tr>";
                        $out .= "\t\t<td class=\"tcat\" width=\"100%\" style=\"font-size: 120%; padding: 3px;\">" . $news->title . "</td>\r\n\t\t\t\t\t\t<td style=\"text-align: right; font-weight: normal;\" class=\"tcat\" nowrap><small>" . date("M mS, Y", strtotime($news->pubDate)) . "</small></td>";
                        $out .= "\t</tr>";
                        $out .= "\t<tr>";
                        $out .= "\t\t<td colspan=\"2\" class=\"alt1\">";
                        $out .= str_replace("border=\"0\"", "", $news->description);
                        $out .= "\t\t<br/>";
                        $out .= "\t\t</td>";
                        $out .= "\t</tr>";
                    }
                    $out .= "</table>";
                    return 1;
                }

            }
            else
            {
                $out .= "<p>Error! Unable to communicate with aarondm's (method: fsockopen) server to check for game cp version updates!</p>";
                return 1;
            }

        }
        else
        {
            $out .= $lang["no_permission"];
            return 1;
        }

    }
    else
    {
        $out .= $lang["invalid_page_load"];
    }

}


