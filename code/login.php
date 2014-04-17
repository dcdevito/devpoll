<?php

include("config.php");
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
// username and password sent from form 

//Set the URL to the main page; in this case we are using the bare bones admin devpoll page
$url = 'http://www.devpoll.net/adminpage.html';

$userid=addslashes($_POST['userid']); 
$accesscode=addslashes($_POST['accesscode']); 


$sql="SELECT userid FROM devpoll.security WHERE userid='$userid' and accesscode='$accesscode'";
$result=mysql_query($sql);
$row=mysql_fetch_array($result);
$active=$row['active'];

$count=mysql_num_rows($result);


// If result matched $username and $password, table row must be 1 row
if($count==1)
{
session_register("myuserid");
$_SESSION['login_user']=$userid;

//header("location: welcome.php");
header("location: $url" );
}
else 
{
$error="Your Login Name or Password is invalid";
}
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Login Page</title>

<style type="text/css">
body
{
font-family:Arial, Helvetica, sans-serif;
font-size:14px;

}
label
{
font-weight:bold;

width:100px;
font-size:14px;

}
.box
{
border:#666666 solid 1px;

}
</style>
</head>
<body bgcolor="#FFFFFF">
<div><h3>Cokely Communications School Survey Site <a href="http://www.devpoll.net/adminpage.html">Click Here</a></h3></div>

<!--<div style="font-weight:bold; margin-bottom:10px">Login Details -> Username : <a href="#">test</a>  Password : <a href="#">test</a></div><-->

<div align="center">
<div style="width:300px; border: solid 1px #333333; " align="left">
<div style="background-color:#333333; color:#FFFFFF; padding:3px;"><b>Login</b></div>


<div style="margin:30px">

<form action="" method="post">
<label>User ID  :</label><input type="text" name="userid" class="box"/><br /><br />
<label>Password  :</label><input type="password" name="accesscode" class="box" /><br/><br />
<input type="submit" value=" Submit "/><br />

</form>
<div style="font-size:11px; color:#cc0000; margin-top:10px"><?php echo $error; ?></div>
</div>
</div>
</div>

</body>
</html>
