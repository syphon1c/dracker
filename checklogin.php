<?php

/**
 * Description of checklogin.php
 *
 * @author GPhillips
 */
 
session_start();
session_regenerate_id(true);

include_once 'configs/DatabaseConnection.php';
$connection = new DatabaseConnection();

// username and password sent from form
$myusername=$_POST['myusername'];
$mypassword=$_POST['mypassword'];

function cleanInput($input) {
    $search_input = array(
        '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
        '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
        '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
        '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
    );
    $output = preg_replace($search_input, '', $input);
    return $output;
}

function sanitize($input) {
    if (is_array($input)) {
        foreach($input as $var=>$val) {
            $output[$var] = sanitize($val);
        }
    }
    else {
        if (get_magic_quotes_gpc()) {
            $input = stripslashes($input);
        }
        $input  = cleanInput($input);
        $connection = new DatabaseConnection();
        $output = mysql_real_escape_string($input);
    }
    return $output;
}


// To protect MySQL injection (more detail about MySQL injection)
$myusername = sanitize($myusername);
$mypassword = sanitize($mypassword);
$pass = md5($mypassword);

$session = session_id();
$time = time(); 

$sql = "SELECT * FROM user_account WHERE username='$myusername' and password='$pass'";
$result = $connection->executeQuery($sql);

// Mysql_num_row is counting table row
$count = mysql_num_rows($result);

// If result matched $myusername and $mypassword, table row must be 1 row

if($count==1){
  
  $row = mysql_fetch_array($result);
  $username = $row['username'];
  $uid = $row['id'];
  sanitize($session);
  
  $ipaddress = getenv('REMOTE_ADDR');

  $new_session = "INSERT INTO loggedin (session, uid, username, time)VALUES('$session', '$uid', '$username', '$time')";
  $connection->executeQuery($new_session);
  
  // Register $myusername, $mypassword and redirect to file "login_success.php"
  $_SESSION['myusername'] = $myusername;
  $_SESSION['mypassword'] = $pass;
  //session_register("myusername");
  //session_register("mypassword");
  ini_set("session.cookie_httponly", 1); //Set cookie into HTTPOnly mode
  
  $message = "Username: <b>" . $username. "</b> logged in successfully!<br>
              Source IP Address is: <b>" .$ipaddress . "</b><br>
              Client Account ID:<b>" . $clientid . "</b> with role:<b>" . $role . "</b>";

  $connection->executeQuery("INSERT INTO Logs VALUES(NULL, 'Notice', 'New', '$username', 'login.php', '$message', NULL)");
  $connection->closeConnection();
            
  header("location:view_results.php");

} else {
  $ipaddress = getenv('REMOTE_ADDR');
  $message = "Username: <b>" . $myusername. "</b> failed to login!<br>
              Source IP Address is: <b>" .$ipaddress . "</b><br>
              ";

  $connection->executeQuery("INSERT INTO Logs VALUES(NULL, 'Warning', 'New', '$myusername', 'login.php', '$message', NULL)");
  $connection->closeConnection();
  header("location:login.php?msg=1");
}
?>

