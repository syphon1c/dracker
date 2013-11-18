<?php
/**
* Description of users_delete.php
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

if(isset($_GET['uid'])) {
$uid=$_GET['uid'];
	}

?>

<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button><h3 id="deluserModalLabel">Remove User</h3></div>
<div class="well">
<form action="settings.php" method="POST"> 
<p class="error-text">Are you sure you want to delete the user?</p>
 <div class="modal-footer">
  <form action="settings.php" method="POST">
<input type="hidden" value="<?echo $uid?>" id="removeuser" name="removeuser">
  <button class="btn-flat gray" data-dismiss="modal" aria-hidden="true">Cancel</button>
  <button class="btn-flat danger" >Delete</button>
 </div>
</form>
</div>



