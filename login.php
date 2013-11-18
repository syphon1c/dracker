<?php
/**
* Description of login.php
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
include_once 'inc/swiftmailer/lib/swift_required.php';
include_once 'SMTP.php';

ini_set("session.cookie_httponly", 1);

if(isset($_GET['msg'])) {
    $msg=intval($_GET['msg']);
}

// Adds X-Frame-Options to HTTP header, so that page cannot be shown in an iframe.
header('X-Frame-Options: DENY');

// Adds X-Frame-Options to HTTP header, so that page can only be shown in an iframe of the same site.
header('X-Frame-Options: SAMEORIGIN');

session_set_cookie_params($httponly = True);

//Check for IE versions...we dont support anything less than IE10
preg_match('/MSIE (.*?);/', $_SERVER['HTTP_USER_AGENT'], $matches);
if(count($matches)<2){
  preg_match('/Trident\/\d{1,2}.\d{1,2}; rv:([0-9]*)/', $_SERVER['HTTP_USER_AGENT'], $matches);
}
if (count($matches)>1){
  //Then we're using IE
  $version = $matches[1];

  switch(true){
    case ($version<=6):
      //IE 8 or under!
      echo "Unsupported browser! If your going to use Internet Explorer atleast upgrade to version 10";
      exit();
      break;
      
    case ($version<=7):
      //IE 8 or under!
      echo "Unsupported browser! If your going to use Internet Explorer atleast upgrade to version 10";
      exit();
      break;
      
    case ($version<=8):
      //IE 8 or under!
      echo "Unsupported browser! If your going to use Internet Explorer atleast upgrade to version 10";
      exit();
      break;

    //case ($version==9 || $version==10):
    case ($version==9):
      //IE9 & IE10!
      echo "Unsupported browser! If your going to use Internet Explorer atleast upgrade to version 10";
      exit();
      break;

    default:
  }
}

//Here we check the logged in users table for sessions 
//older than 20minutes, anything older gets removed
$date = date('Y-m-d H:i:s');
$time=time();
$time_check=$time-3600; //60 minutes 

// if over 20 minutes and no new activity we remove the session from DB
$end_session_time = mysql_query("DELETE FROM loggedin WHERE time<$time_check");

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

//Password Reset Requests
if(isset($_POST['email'])) {
    $email_account=$_POST['email'];
    $email_account=sanitize($email_account);
    $email_user = "";
    
    $time = time(); 
    
    $connection = new DatabaseConnection();
    $queryUserAccount = $connection->executeQuery("SELECT * FROM user_account WHERE email = '$email_account'");
    while ($rowAccountDetails = mysql_fetch_array($queryUserAccount)){
        $uid=$rowAccountDetails['id'];
        $cid=$rowAccountDetails['clientid'];
        $username=$rowAccountDetails['username'];
        $name=$rowAccountDetails['name'];
        $surname=$rowAccountDetails['surname'];
        $set_email=$rowAccountDetails['email'];
        
        $new_key=random_string(35);
        if($set_email != ""){
            $connection->executeQuery("INSERT INTO reset_accounts VALUES(NULL, '$set_email', '$uid', '$cid', '$new_key', '$time')");
            $email_user = "proceed";
            
            $connection = new DatabaseConnection();
            $ipaddress = getenv(REMOTE_ADDR);
            $message = "Email Password Reset Request:<b> " . $set_email . "</b> has requested a password reset!<br>
                        Username: <b>" . $username . "</b><br>
                        Source IP Address is: <b>" . $ipaddress . "</b>";

            $connection->executeQuery("INSERT INTO Logs VALUES(NULL, 'Notice', 'New', '$username', 'password_reset.php', '$message', NULL)");
            $connection->closeConnection();
        }else{
        //do nothing
        }
    
    if($email_user == "proceed"){
        $current_url = curPageURL();
        $reset_url = str_replace("login.php?msg=2",'account_reset.php', $current_url);
        $connection = new DatabaseConnection();
        //Create the Mailer using your created Transport
        $mailer = Swift_Mailer::newInstance ( $transport );
        //To use the ArrayLogger
        $logger = new Swift_Plugins_Loggers_ArrayLogger();
        $mailer -> registerPlugin ( new Swift_Plugins_LoggerPlugin ( $logger ) );
        //Prep message
        $subject = "Dracker - Password Reset";
        $sender_email = $smtp_senderemail;
        $sender_friendly = $smtp_sendername;
        $reply_to = $smtp_senderemail;
        $message = "Hello " . $name . ' ' . $surname . ",<br> <br>You have requested a password reset<br><br>
                    To continue with the password reset for user <b>" . $username . "</b> follow the below link:<br><br>"
                   . $reset_url . "?key=" . $new_key . "<br><br>If you did not request the password reset to your
                    account, ignore this email. This link will expire within 1 hour!";
        //Create a message
        $message = Swift_Message::newInstance ( $subject )
                -> setSubject ( $subject )
                -> setFrom ( array ( $sender_email => $sender_friendly ) )
                -> setReplyTo ( $reply_to )
                -> setTo ( array ( $set_email => $name . ' ' . $surname) )
                -> setBody ( $message, 'text/html' )
            ;
        //Send the message
        $test = $mailer -> send ( $message, $failures );
        //store logs in database
    }
    }
}

if(isset($_POST['email_reset'])) {
    $email_account=$_POST['email_reset'];
    $password_reset=$_POST['mypassword_reset'];
    $key_reset=$_POST['key_reset'];
    
    $email_account=sanitize($email_account);
    $key_reset=sanitize($key_reset);
    
    $passmd5 = md5($password_reset);
    
    $connection = new DatabaseConnection();
    $queryResetAccount = $connection->executeQuery("SELECT uid FROM reset_accounts WHERE email = '$email_account' AND key_token = '$key_reset'");
    while ($rowResetAccount = mysql_fetch_array($queryResetAccount)){
        $uid=$rowResetAccount['uid'];
        $cid=$rowResetAccount['cid'];
        
        $connection->executeQuery("UPDATE user_account SET password = '$passmd5' WHERE id = '$uid' AND email = '$email_account'");
        $connection->executeQuery("DELETE FROM reset_accounts WHERE key_token = '$key_reset'");
    }
}


function random_string($length) {
    $key = '';
    $keys = array_merge(range(0, 9), range('a', 'z'));

    for ($i = 0; $i < $length; $i++) {
        $key .= $keys[array_rand($keys)];
    }

    return $key;
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

?>
<html>
 <head>
   <meta charset="utf-8"> 
   <title>Track and Trace Documents</title>
   <link href="css/login.css" rel="stylesheet" media="screen">
   <link href="css/bootstrap-modal.css" rel="stylesheet">
   <link href="css/bootstrap-responsive.css" rel="stylesheet">
 </head>
   
<body>

<div class="navbar navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container">
      <a data-target=".nav-collapse" data-toggle="collapse" class="btn btn-navbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>
      <a href="#" class="brand">Track and Trace Documents</a> 
      <div class="nav-collapse">
      </div>
    </div>
  </div>
</div>

<div class="header"></div>

<div class="main">
    <div class="mid-container wrapper">
  <h1 class="title">Dracker</h1>

<form name="login" id="login"class="form-signin" method="post" action="checklogin.php">
    <ul class="fields">
      <li>
        <input autocomplete="off" id="myusername" name="myusername" placeholder="username" type="text" />
      </li>
      <li>
        <input autocomplete="off" id="mypassword" name="mypassword" placeholder="password" type="password" />
      </li>
    </ul>
    <? 
    if($msg=="1"){
        echo "<br><b>Incorrect username or password!</b>";
    }elseif($msg=="2"){
        echo "<br><b>Password reset sent to email address!</b>";
    }elseif($msg=="3"){
        echo "<br><b>Password reset, try login!</b>";
    }
    ?>
    <div class="mid-form">
      <input class="btn-flat inverse large pull-right" name="commit" type="submit" value="Sign In" />
     	<br> 
    </div>
  </form>
   <a data-toggle="modal" class="btn-flat small gray pull-right" href="password_reset.php" data-target="#ResetPass" data-width="500">Reset Password</a>
   <br>
</div>
</div>
</div>

<div class="modal container hide fade" id="ResetPass" tabindex="-1" role="dialog" aria-labelledby="ResetPassLabel" aria-hidden="true">

    <div class="modal-body">
        <p>Loading...</p>
    </div>
</div>
</body>

<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
<script type="text/javascript" src="js/bootstrap-modal/bootstrap-modal.js"></script>
<script type="text/javascript" src="js/bootstrap-modal/bootstrap-modalmanager.js"></script>

</html>

