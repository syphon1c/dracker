<?php
/**
* Description of SMTP.php
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

$connection = new DatabaseConnection();

$querySMTPSettings = $connection->executeQuery("SELECT host, port, ssl_enc, username, aes_decrypt(password, '$encrypt_key') as password, sender_email, sender_name, sys_default FROM settings_smtp WHERE sys_default = \"1\"");
while ($rowSMTP = mysql_fetch_array($querySMTPSettings)){
    $smtp_host = $rowSMTP['host'];
    $smtp_port = $rowSMTP['port'];
    if(isset($rowSMTP['ssl_enc']) && $rowSMTP['ssl_enc'] == 1){
        $ssl = 'yes';
    }else{
        $ssl = 'no';
    }
    if(strlen($rowSMTP['username'])){
        $smtp_username = $rowSMTP['username'];
    }
    if(strlen($rowSMTP['password'])){
        $smtp_password = $rowSMTP['password'];
    }
    $smtp_senderemail = $rowSMTP['sender_email'];
    $smtp_sendername = $rowSMTP['sender_name'];
}

if ( isset ( $smtp_host ) AND isset ( $smtp_username ) AND isset ( $smtp_password ) ) {
        if ( ! isset ( $smtp_port ) ) {
            $smtp_port = 25;
        }
        if($ssl == "no"){
            $transport = Swift_SmtpTransport::newInstance ( $smtp_host, $smtp_port )
                -> setUsername ( $smtp_username )
                -> setPassword ( $smtp_password )
            ;
        }else{
            $transport = Swift_SmtpTransport::newInstance ( $smtp_host, $smtp_port, 'tls' )
                -> setUsername ( $smtp_username )
                -> setPassword ( $smtp_password )
            ;
        }
    }
    if ( isset ( $smtp_host ) AND ! isset ( $smtp_username ) AND ! isset ( $smtp_password ) ) {
        if($ssl == "no"){
            $transport = Swift_SmtpTransport::newInstance ( $smtp_host, $smtp_port );
        }else{
            $transport = Swift_SmtpTransport::newInstance ( $smtp_host, $smtp_port, 'tls' );
        }
    }
