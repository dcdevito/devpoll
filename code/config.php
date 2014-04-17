<?php
$mysql_hostname = "localhost:8888";
$mysql_user = "devpoll";
$mysql_password = "devpoll";
$mysql_database = "devpoll";

$bd = mysql_connect($mysql_hostname, $mysql_user, $mysql_password) or die("Do not try and bend the spoon. That's impossible. Instead... only try to realize the truth");
mysql_select_db($mysql_database, $bd) or die("What truth? There is no spoon");

?>