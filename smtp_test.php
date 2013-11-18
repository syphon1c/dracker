<?php
/**
* Description of smtp_test.php
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
include_once 'inc/swiftmailer/lib/swift_required.php';
include_once 'SMTP.php';

$include_allowed = true;

session_start();
$sessionid= session_id();
//Here we fetch the admins details and email address from DB to send test email to
$connection = new DatabaseConnection();
$queryUserSession = $connection->executeQuery("SELECT uid, username FROM loggedin WHERE session = '$sessionid'");
while ($rowSession = mysql_fetch_array($queryUserSession)){
    $uid = $rowSession['uid'];
    $loggedinusername = $rowSession['username']; 
    
    $queryUserDetails = $connection->executeQuery("SELECT email, name, surname FROM user_account WHERE id ='$uid' AND username ='$loggedinusername'");
    while ($rowUser = mysql_fetch_array($queryUserDetails)){
        $admin_email = $rowUser['email'];
        $admin_name = $rowUser['name'];
        $admin_surname = $rowUser['surname'];
    }
} 

if(isset($_GET['sid'])) {
     $sid=intval($_GET['sid']);
}

//
//smtp stuff here
//

    //Create the Mailer using your created Transport
    $mailer = Swift_Mailer::newInstance ( $transport );
    //To use the ArrayLogger
    $logger = new Swift_Plugins_Loggers_ArrayLogger();
    $mailer -> registerPlugin ( new Swift_Plugins_LoggerPlugin ( $logger ) );
    //Prep message
    $subject = "Test Message";
    $sender_email = $smtp_senderemail;
    $sender_friendly = $smtp_sendername;
    $reply_to = $smtp_senderemail;
    $message = "Hello,<br> <br>This is a test email you just sent!<br><br> Seems like its working";
    //Create a message
    $message = Swift_Message::newInstance ( $subject )
            -> setSubject ( $subject )
            -> setFrom ( array ( $sender_email => $sender_friendly ) )
            -> setReplyTo ( $reply_to )
            -> setTo ($admin_email )
            -> setBody ( $message, 'text/html' )
        ;
    //Pre stage alert message in case something happens
    $_SESSION['alert_message'] = "Check your email...";
    //Send the message
    $test = $mailer -> send ( $message, $failures );

?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 id="edituserModalLabel">Test Mail</h4>
</div>

<div class="well">
    <form method="POST" action="smtp_add.php">
        
        <?php echo $_SESSION['alert_message'];?>

        <div class="modal-footer">
            <button class="btn-flat gray" data-dismiss="modal" aria-hidden="true">Close</button>
        </div>
    </form>
</div>


