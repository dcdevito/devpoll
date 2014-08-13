<?php

include("config.php");
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST")
{

$userid=($_POST['userID']); 
$accesscode=($_POST['userPassword']);

$sql="SELECT userid FROM devpoll.security WHERE userid='$userid' and accesscode='$accesscode'";
$result=mysql_query($sql);
$row=mysql_fetch_array($result);
$active=$row['active'];

$count=mysql_num_rows($result);

// If result matched $username and $password, table row must be 1 row
if($count==1)
{
//for future use and consideration
//session_register("myuserid");
//$_SESSION['login_user']=$userid;

header('location: createAccount2.html');
}
else 
{
header('location: login.html');
}
}
?>


