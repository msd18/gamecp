<?php 
define("IN_GAMECP_SALT58585", true);
include("./gamecp_common.php");
gamecp_nav($isuser);
$title = $program_name . " - Vote For Game Points";
$page_info = "";
$insert_timestamp = "";
$update_timestamp = "";
$redirect_url = "";
$exit_stage_0 = false;
$exit_stage_1 = false;
$exit_stage_2 = false;
$update_data = true;
$show_form = true;
$voted = false;
$vote_id = isset($_POST["vote_id"]) && is_numeric($_POST["vote_id"]) ? antiject($_POST["vote_id"]) : "";
$account_name = isset($_POST["vote_account"]) && !empty($_POST["vote_account"]) ? antiject($_POST["vote_account"]) : "";
$account_name = preg_replace(sql_regcase("/(from|select|insert|delete|where|drop table|show tables|#|\\*|--|\\\\)/"), "", $account_name);
$vote_id = ceil($vote_id);
$max_gp = isset($config["vote_max_gp"]) ? $config["vote_max_gp"] : 4;
$min_gp = isset($config["vote_min_gp"]) ? $config["vote_min_gp"] : 1;
if( $max_gp <= 0 ) 
{
    $max_gp = 4;
}

if( $min_gp <= 0 ) 
{
    $min_gp = 1;
}

if( $max_gp < $min_gp ) 
{
    $max_gp = $min_gp;
}

if( $max_gp < 1 || $min_gp < 1 ) 
{
    $gp_increase = game_cp_19($min_gp, $max_gp);
}
else
{
    $gp_increase = rand($min_gp, $max_gp);
}

$user_points = "" . "user_points = user_points+" . $gp_increase . ",";
if( !($total_sites = count($vote)) ) 
{
    $show_form = false;
    $exit_stage_0 = true;
    $page_info .= "<p style=\"text-align: center; font-weight: bold;\">Sorry, unable to find any sites to vote for!</p>";
}

if( $vote_id != "" && $account_name != "" && $exit_stage_0 == false ) 
{
    connectuserdb();
    $user_sql = "SELECT TOP 1 Serial FROM tbl_UserAccount WHERE id = convert(binary,'" . $account_name . "')";
    if( !($user_result = mssql_query($user_sql)) ) 
    {
        $exit_stage_1 = true;
        $show_form = false;
        $page_info .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error while trying to get your account info</p>";
    }

    $user = @mssql_fetch_array($user_result);
    if( @mssql_num_rows($user_result) <= 0 ) 
    {
        $exit_stage_1 = true;
        $page_info .= "<p style=\"text-align: center; font-weight: bold;\">The Account Name (" . $account_name . ") you entered cannot be found</p>";
    }

    if( $exit_stage_1 == false ) 
    {
        $user_serial = $user["Serial"];
        connectgamecpdb();
        $gamecp_sql = "" . "SELECT user_id, user_points, user_vote_timestamp FROM gamecp_gamepoints WHERE user_account_id = '" . $user_serial . "'";
        if( !($gamecp_result = mssql_query($gamecp_sql)) ) 
        {
            $exit_stage_2 = true;
            $show_form = false;
            $page_info .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error while trying to get Game CP Data</p>";
        }

        $gamecp = @mssql_fetch_array($gamecp_result);
        if( @mssql_num_rows($gamecp_result) <= 0 ) 
        {
            for( $k = 0; $k < $total_sites; $k++ ) 
            {
                if( $k != 0 ) 
                {
                    $insert_timestamp .= ",";
                }

                if( $vote_id == $vote[$k]["vote_id"] ) 
                {
                    $insert_timestamp .= $vote[$k]["vote_id"] . ":" . time();
                    $redirect_url = $vote[$k]["vote_site_url"];
                    $voted = true;
                }
                else
                {
                    $insert_timestamp .= $vote[$k]["vote_id"] . ":" . "0";
                }

            }
            $page_info .= $insert_timestamp;
            if( $voted == true ) 
            {
                $insert_sql = "" . "INSERT INTO gamecp_gamepoints (user_account_id, user_points, user_vote_timestamp) VALUES ('" . $user_serial . "', '" . $gp_increase . "', '" . $insert_timestamp . "')";
                if( !($insert_result = mssql_query($insert_sql)) ) 
                {
                    $show_form = false;
                    $page_info .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error while trying to add game points data</p>";
                }
                else
                {
                    game_cp_20($user_serial, $ip, $gp_increase, $gp_increase);
                    header("" . "Location: " . $redirect_url);
                }

            }
            else
            {
                $page_info .= "<p style=\"text-align: center; font-weight: bold;\">This website does not exist...nice...try</p>";
            }

        }
        else
        {
            $user_vote_timestamp = $gamecp["user_vote_timestamp"];
            if( empty($user_vote_timestamp) || $user_vote_timestamp == " " ) 
            {
                for( $k = 0; $k < $total_sites; $k++ ) 
                {
                    if( $k != 0 ) 
                    {
                        $update_timestamp .= ",";
                    }

                    if( $vote_id == $vote[$k]["vote_id"] ) 
                    {
                        $update_timestamp .= $vote[$k]["vote_id"] . ":" . time();
                        $redirect_url = $vote[$k]["vote_site_url"];
                        $voted = true;
                    }
                    else
                    {
                        $update_timestamp .= $vote[$k]["vote_id"] . ":" . "0";
                    }

                }
                if( $voted == true ) 
                {
                    $update_sql = "" . "UPDATE gamecp_gamepoints SET " . $user_points . " user_vote_timestamp = '" . $update_timestamp . "' WHERE user_account_id = '" . $user_serial . "'";
                    if( !($update_result = mssql_query($update_sql)) ) 
                    {
                        $show_form = false;
                        $page_info .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error while trying to update game points data</p>";
                    }
                    else
                    {
                        game_cp_20($user_serial, $ip, $gp_increase, $gamecp["user_points"] + $gp_increase);
                        header("" . "Location: " . $redirect_url);
                    }

                }
                else
                {
                    $page_info .= "<p style=\"text-align: center; font-weight: bold;\">This website does not exist...nice...try</p>";
                }

            }
            else
            {
                $split_raw_timestamp = explode(",", $user_vote_timestamp);
                for( $i = 0; $i < count($split_raw_timestamp); $i++ ) 
                {
                    $split_info = explode(":", $split_raw_timestamp[$i]);
                    $split_id[] = $split_info[0];
                    $split_timestamp[$split_info[0]] = $split_info[1];
                }
                for( $n = 0; $n < $total_sites; $n++ ) 
                {
                    if( $vote[$n]["vote_id"] == $vote_id ) 
                    {
                        $now_check = time() - $split_timestamp[$vote_id];
                        if( $now_check < $vote[$n]["vote_reset_time"] ) 
                        {
                            $time_arr = game_cp_21($vote[$n]["vote_reset_time"] - $now_check);
                            $page_info .= "<meta http-equiv=\"REFRESH\" content=\"10;url=" . $vote[$n]["vote_site_url"] . "\">";
                            $page_info .= "<b>Voting for site: " . $vote[$n]["vote_site_name"] . "</b><br/>";
                            $page_info .= "Woops, your vote has not been counted!<br/> Its good to see that you want to vote for us, but please wait <b>" . $time_arr["hours"] . ":" . $time_arr["minutes"] . ":" . $time_arr["seconds"] . "</b> until you can vote for GP again.";
                            $page_info .= "<br/><br/>";
                            $page_info .= "<a href=\"" . $vote[$n]["vote_site_url"] . "\"><b>Continue to the vote site >></b></a>";
                            $update_data = false;
                            $show_form = false;
                            break;
                        }

                        $update_data = true;
                    }
                    else
                    {
                        $update_data = true;
                    }

                }
                if( $update_data == true ) 
                {
                    for( $k = 0; $k < $total_sites; $k++ ) 
                    {
                        if( !isset($split_timestamp[$vote[$k]["vote_id"]]) ) 
                        {
                            $split_timestamp[$vote[$k]["vote_id"]] = 0;
                        }

                        if( $k != 0 ) 
                        {
                            $update_timestamp .= ",";
                        }

                        if( $vote_id == $vote[$k]["vote_id"] ) 
                        {
                            $update_timestamp .= $vote[$k]["vote_id"] . ":" . time();
                            $redirect_url = $vote[$k]["vote_site_url"];
                            $voted = true;
                        }
                        else
                        {
                            $update_timestamp .= $vote[$k]["vote_id"] . ":" . $split_timestamp[$vote[$k]["vote_id"]];
                        }

                    }
                    if( $voted != true ) 
                    {
                        $user_vote_points = 0;
                    }

                    if( $voted == true ) 
                    {
                        $update_sql = "" . "UPDATE gamecp_gamepoints SET " . $user_points . " user_vote_timestamp = '" . $update_timestamp . "' WHERE user_account_id = '" . $user_serial . "'";
                        if( !($update_result = mssql_query($update_sql)) ) 
                        {
                            $show_form = false;
                            $page_info .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error while trying to update game points data</p>";
                        }
                        else
                        {
                            game_cp_20($user_serial, $ip, $gp_increase, $gamecp["user_points"] + $gp_increase);
                            header("" . "Location: " . $redirect_url);
                        }

                    }
                    else
                    {
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">This website does not exist...nice...try</p>";
                    }

                }

            }

        }

    }

}

if( $show_form == true ) 
{
    $page_info .= "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">" . "\n";
    $page_info .= "\t<tr> " . "\n";
    $page_info .= "\t\t<td style=\"text-align: center;\" valign=\"middle\">" . "\n";
    $page_info .= "\t\t" . $vote_page . "\n";
    $page_info .= "\t\t</td>" . "\n";
    $page_info .= "\t</tr>" . "\n";
    $page_info .= "\t<tr>" . "\n";
    $page_info .= "\t\t<td>" . "\n";
    $page_info .= "\t\t\t<div id=\"show_vinfo\" style=\"text-align: center;\"></div>" . "\n";
    $page_info .= "\t\t</td>" . "\n";
    $page_info .= "\t</tr>" . "\n";
    $page_info .= "</table>" . "\n";
}

eval("print_outputs(\"" . gamecp_template("rf_votescript") . "\");");
function game_cp_19($min, $max)
{
    return $min + lcg_value() * abs($max - $min);
}

function game_cp_20($account_serial, $ip, $gained, $total)
{
    global $gamecp_dbconnect;
    $time = time();
    $insert_log = "" . "INSERT INTO gamecp_vote_log (log_account_serial, log_time, log_ip, log_points_gained, log_total_points) VALUES ('" . $account_serial . "', '" . $time . "', '" . $ip . "', '" . $gained . "', '" . $total . "')";
    if( !($log_result = mssql_query($insert_log, $gamecp_dbconnect)) ) 
    {
    }

}


