<?php
/**
* Description of settings.php
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
include "menu.php";

$sessionid=sanitize($sessionid);
$connection = new DatabaseConnection();
$row = $connection->getRole($sessionid);

if($row != FALSE){
}
if($row == FALSE){
header("location:login.php");
}

if(isset($_POST['upname'])) {
    $upemail=$_POST['upemail'];
    $upname=$_POST['upname'];
    $upsurname=$_POST['upsurname'];
    // To protect MySQL injection (more detail about MySQL injection)
    $upemail=sanitize($upemail);
    $upname=sanitize($upname);
    $upsurname=sanitize($upsurname);
    // End of protection

    $connection = new DatabaseConnection();

    $queryLoggedinDetail = $connection->executeQuery("SELECT uid FROM loggedin WHERE session = '$sessionid'");
        while ($rowLoggedinID = mysql_fetch_array($queryLoggedinDetail)){
            $uid=$rowLoggedinID['uid'];
        }

    $connection->executeQuery("UPDATE user_account SET name = '$upname', surname='$upsurname', email = '$upemail' WHERE id = '$uid'");
    $connection->closeConnection();
    $message = "Your profile has been updated!";
}



if(isset($_POST['username'])) {
    $username=$_POST['username'];
    $password=$_POST['password'];
    $email=$_POST['email'];
    $name=$_POST['name'];
    $surname=$_POST['surname'];
    // To protect MySQL injection (more detail about MySQL injection)
    $username=sanitize($username);
    $email=sanitize($email);
    $name=sanitize($name);
    $surname=sanitize($surname);
    // End of protection

    $passmd5 = md5($password);

    $connection = new DatabaseConnection();

    $connection->executeQuery("INSERT INTO user_account VALUES(NULL, '$username', '$passmd5', '$name', '$surname', '$email', '', '')");
    $connection->closeConnection();
    $message = "The user - '$username' - has been created!";
}

if(isset($_POST['removeuser'])) {
    $uid=intval($_POST['removeuser']);
    $uid=stripslashes($uid);
    $uid=mysql_real_escape_string($uid);

    $connection = new DatabaseConnection();
    $connection->executeQuery("DELETE FROM user_account WHERE id = {$uid}");
    $connection->closeConnection();

    $message = "User has been removed!";
}

?>

<head>
     <link href="css/bootstrap.css" rel="stylesheet">
     <link href="css/bootstrap-modal.css" rel="stylesheet">
     <link href="css/bootstrap-responsive.css" rel="stylesheet">
     <link href="css/DT_bootstrap.css" rel="stylesheet">
     <link href="css/base-admin.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span2">
                <div class="sidebar-nav">
                    <div class="well" style="width:200px; padding: 1px 0;">
                        <ul class="nav nav-list">
                            <li class="nav-header">Dracker Menu</li>
                            <li ><a href="view_results.php"><i class="icon-search"></i> Traces</a></li>
                            <li class="active"><a href="settings.php"><i class="icon-wrench"></i> Settings</a></li>
                        </ul>
                    </div>
                </div>
            </div>
<div class="span10">
                <div class="navbar navbar-inverse">
                    <div class="navbar-inner">
                        <div class="container-fluid">
                        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </a>
                        <a class="brand2" href="#" name="top">Settings </a>
                        <div class="nav-collapse collapse">
                        </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="span10">
               
<div class="span3">
    <div class="widget">
        <div class="widget-header">
            <i class="icon-envelope"></i>
            <h3>SMTP Server</h3>
        </div>
        <div class="widget-content">
        <?php
         $connection = new DatabaseConnection();
         $querySMTPdefault = "SELECT * FROM settings_smtp WHERE sys_default= 1";
         $smtpArray = $connection->executeQuery($querySMTPdefault);
         while($smtpItems = mysql_fetch_array($smtpArray) ){
             $sid = $smtpItems['id'];
	     $default = $smtpItems['sys_default'];
	     }
             if($default ==1){
                 echo '<br>&nbsp;&nbsp;&nbsp;<span class="label label-success">SMTP Server is setup</span>
		       <br><br> 
		       &nbsp;&nbsp;&nbsp;<a class="btn-flat small" data-toggle="modal" href="smtp_test.php" data-target="#remoteLoad"><i class="icon-envelope icon-white"></i> Send Test</a>
                       &nbsp;&nbsp;&nbsp;<a class="btn-flat small" data-toggle="modal" href="edit_smtp.php" data-target="#remoteLoad"><i class="icon-pencil icon-white"></i> Edit SMTP</a>
                       <br><br>';
             }else{
	     echo '<br>&nbsp;&nbsp;&nbsp;<span class="label label-important">SMTP Server is not set</span><br>
                   <br> &nbsp;&nbsp;&nbsp;<a class="btn-flat small" data-toggle="modal" href="smtp_admin.php" data-target="#remoteLoad"><i class="icon-pencil icon-white"></i> Set SMTP</a>
                   <br><br>';
             }
          
         ?>
          </div> <!-- /widget-content -->
        </div> <!-- /widget -->
      </div>
<div class="span7">
    <div class="widget">
        <div class="widget-header">
            <i class="icon-user"></i>
            <h3>Users</h3>
        </div>
        <div class="widget-content">
        <br> &nbsp;&nbsp;&nbsp;<a class="btn-flat info small" data-toggle="modal" href="user_add.php" data-target="#remoteLoad"><i class="icon-pencil icon-white"></i> Add User</a>&nbsp;&nbsp;&nbsp;<a class="btn-flat small" data-toggle="modal" href="user_edit_profile.php" data-target="#remoteLoad"><i class="icon-user icon-white"></i> Edit Current Profile</a><br><br>
	
		<table  id="vmTables1" class="table table-striped table-bordered">
                    <thead>
                       <tr>
                         <th width="80">User</th>
                         <th>Full Name</th>
                         <th>Email</th>
                         <th width="30"></th>
                       </tr>
                    </thead>
               <tbody>
                    <?
                    $connection = new DatabaseConnection();

                    $queryUserAccounts = $connection->executeQuery("SELECT * FROM user_account");
                    while ($rowUserAccounts = mysql_fetch_array($queryUserAccounts)){
                        $uid = $rowUserAccounts['id'];
                        $username = $rowUserAccounts['username'];
                        $name = $rowUserAccounts['name'];
                        $surname = $rowUserAccounts['surname'];
                        $email = $rowUserAccounts['email'];

			echo "<tr><td align=\"center\" >$username</td><td>$name $surname</td><td>$email</td>";
			if($username != "admin"){
                            echo "<td><span class=\"label label-important\"><a id=\"delUsers\"  href=\"users_delete.php?uid=$uid\"  rel=\"tooltip\" title=\"Delete User\" data-toggle=\"modal\" data-target=\"#remoteLoad\" data-width=\"500\"><i class=\"icon-trash\"></i></a></span></td>";
			}else{
			    echo "<td></td>";
			}
			echo "</tr>";
	            }	
                   ?>
		</tbody>
            </table>


	</div> <!-- /widget-content -->
        </div> <!-- /widget -->
      </div>

     </div>

</div>
</div>

<div class="modal small hide fade" id="remoteLoad" tabindex="-1" role="dialog" aria-labelledby="delDrackEntryLabel" aria-hidden="true">
    <div class="modal-body">
        <p>Loading...</p>
    </div>
</div>

<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
<script type="text/javascript" src="js/bootstrap-modal.js"></script>
<script type="text/javascript" src="js/bootstrap-modalmanager.js"></script>
<script type="text/javascript" src="js/jquery.dataTables.js"></script>
<script type="text/javascript" src="js/DT_bootstrap.js"></script>
<script>
$(function(){
    $('a[rel=popover]')
    .popover({
        placement : 'left',
        trigger : 'hover'
    })
    .on("hover", function(){
        $('.popover').addClass($(this).data("class"));
    });
});

</script>

<script type='text/javascript'>
$(document).ready(function() {
    $('#vmTables1').dataTable( {
        "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
        "sPaginationType": "bootstrap",
        "aaSorting": [[0,'desc']],
        "iDisplayLength": 10,
        "oLanguage": {
            "sLengthMenu": "_MENU_ items per page "
        }
    });
});
</script>

