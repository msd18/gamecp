<?php
//////////////////////////////////////////////////
//	Game CP: RF Online Game Control Panel		//
//	Module: config.php							//
//	Copyright (C) www.AaronDM.com				//
//////////////////////////////////////////////////

if(!defined("IN_GAMECP_SALT58585")) {
	die("Hacking Attempt");
	exit;
	return;
}

// Table Definations
define("TABLE_LUACCOUNT", "tbl_rfaccount");  // i.e. tbl_LUAccount or tbl_rfaccount, the latter for 2.2.3+

?>