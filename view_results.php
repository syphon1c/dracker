<?php
/**
* Description of view_results.php
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
include "menu.php";

$sessionid=sanitize($sessionid);
$connection = new DatabaseConnection();
$row = $connection->getRole($sessionid);

if($row != FALSE){
}
if($row == FALSE){
header("location:login.php");
}

$connection = new DatabaseConnection();
$queryLoggedinDetail = $connection->executeQuery("SELECT uid FROM loggedin WHERE session = '$sessionid'");
    while ($rowLoggedinID = mysql_fetch_array($queryLoggedinDetail)){
        $userid=$rowLoggedinID['uid'];
	
	$queryUserEmail = $connection->executeQuery("SELECT email FROM user_account WHERE id = '$userid'");	
        while ($rowUserEmail = mysql_fetch_array($queryUserEmail)){
	    $uemail=$rowUserEmail['email'];
        }
    }

if(isset($_POST['FileName'])) {
    $FileName=$_POST['FileName'];
    $Desc=$_POST['Desc'];
    $Email=$_POST['Email'];
    $Desc=sanitize($Desc);
    $Email=sanitize($Email);
    $FileName=sanitize($FileName);

$new_docid=random_string(21);

$create_time = date("y-m-d h:i:s");

$connection = new DatabaseConnection();
$queryDocumentDracks = $connection->executeQuery("INSERT INTO dracker_file VALUES (NULL, '$FileName', 'Waiting', '$Desc', '$Email', '$new_docid', '$create_time')");

}

if(isset($_POST['delrefid'])) {
    $refid=$_POST['delrefid'];
    $refid=sanitize($refid);
    $connection = new DatabaseConnection();
    $queryDocumentDracks = $connection->executeQuery("DELETE FROM dracker_file WHERE RefID = '$refid' ");
    $queryDocumentDracksCall = $connection->executeQuery("DELETE FROM call_home WHERE RefID = '$refid' ");
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

<head>
	<link href="css/bootstrap.css" rel="stylesheet">
	<link href="css/bootstrap-responsive.css" rel="stylesheet">
	<link href="css/DT_bootstrap.css" rel="stylesheet"> 
</head>

<body>
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span2">
                <div class="sidebar-nav">
                    <div class="well" style="width:200px; padding: 1px 0;">
                        <ul class="nav nav-list">
                            <li class="nav-header">Dracker Menu</li>
                            <li class="active"><a href="view_results.php"><i class="icon-search"></i> Traces</a></li>
                            <li><a href="settings.php"><i class="icon-wrench"></i> Settings</a></li>
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
                        <a class="brand2" href="#" name="top">Dracker Traces </a>
                        <div class="nav-collapse collapse">
                            <div class="pull-right">
                                <ul class="nav">
                                    <li class="divider-vertical"></li>
                                    <li><a data-toggle="modal" href="#" data-target="#CreateDrack" data-width="700"><i class="icon-file icon-white"></i> New Drack</a></li>
                                </ul>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="span10">
                <table class="table table-striped table-bordered" id="vmTables1">
                    <thead>
                        <tr>
                            <th width="150">File</th>
                            <th width="30">Status</th>
                            <th width="200">Description</th>
                            <th width="100">Email</th>
                            <th width="70">Date</th>
                            <th width="50"></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                       $connection = new DatabaseConnection();
                       $queryDocumentDracks = $connection->executeQuery("SELECT DATE_FORMAT(time,'%e %b %y %H:%i') AS time, FileName, Status, Description, Email, id, RefID FROM dracker_file");
                       while ($rowDocDracks = mysql_fetch_array($queryDocumentDracks)){
	                       $name=$rowDocDracks['FileName'];
			       $status=$rowDocDracks['Status'];
			       $desc=$rowDocDracks['Description'];
			       $email=$rowDocDracks['Email'];
			       $time=$rowDocDracks['time'];
			       $id=$rowDocDracks['id'];
			       $refid=$rowDocDracks['RefID'];
			       
			       if($status == 'Opened'){$status_label = "<span class=\"label label-important\" style=\"background-color: #ff3333\">Opened</span>";}
    		   	       else if ($status == 'Waiting'){$status_label = "<span class=\"label label-important\" style=\"background-color: #3399CC\">Active</span>";}

                    ?>
		<tr>
                    <td><? echo $name; ?></td>
                    <td><? echo $status_label; ?></td> 
                    <td><? echo $desc; ?></td>
                    <td><? echo $email ?></td>
                    <td><? echo $time ?></td>
                    <td><span class="label label-info"><a id="viewdetails" href="results.php?refid=<?php echo $refid ?>" rel="tooltip" title="View Details"><i class="icon-th-large"></i></a></span>
		        <span class="label label-success"><a id="download" href="download_dracker_doc.php?refid=<?php echo $refid ?>" rel="tooltip" title="Download Document"><i class="icon-file"></i></a></span>
		        <span class="label label-important"><a id="delID" data-toggle="modal" data-target="#DeleteDrack" href="delete_dracker.php?refid=<? echo $refid ?>"  rel="tooltip" title="Delete"><i class="icon-trash"></i></a></span>
                    </td>
		<?php
		    }
		?>
               </tr>
			</tbody>
		</table>
	</div>

</div>
</div>

<div class="modal small hide fade" id="CreateDrack" tabindex="-1" role="dialog" aria-labelledby="newDrackLabel" aria-hidden="true">
 <div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
  <h3 id="myModalLabel">Create New Tracking File</h3>
 </div>

<div class="well">

<form method="POST" action="view_results.php">
   <label>File name</label>
    <input type="text" class="input-large" autocomplete="off" id="FileName" name="FileName">
   <label>Email</label>
    <input type="text" class="input-large" autocomplete="off" id="Email" name="Email" value="<? echo $uemail;?>">
   <label>Description</label>
    <textarea value="Desc" id="Desc" name="Desc" rows="3" class="input-xlarge">Description of track...</textarea>

 <div class="modal-footer">
  	<button class="btn-flat gray" data-dismiss="modal" aria-hidden="true">Cancel</button>
 	<button class="btn-flat primary" >Create</button>
 </div>
</form>
</div>
</div>
</div>

<div class="modal small hide fade" id="DeleteDrack" tabindex="-1" role="dialog" aria-labelledby="delDrackLabel" aria-hidden="true">
    <div class="modal-body">
        <p>Loading...</p>
    </div>
</div>


<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
<script type="text/javascript" src="js/jquery.dataTables.js"></script>
<script type="text/javascript" src="js/DT_bootstrap.js"></script>


<script type='text/javascript'>
$(document).ready(function() {
    $('#vmTables1').dataTable( {
        "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
        "aaSorting": [[0,'asc']],
        "sPaginationType": "bootstrap",
        "iDisplayLength": 13,
        "oLanguage": {
            "sLengthMenu": "_MENU_ items per page "
        }
    });
}); 
</script>
