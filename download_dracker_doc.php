<?php
/**
* Description of download_dracker_doc.php
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

if(isset($_GET['refid'])) {
    $refid=$_GET['refid'];

    $document_template = file_get_contents("template/dont_change_template.doc");

    $current_url = curPageURL();
    $images_url = str_replace("download_dracker_doc.php?refid=",'images.php?imageid=', $current_url);

    //$tracker_url = $images_url . "?imageid=" . $refid;
    $document_template = str_replace("dracker_http_url_replace",$images_url, $document_template);

    file_put_contents("template/temp.doc", $document_template);

header('Content-Description: Report Download');
header("Content-type: application/x-download");
header("Content-disposition: attachment; filename=tracking.doc");
header('Cache-Control: must-revalidate');
 ob_clean();
 flush();
 readfile("template/temp.doc");

exit;
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
