<?php
/**
* Description of smtp_admin.php
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

?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h5 id="edituserModalLabel">Add SMTP Server </h5>
</div>

<div class="well">
    <form method="POST" action="set_smtp.php">
        <label>Host
        <input type="text" value="" class="input-large" autocomplete="off" id="host" name="host" required>
        Port
        <input type="text" value=""class="input-small" autocomplete="off" id="port" name="port" required></label>
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
        <input type="text" value="" class="input-large" autocomplete="off" id="username" name="username"></label>
        
        <label>Password
        <input type="text" value="" class="input-large" autocomplete="off" id="password" name="password"></label>
        
        <label>From Email
        <input type="text" value="no-reply@myemailaddies.org.com.za" class="input-large" autocomplete="off" id="sender_email" name="sender_email" required></label>
        
        <label>Senders Name
        <input type="text" value="Dracker Alerts" class="input-large" autocomplete="off" id="sender_name" name="sender_name" required></label>

        <input type="hidden" name="default" id="default" value="1">
        </label>
        <br>

        <div class="modal-footer">
            <button class="btn-flat gray" data-dismiss="modal" aria-hidden="true">Cancel</button>
            <button class="btn-flat primary" >Add Host</button>
        </div>
    </form>
</div>


