<?php
/**
* Description of user_add.php
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
    <h5 id="edituserModalLabel">Add new User </h5>
</div>

<div class="well">
    <form method="POST" action="settings.php">
        <label>Name
        <input type="text" value="" class="input-medium" autocomplete="off" id="name" name="name" required>
        Surname
        <input type="text" value=""class="input-medium" autocomplete="off" id="surname" name="surname" required></label>
        </label>
        
        <label>Username
        <input type="text" value="" class="input-medium" autocomplete="off" id="username" name="username"></label>
        
        <label>Password
        <input type="text" value="" class="input-medium" autocomplete="off" id="password" name="password"></label>
        
        <label>Email
        <input type="text" value="" class="input-large" autocomplete="off" id="email" name="email" required></label>
        
        <br>

        <div class="modal-footer">
            <button class="btn-flat gray" data-dismiss="modal" aria-hidden="true">Cancel</button>
            <button class="btn-flat primary" >Add User</button>
        </div>
    </form>
</div>


