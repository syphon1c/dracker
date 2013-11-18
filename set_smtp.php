<?php
/**
* Description of set_smtp.php
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
include 'configs/EncryptionKey.php';

$sessionid=sanitize($sessionid);
$connection = new DatabaseConnection();
$row = $connection->getRole($sessionid);

if($row != FALSE){
}
if($row == FALSE){
header("location:login.php");
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

//check to see if something was posted
if($_POST){
    //get values
    if(isset($_POST['host'])){
        $_SESSION['temp_smtp_host'] = $_POST['host'];
    }
    if(isset($_POST['port'])){
        $_SESSION['temp_smtp_port'] = $_POST['port'];
    }
    if(isset($_POST['ssl'])){
        $_SESSION['temp_smtp_ssl'] = $_POST['ssl'];
    }
    if(isset($_POST['username'])){
        $_SESSION['temp_smtp_username'] = $_POST['username'];
    }
    if(isset($_POST['password'])){
        $_SESSION['temp_smtp_password'] = $_POST['password'];
    }
    if(isset($_POST['sender_email'])){
        $_SESSION['temp_smtp_sender_email'] = $_POST['sender_email'];
    }
    if(isset($_POST['sender_name'])){
        $_SESSION['temp_smtp_sender_name'] = $_POST['sender_name'];
    }
    if(isset($_POST['default'])){
        $_SESSION['temp_smtp_default'] = $_POST['default'];
    }
    //validate and get host
    if(isset($_POST['host']) && preg_match( '/^[a-zA-Z0-9\-\_\.]/' , $_POST['host']) ){
        $host = $_POST['host'];
    }
    else{
        $_SESSION['alert_message'] = 'host was either empty or not a valid hostname';
        header ( 'location:./?add_smtp_server=true#tabs-2' );
        exit;
    }
    //validate and get port
    if(isset($_POST['port']) && preg_match('/^[0-9]/', $_POST['port']) && $_POST['port'] > 0 && $_POST['port'] < 65536 ){
        $port = $_POST['port'];
    }

    //get ssl status
    if(isset($_POST['ssl'])){
        $ssl = "1";
    }else{
        $ssl = "0";
    }
    //get username if provided
    if(isset($_POST['username'])){
        $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    }else{
        $username = "";
    }
    if(isset($_POST['sender_email'])){
        $sender_email = filter_var($_POST['sender_email'], FILTER_SANITIZE_STRING);
    }else{
        $sender_email = "";
    }
    if(isset($_POST['sender_name'])){
        $sender_name = filter_var($_POST['sender_name'], FILTER_SANITIZE_STRING);
    }else{
        $sender_name = "";
    }
    //get password if provided
    if(isset($_POST['password'])){
        $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
    }else{
        $password = "";
    }
    //get default status
    if(isset($_POST['default'])){
        $default = '1';
    }else{
        $default = '    0';
    }
    //connect to database
    //take away default value from any existing smtp servers that are set to default
    if($default == "1"){
        $connection = new DatabaseConnection();
        $query = $connection->executeQuery("UPDATE settings_smtp SET sys_default = '0' WHERE sys_default='1'");
    }
    //add smtp server details to database
    $connection = new DatabaseConnection();
    $query = $connection->executeQuery("INSERT INTO settings_smtp(host,port,ssl_enc,username,password,sender_email,sender_name,sys_default) VALUES('$host','$port', '$ssl', '$username', aes_encrypt('$password', '$encrypt_key'), '$sender_email', '$sender_name', '$default')");
    
    //unset temp variables
    if(isset($_SESSION['temp_smtp_host'])){
        unset($_SESSION['temp_smtp_host']);
    }
    if(isset($_SESSION['temp_smtp_port'])){
        unset($_SESSION['temp_smtp_port']);
    }
    if(isset($_SESSION['temp_smtp_ssl'])){
        unset($_SESSION['temp_smtp_ssl']);
    }
    if(isset($_SESSION['temp_smtp_username'])){
        unset($_SESSION['temp_smtp_username']);
    }
    if(isset($_SESSION['temp_smtp_password'])){
        unset($_SESSION['temp_smtp_password']);
    }
    if(isset($_SESSION['temp_smtp_sender_email'])){
        unset($_SESSION['temp_smtp_sender_email']);
    }
    if(isset($_SESSION['temp_smtp_sender_name'])){
        unset($_SESSION['temp_smtp_sender_name']);
    }
    if(isset($_SESSION['temp_smtp_default'])){
        unset($_SESSION['temp_smtp_default']);
    }

}
$_SESSION['alert_message'] = "smtp server added successfully";
header("location:settings.php");
exit;

?>
