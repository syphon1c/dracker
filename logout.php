<?php
/**
* Description of logout.php
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


session_start();
$currentsessionid=session_id();
$connection = new DatabaseConnection();
	
$connection->executeQuery("DELETE from loggedin where session ='$currentsessionid'");

session_start();
session_destroy();
session_start();
session_regenerate_id(true);

header("location:login.php");
?>
