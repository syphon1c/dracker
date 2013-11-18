<?php
/**
* Description of menu.php
* version: 1.0
* package: Dracker - Track and Trace
* copyright: Copyright (C) 2013 Gareth Phillips. All rights reserved.
* license: GNU/GPL, see license.htm.
*
* This file is part of the Dracker project.
*
* Dracker is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, under version 3 of the License.
*
* Dracker is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Dracker. If not, see <http://www.gnu.org/licenses/>.
*
* @author GPhillips
**/

ini_set("display_errors", 0);
ob_start();

include_once 'configs/DatabaseConnection.php';
$connection = new DatabaseConnection();

if (!isset($include_allowed)){die("<meta http-equiv='refresh' content='0;url=\"logout.php'>");};

session_start();
$sessionid= session_id();
$sessionid=sanitize($sessionid);

$date = date('Y-m-d H:i:s');
$time=time();
$time_check=$time-3600; //60 minutes

// if over 20 minutes and no new activity we remove the session from DB
$connection->executeQuery("DELETE FROM loggedin WHERE time<$time_check");

// Adds X-Frame-Options to HTTP header, so that page cannot be shown in an iframe.
header('X-Frame-Options: DENY');

// Adds X-Frame-Options to HTTP header, so that page can only be shown in an iframe of the same site.
header('X-Frame-Options: SAMEORIGIN');

function cleanInput($input) {
    $search_input = array(
        '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
        '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
        '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
        '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
    );
    $output = preg_replace($search_input, '', $input);
    return $output;
}

function sanitize($input) {
    if (is_array($input)) {
        foreach($input as $var=>$val) {
            $output[$var] = sanitize($val);
        }
    }
    else {
        if (get_magic_quotes_gpc()) {
            $input = stripslashes($input);
        }
        $input  = cleanInput($input);
        $connection = new DatabaseConnection();
        $output = mysql_real_escape_string($input);
    }
    return $output;
}

?>
<!DOCTYPE html>
<html lang="en"> 
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width">
	<style>
	body {
		padding-top: 60px; /* When using the navbar-top-fixed */
    	}
    </style>

    <style>
    /* To keep short panes open decently tall */
   	.tab-pane {min-height: 500px;}
   	
    .navbar2{
    position:fixed;    
    }
    
        html { overflow-y: scroll; font-size: 100%; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }

    </style>
</head>

<div class="navbar2 navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
        		<span class="icon-bar"></span>
        		<span class="icon-bar"></span>
      		</a>

			<a class="brand" href="#">Dracker v1.0</a>
			<div class="nav-collapse">
				<ul class="nav">
					<li ><a href="view_results.php">Home</a></li>
      			</ul>
            <a class="btn-flat smallnavbar pull-right" href="logout.php">Logout</a>
      			<div class="pull-right">
      				<ul class="nav pull-right">
      					</li>
      				</ul>
      			</div>
      			</ul>
     		</div><!-- /.nav-collapse -->
   		</div><!-- /.container -->
  	</div><!-- /navbar-inner -->
</div><!-- /navbar -->

<script type='text/javascript'>
$(document).ready(function(){ 
  $('.dropdown-toggle').dropdown();
  });
</script>

