<?php
/**
* Description of password_reset.php
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
 
$include_allowed = true;

?>

 <div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
  <h4 id="edituserModalLabel">Reset Account Password</h4>
 </div>

<div class="well">

<form method="POST" action="login.php?msg=2">
<div class="row-fluid">
   <div class="span7">
        <ul class="fields">
            <li>
                <input autocomplete="off" type="text" placeholder="email address"  id="email" name="email">
            </li>
        </ul>
   </div>
</div>
<br><br>
 <div class="modal-footer">
    <button class="btn-flat gray" data-dismiss="modal" aria-hidden="true">Cancel</button>
    <button class="btn-flat success" >Reset </button>
 </div>

</form>
</div> 

