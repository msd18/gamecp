<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Super Admin"]["<b>Configuration</b>"] = $file;
}
else
{
    $lefttitle = "Super Admin - Game CP Configuration";
    $time = date("F j Y G:i");
    if( $this_script == $script_name ) 
    {
        $super_admin = explode(",", $admin["super_admin"]);
        $page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : "";
        if( $isuser = true && in_array($userdata["username"], $super_admin) ) 
        {
            if( empty($page) ) 
            {
                $out .= "<form method=\"post\">" . "\n";
                $out .= "<table width=\"100%\" cellpadding=\"4\" cellspacing=\"2\" border=\"0\">" . "\n";
                connectgamecpdb();
                $config_query = "SELECT config_name, config_value, config_description FROM gamecp_config";
                if( !($config_result = mssql_query($config_query, $gamecp_dbconnect)) ) 
                {
                    $out .= "Unable query the database";
                }

                while( $configx = mssql_fetch_array($config_result) ) 
                {
                    $out .= "\t<tr>" . "\n";
                    $out .= "\t\t<td><b>" . ucwords(strtolower(str_replace("_", " ", $configx["config_name"]))) . "</b><br/><i>" . $configx["config_description"] . "</i></td>" . "\n";
                    $out .= "\t\t<td><input type=\"text\" name=\"config[" . $configx["config_name"] . "]\" value=\"" . $configx["config_value"] . "\"/></td>" . "\n";
                    $out .= "\t</tr>" . "\n";
                }
                @mssql_free_result($config_result);
                $out .= "\t<tr>" . "\n";
                $out .= "\t\t<td colspan=\"2\" style=\"text-align: center;\"><input type=\"hidden\" name=\"page\" value=\"update\"/><input type=\"submit\" name=\"submit\" value=\"Update Config\"/>" . "\n";
                $out .= "\t</tr>" . "\n";
                $out .= "</table>";
                $out .= "</form>";
                return 1;
            }

            if( $page == "update" ) 
            {
                $configx = isset($_POST["config"]) ? $_POST["config"] : "";
                if( $configx != "" ) 
                {
                    connectgamecpdb();
                    $trigger_fail = false;
                    foreach( $configx as $key => $value ) 
                    {
                        $update_query = "UPDATE gamecp_config SET config_value = '" . antiject($value) . "' WHERE config_name = '" . antiject($key) . "'";
                        if( !($update_result = mssql_query($update_query)) ) 
                        {
                            $trigger_fail = true;
                            $out .= "<p style=\"text-align: center; font-weight: bold;\">Unable to update the config, please contact a developer.</p>";
                            return NULL;
                        }

                    }
                    if( !$trigger_fail ) 
                    {
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">Configuration has been successfully updated.</p>";
                        gamecp_log(2, $userdata["username"], "SUPER ADMIN - CONFIG - Updated the GameCP config", 1);
                        return 1;
                    }

                }

            }
            else
            {
                $out .= $lang["page_not_found"];
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


