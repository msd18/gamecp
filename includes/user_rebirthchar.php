<?php 
if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Account"]["Rebirth"] = $file;
}
else
{
	$lefttitle = "Rebirth";
	if( $this_script == $script_name && $isuser )
		$page = isset($_GET["page"]) ? $_GET["page"] : "";
		$show_form = true;
		if( $page == "" )
		{
			//$out .= "<p style=\"text-align: center; font-weight: bold;\">Rebirth Module is currently under development check back later.</p>";
			$page = "active";
		}
		if( $page == "active")
		{
			$out .= "<p style=\"text-align: center; font-weight: bold;\">Character Rebirth gives you 100 Gamepoints per rebirth</p>";
			if( !($chars = getcharacters($userdata["serial"])) )
			{
				$out .= "<p style=\"text-align: center; font-weight: bold;\">According to our database, you have no characters on your account</p>";
				return 1;
			}
		                if( !empty($chardata) ) 
               			 {
                    		foreach( $chardata as $character ) 
                   		 	{
                        $out .= "<option value=\"" . $character["Name"] . "\">" . $character["Name"];
                    		}
                		}

                $out .= "</select></td></tr>" . "<tr><td><strong>New Character Name: </strong></td><td> <input type=\"text\" name=\"newname\"></td></tr>" . "<tr><td><strong>Comfirm Name: </strong></td><td> <input type=\"text\" name=\"comfname\"></td></tr>" . "<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" disabled value=\"Change Name\"></td></tr>" . "</table></form>";
                return 1;
			
		}
}