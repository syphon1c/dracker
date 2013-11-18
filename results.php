<?php
/**
* Description of results.php
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

if(isset($_GET['refid'])) {
    $refid=$_GET['refid'];
    $refid=sanitize($refid);

    $connection = new DatabaseConnection();
    $queryDocumentDracks = $connection->executeQuery("SELECT FileName FROM dracker_file WHERE RefID = '$refid'");
    while ($rowDocDracks = mysql_fetch_array($queryDocumentDracks)){
        $file_name=$rowDocDracks['FileName'];
    }
}

if(isset($_POST['logid'])) {
    $log_id=$_POST['logid'];
    $log_id=sanitize($log_id);
    $connection = new DatabaseConnection();
    $queryDocumentDracksCall = $connection->executeQuery("DELETE FROM call_home WHERE log_id = '$log_id' ");
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
                        <a class="brand2" href="#" name="top">Dracker Results - <?echo $file_name; ?> </a>
                        <div class="nav-collapse collapse">
                            <div class="pull-right">
                                <ul class="nav">
                                    <li class="divider-vertical"></li>
                                    <li><a href="javascript:history.go(-1)"><i class="icon-arrow-left icon-white"></i> Back</a></li>
                                </ul>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="span10">
                Should you want to embed the link used to track this particular configuration into another application or web page the link used is:<br><br>
	    <?
	    $current_url = curPageURL();
	    $track_url = str_replace("results.php?refid=$refid","images.php?imageid=$refid", $current_url);
	    echo "<b>".$track_url."</b>";
	    ?><br><br>

                <table class="table table-striped table-bordered" id="vmTables1">
                    <thead>
                        <tr>
			    <th width="110">Time</th>
                            <th width="100">IP Address</th>
                            <th >Hostname</th>
                            <th width="100">Proxied IP</th>
                            <th width="150">Browser</th>
                            <th width="150">OS</th>
                            <th width="50"></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                       $connection = new DatabaseConnection();
                       $queryDocumentHits = $connection->executeQuery("SELECT DATE_FORMAT(time,'%e %b %y %H:%i') AS time, SourceIP, HostResolve, Proxied_IP, Browser, OS, log_id, Country, Region, City, Organisation FROM call_home WHERE RefID = '$refid' ORDER BY time DESC");
                       while ($rowDocHits = mysql_fetch_array($queryDocumentHits)){
	                       $time=$rowDocHits['time'];
			       $ip_address=$rowDocHits['SourceIP'];
			       $host_res=$rowDocHits['HostResolve'];
			       $proxy_ip=$rowDocHits['Proxied_IP'];
			       $browser=$rowDocHits['Browser'];
			       $browserdetail=getBrowser($browser);
			       $operating_sys=getOS($browser);
			       $logid=$rowDocHits['log_id'];
			       $country=$rowDocHits['Country'];
                               $region=$rowDocHits['Region'];
                               $city=$rowDocHits['City'];
                               $organisation=$rowDocHits['Organisation'];

			       $array = array_values($browserdetail);
			       
		    ?>
		<tr>
		    <td><? echo $time; ?></td>
                    <td><a href="#" id="location" name="location<?php echo $logid;?>" class="btn-flat mini btn-info" rel="popover" data-content="<?echo $country;?>, <?echo $region;?>, <?echo $city;?> - <?echo $organisation;?>" data-original-title="IP Location"><? echo $ip_address; ?></a></td>
                    <td><span class="label label-warning"><? echo $host_res; ?></span></td> 
                    <td><? echo $proxy_ip; ?></td>
                    <td><a href="#" id="browser" name="<?php echo $logid;?>" class="btn-flat mini btn-info" rel="popover" data-content="<?echo $browser;?>" data-original-title="Browser Details"><? echo $array[1] . " " . $array[2]?></td>
                    <td><? echo $operating_sys ?></td>
                    <td>
                    <span class="label label-important"><a id="delID" data-toggle="modal" data-target="#DeleteDrackEntry" href="delete_dracker_entry.php?refid=<? echo $refid ?>&logid=<? echo $logid ?>"  rel="tooltip" title="Delete"><i class="icon-trash"></i></a></span>
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

<div class="modal small hide fade" id="DeleteDrackEntry" tabindex="-1" role="dialog" aria-labelledby="delDrackEntryLabel" aria-hidden="true">
    <div class="modal-body">
        <p>Loading...</p>
    </div>
</div>

<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
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
        "iDisplayLength": 13,
        "oLanguage": {
            "sLengthMenu": "_MENU_ items per page "
        }
    });
}); 
</script>

<?php
function getOS($userAgent) {
  // Feel free to update and add to the list 
        $oses = array (
                'iPhone' => '(iPhone)',
                'Windows 3.11' => 'Win16',
                'Windows 95' => '(Windows 95)|(Win95)|(Windows_95)', 
                'Windows 98' => '(Windows 98)|(Win98)',
                'Windows 2000' => '(Windows NT 5.0)|(Windows 2000)',
                'Windows XP' => '(Windows NT 5.1)|(Windows XP)',
                'Windows 2003' => '(Windows NT 5.2)',
                'Windows Vista' => '(Windows NT 6.0)|(Windows Vista)',
		//'Windows Server 2008' => '(Windows NT 6.1)|(WOW)',	
                'Windows 7 64bit' => '(Windows NT 6.1; WOW64)',
                'Windows 7' => '(Windows NT 6.1)|(Windows 7)',
		'Windows 8' => '(Windows NT 6.2)|(Windows 8)',
                'Windows 8.1' => '(Windows NT 6.3)|(Windows 8.1)',
                'Windows NT 4.0' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
                'Windows ME' => 'Windows ME',
                'Open BSD'=>'OpenBSD',
                'Sun OS'=>'SunOS',
                'Linux'=>'(Linux)|(X11)',
                'Safari' => '(Safari)',
		'Mac OS X 10.8'=>'Mac OS X 10.8',
                'Mac OS X 10.7'=>'Mac OS X 10.7',
                'Mac OS X 10.6'=>'Mac OS X 10.6',
                'Mac OS X 10.5'=>'Mac OS X 10.5',
		'Mac OS X Intel'=>'Intel Mac OS X',
                'QNX'=>'QNX',
                'BeOS'=>'BeOS',
                'OS/2'=>'OS/2',
                'Search Bot'=>'(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp/cat)|(msnbot)|(ia_archiver)'
        );

        foreach($oses as $os=>$pattern){ 
                if(eregi($pattern, $userAgent)) { 
                        return $os; 
                }
        }
        return 'Unknown'; // Cannot find operating system so return Unknown
}

function getBrowser($AgentBrowser) {
   // if (isset($_SERVER["HTTP_USER_AGENT"]) OR ($_SERVER["HTTP_USER_AGENT"] != "")) {
        $visitor_user_agent = $AgentBrowser;
   // } else {
   //     $visitor_user_agent = "Unknown";
    //}
    $bname = 'Unknown';
    $version = "0.0.0";
 
    // Next get the name of the useragent yes seperately and for good reason
    if (eregi('MSIE', $visitor_user_agent) && !eregi('Opera', $visitor_user_agent)) {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    } elseif (eregi('Firefox', $visitor_user_agent)) {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    } elseif (eregi('Chrome', $visitor_user_agent)) {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    } elseif (eregi('Safari', $visitor_user_agent)) {
        $bname = 'Apple Safari';
        $ub = "Safari";
    } elseif (eregi('Opera', $visitor_user_agent)) {
        $bname = 'Opera';
        $ub = "Opera";
    } elseif (eregi('Netscape', $visitor_user_agent)) {
        $bname = 'Netscape';
        $ub = "Netscape";
    } elseif (eregi('Seamonkey', $visitor_user_agent)) {
        $bname = 'Seamonkey';
        $ub = "Seamonkey";
    } elseif (eregi('Konqueror', $visitor_user_agent)) {
        $bname = 'Konqueror';
        $ub = "Konqueror";
    } elseif (eregi('Navigator', $visitor_user_agent)) {
        $bname = 'Navigator';
        $ub = "Navigator";
    } elseif (eregi('Mosaic', $visitor_user_agent)) {
        $bname = 'Mosaic';
        $ub = "Mosaic";
    } elseif (eregi('Lynx', $visitor_user_agent)) {
        $bname = 'Lynx';
        $ub = "Lynx";
    } elseif (eregi('Amaya', $visitor_user_agent)) {
        $bname = 'Amaya';
        $ub = "Amaya";
    } elseif (eregi('Omniweb', $visitor_user_agent)) {
        $bname = 'Omniweb';
        $ub = "Omniweb";
    } elseif (eregi('Avant', $visitor_user_agent)) {
        $bname = 'Avant';
        $ub = "Avant";
    } elseif (eregi('Camino', $visitor_user_agent)) {
        $bname = 'Camino';
        $ub = "Camino";
    } elseif (eregi('Flock', $visitor_user_agent)) {
        $bname = 'Flock';
        $ub = "Flock";
    } elseif (eregi('AOL', $visitor_user_agent)) {
        $bname = 'AOL';
        $ub = "AOL";
    } elseif (eregi('AIR', $visitor_user_agent)) {
        $bname = 'AIR';
        $ub = "AIR";
    } elseif (eregi('Fluid', $visitor_user_agent)) {
        $bname = 'Fluid';
        $ub = "Fluid";
    } elseif (eregi('Word', $visitor_user_agent)) {
        $bname = 'Word 4 Mac';
        $ub = "Word";
    }
      else {
        $bname = 'Unknown';
        $ub = "Unknown";
    }
 
    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
            ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $visitor_user_agent, $matches)) {
        // we have no matching number just continue
    }
 
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($visitor_user_agent, "Version") < strripos($visitor_user_agent, $ub)) {
            $version = $matches['version'][0];
        } else {
            $version = $matches['version'][1];
        }
    } else {
        $version = $matches['version'][0];
    }
 
    // check if we have a number
    if ($version == null || $version == "") {
        $version = "?";
    }
 
    return array(
        'userAgent' => $visitor_user_agent,
        'name' => $bname,
        'version' => $version,
        'pattern' => $pattern
    );
}

?>
