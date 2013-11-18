<?php
/**
* Description of edit_smtp.php
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
include_once 'configs/EncryptionKey.php';

$include_allowed = true;

session_start();
$sessionid= session_id();

$sessionid=sanitize($sessionid);
$connection = new DatabaseConnection();
$row = $connection->getRole($sessionid);

if($row != FALSE){
}
if($row == FALSE){
header("location:login.php");
}

$connection = new DatabaseConnection();
$querySMTPdefault = $connection->executeQuery("SELECT host, port, ssl_enc, username, aes_decrypt(password, '$encrypt_key') as password, sender_email, sender_name, sys_default FROM settings_smtp WHERE sys_default = \"1\"");
while($smtpItems = mysql_fetch_array($querySMTPdefault) ){
    $host = $smtpItems['host'];
    $port = $smtpItems['port'];
    $usernamesmtp = $smtpItems['username'];
    $passwordsmtp = $smtpItems['password'];
    $sendmail = $smtpItems['sender_email'];
    $sendname = $smtpItems['sender_name'];
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
    <h5 id="edituserModalLabel">Add SMTP Server <?echo $passwordsmtp;?></h5>
</div>

<div class="well">
    <form method="POST" action="set_smtp.php">
        <label>Host
        <input type="text" value="<? echo $host;?>" class="input-large" autocomplete="off" id="host" name="host" required>
        Port
        <input type="text" value="<? echo $port;?>"class="input-small" autocomplete="off" id="port" name="port" required></label>
        &nbsp;&nbsp;SSL/TLS enabled
        <input type="checkbox" name="ssl" 
        <?php
        if(isset($_SESSION['temp_smtp_ssl'])){
            echo 'CHECKED';
            unset($_SESSION['temp_smtp_ssl']);
            }
        echo '/>';
         ?>
        </label>
        
        <label>Username
        <input type="text" value="<? echo $usernamesmtp;?>" class="input-large" autocomplete="off" id="username" name="username"></label>
        
        <label>Password
        <input type="text" value="<? echo $passwordsmtp;?>" class="input-large" autocomplete="off" id="password" name="password"></label>
        
        <label>From Email
        <input type="text" value="<? echo $sendmail;?>" class="input-large" autocomplete="off" id="sender_email" name="sender_email" required></label>
        
        <label>Senders Name
        <input type="text" value="<?echo $sendname;?>" class="input-large" autocomplete="off" id="sender_name" name="sender_name" required></label>

        <input type="hidden" name="default" id="default" value="1">
        </label>
        <br>

        <div class="modal-footer">
            <button class="btn-flat gray" data-dismiss="modal" aria-hidden="true">Cancel</button>
            <button class="btn-flat primary" >Add Host</button>
        </div>
    </form>
</div>


