<?php
/**
* Description of user_edit_profile.php
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

include_once 'configs/DatabaseConnection.php';

$include_allowed = true;

session_start();
$sessionid= session_id();
$sessionid=sanitize($sessionid);

$connection = new DatabaseConnection();
$queryLoggedinDetail = $connection->executeQuery("SELECT uid FROM loggedin WHERE session = '$sessionid'");
    while ($rowLoggedinID = mysql_fetch_array($queryLoggedinDetail)){
        $userid=$rowLoggedinID['uid'];

        $queryUserEmail = $connection->executeQuery("SELECT * FROM user_account WHERE id = '$userid'");
        while ($rowUserEmail = mysql_fetch_array($queryUserEmail)){
            $email=$rowUserEmail['email'];
	    $name=$rowUserEmail['name'];
	    $surname=$rowUserEmail['surname'];
        }
    }

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

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h5 id="edituserModalLabel">Update Your Profile </h5>
</div>

<div class="well">
    <form method="POST" action="settings.php">
        <label>Name
        <input type="text" value="<? echo $name;?>" class="input-medium" autocomplete="off" id="upname" name="upname" required>
	Surname
        <input type="text" value="<? echo $surname;?>"class="input-medium" autocomplete="off" id="upsurname" name="upsurname" required></label>
        </label>
        
        <label>Email
        <input type="text" value="<? echo $email;?>" class="input-large" autocomplete="off" id="upemail" name="upemail" required></label>
        
        <br>

        <div class="modal-footer">
            <button class="btn-flat gray" data-dismiss="modal" aria-hidden="true">Cancel</button>
            <button class="btn-flat primary" >Update</button>
        </div>
    </form>
</div>


