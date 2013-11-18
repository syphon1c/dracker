<?php
/**
 *  Description of account_reset.php
 * 
 * @author GPhillips
 */

include_once 'configs/DatabaseConnection.php';

ini_set("session.cookie_httponly", 1);

// Adds X-Frame-Options to HTTP header, so that page cannot be shown in an iframe.
header('X-Frame-Options: DENY');

// Adds X-Frame-Options to HTTP header, so that page can only be shown in an iframe of the same site.
header('X-Frame-Options: SAMEORIGIN');

session_set_cookie_params($httponly = True);

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


if(isset($_GET['key'])) {
    $token_key=$_GET['key'];
    $token_key=sanitize($token_key);
    
    $set_key="";
    $connection = new DatabaseConnection();
    $queryUserAccount = $connection->executeQuery("SELECT * FROM reset_accounts WHERE key_token = '$token_key'");
    while ($rowAccountDetails = mysql_fetch_array($queryUserAccount)){
        $uid=$rowAccountDetails['uid'];
        $cid=$rowAccountDetails['cid'];
        $set_email=$rowAccountDetails['email'];
        $set_key=$rowAccountDetails['key_token'];
        
        if($set_key=""){
            header("location:index.php");
        }
    }
}
    

?>
<html>
 <head>
   <meta charset="utf-8"> 
   <title>Dracker Account Reset</title> 
   <link href="css/login.css" rel="stylesheet" media="screen">
   <link href="css/bootstrap-modal.css" rel="stylesheet">
   <link href="css/bootstrap-responsive.css" rel="stylesheet">
 </head>
   
<body>

<div class="navbar navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container">
      <a data-target=".nav-collapse" data-toggle="collapse" class="btn btn-navbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>
      <a href="#" class="brand">Track and Trace Documents</a>
      <div class="nav-collapse">
      </div>
    </div>
  </div>
</div>

<div class="header"></div>

<div class="main">
    <div class="mid-container wrapper">
  <h1 class="title">Dracker</h1>

<form name="login" id="login"class="form-signin" method="post" action="login.php?msg=3">
    <ul class="fields">
      <li>
        <input autocomplete="off" id="email_reset" name="email_reset" placeholder="email address" type="text" required />
      </li>
      <li>
        <input autocomplete="off" id="mypassword_reset" name="mypassword_reset" placeholder="new password" type="password" required/>
        <input autocomplete="off" id="key_reset" name="key_reset" placeholder="new password" type="hidden" value="<?echo $token_key?>"/>
      </li>
    </ul>
   
    <div class="mid-form">
      <input class="btn-flat inverse large pull-right" name="commit" type="submit" value="Reset" />
     	<br> 
    </div>
  </form>
</div>
</div>

<style>
#ResetPass {
       width: 500px; 
       margin: -240px 0 0 -250px; 
}
</style>

<div class="modal container hide fade" id="ResetPass" tabindex="-1" role="dialog" aria-labelledby="ResetPassLabel" aria-hidden="true">
    <div class="modal-body">
        <p>Loading...</p>
    </div>
</div>


</body>

<script type="text/javascript" src="js/bootstrap.js"></script>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/bootstrap-modal.js"></script>
<script type="text/javascript" src="js/bootstrap-modalmanager.js"></script>

</html>
