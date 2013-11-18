<?php
/**
* Description of images.php
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

function curPageURL() {
    $pageURL = 'http';
    if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
    return $pageURL;
}

function ip_details($ip_lookup) {
    $json = file_get_contents("http://ipinfo.io/{$ip_lookup}");
    $details_lookup = json_decode($json);
    return $details_lookup;
}

header('Content-Type: image/gif'); 
header("Content-Length: " . filesize("image.gif"));
$f = fopen('image.gif', 'rb');
fpassthru($f);
fclose($f);


include_once 'configs/DatabaseConnection.php';
include_once 'configs/EncryptionKey.php';
include_once 'inc/swiftmailer/lib/swift_required.php';
include_once 'SMTP.php';

if(isset($_GET['imageid'])) {
    $refid=$_GET['imageid'];
    $refid=sanitize($refid);
}

$ip = $_SERVER['REMOTE_ADDR'];
$browser = $_SERVER['HTTP_USER_AGENT'];
$ip2 = "";
$host_name = gethostbyaddr($ip) ;
if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip2 = $_SERVER['HTTP_X_FORWARDED_FOR'];
}

$ip = sanitize($ip);
$browser = sanitize($browser);
$ip2 = sanitize($ip2);
$host_name = sanitize($host_name);

$time=date("y-m-d h:i:s");

$details_lookup = ip_details($ip);

$connection = new DatabaseConnection();
$queryDocumentDracks = $connection->executeQuery("INSERT INTO call_home VALUES (NULL, '$ip', '$host_name', '$ip2', '$browser', '', '$refid', '$time', '$details_lookup->org', '$details_lookup->city', '$details_lookup->country', '$details_lookup->region')");
$queryDocumentDracksOpened = $connection->executeQuery("UPDATE dracker_file SET Status= 'Opened' WHERE RefID= '$refid' AND Status='Waiting'");
$DrackerEmailAlert = "SELECT * FROM dracker_file WHERE RefID= '$refid'";
$smtpArray = $connection->executeQuery($DrackerEmailAlert);
while($smtpItems = mysql_fetch_array($smtpArray) ){
    $send_to = $smtpItems['Email'];
    $dracker_name = $smtpItems['FileName'];
}

$connection = new DatabaseConnection();
$querySMTPdefault = "SELECT * FROM settings_smtp WHERE sys_default= 1";
$smtpArray = $connection->executeQuery($querySMTPdefault);
while($smtpItems = mysql_fetch_array($smtpArray) ){
    $sid = $smtpItems['id'];
    $default = $smtpItems['sys_default'];
}
    
if($default ==1){
    $current_url = curPageURL();
    $login_url = str_replace("images.php?imageid=$refid",'login.php', $current_url);
    //SMTP Create mail alert and send
    $mailer = Swift_Mailer::newInstance ( $transport );
    $logger = new Swift_Plugins_Loggers_ArrayLogger();
    $mailer -> registerPlugin ( new Swift_Plugins_LoggerPlugin ( $logger ) );
    $subject = "Dracker Alert - $dracker_name";
    $sender_email = $smtp_senderemail;
    $sender_friendly = $smtp_sendername;
    $reply_to = $smtp_senderemail;
    $message = "Hello,<br> <br>You have a hit!<br><br>Name: <b>$dracker_name</b><br>Source IP: <b>$ip</b><br>Agent: <b>$browser</b><br><br>more details on the Dracker portal. <br><br> $login_url";
    //Create a message
    $message = Swift_Message::newInstance ( $subject )
          -> setSubject ( $subject )
          -> setFrom ( array ( $sender_email => $sender_friendly ) )
          -> setReplyTo ( $reply_to )
          -> setTo ( $send_to )
          -> setBody ( $message, 'text/html' )
        ;
    //Send the message
    $test = $mailer -> send ( $message, $failures );
}
?>
