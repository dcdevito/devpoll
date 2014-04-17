<?php

include("config.php");
session_start();

// define variables and set to empty values
//$nameErr = $schoolErr = $emailErr = $genderErr = $websiteErr = "";
//$firstName = $lastName = $school = $email = $password = $gender = $comment = $website = "";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
   if (empty($_POST["firstName"]))
     {$nameErr = "First Name is required";}
   else
     {
     $firstName = test_input($_POST["firstName"]);
     // check if name only contains letters and whitespace
     if (!preg_match("/^[a-zA-Z ]*$/",$firstName))
       {
       $nameErr = "Only letters and white space allowed"; 
       }
     }
   
      if (empty($_POST["lastName"]))
     {$nameErr = "Last Name is required";}
   else
     {
     $lastName = test_input($_POST["lastName"]);
     // check if name only contains letters and whitespace
     if (!preg_match("/^[a-zA-Z ]*$/",$lastName))
       {
       $nameErr = "Only letters and white space allowed"; 
       }
     }
     
   if (empty($_POST["school"]))
     {$schoolErr = "School is required";}
   else
     {
     $school = test_input($_POST["school"]);
     // check if name only contains letters and whitespace
     if (!preg_match("/^[a-zA-Z ]*$/",$school))
       {
       $schoolErr = "Only letters and white space allowed for School Name";
       }
     }
   
   if (empty($_POST["email"]))
     {$emailErr = "Email is required";}
   else
     {
     $email = test_input($_POST["email"]);
     // check if e-mail address syntax is valid
     if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email))
       {
       $emailErr = "Invalid email format"; 
       }
     }
     
   if (empty($_POST["password"]))
     {$passwordErr = "Password is required";}
   else
     {
     $password = test_input($_POST["password"]);
     // check if password is valid
     if (!preg_match("/^[a-zA-Z ]*$/",$password))
       {
       $passwordErr = "Invalid password format"; 
       }
     }
     
   if (empty($_POST["website"]))
     {$website = "";}
   else
     {
     $website = test_input($_POST["website"]);
     // check if URL address syntax is valid (this regular expression also allows dashes in the URL)
     if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$website))
       {
       $websiteErr = "Invalid URL"; 
       }
     }

   if (empty($_POST["comment"]))
     {$comment = "";}
   else
     {$comment = test_input($_POST["comment"]);}

   if (empty($_POST["gender"]))
     {$genderErr = "Gender is required";}
   else
     {$gender = test_input($_POST["gender"]);}
}

function test_input($data)
{
     $data = trim($data);
     $data = stripslashes($data);
     $data = htmlspecialchars($data);
     return $data;
}

//$sql = 'INSERT INTO devpoll.users '.
//      ' (userid, districtid, schoolid, role, opensurveys, firstname, lastname, email, password) '.
//      ' VALUES ("devpoll3", 00002, 00002, 1, null,"Ben", "Dover", "bdover@upyours.com", "oldballs") ';
//

$sql = "INSERT INTO devpoll.users (userid, districtid, schoolid, role, opensurveys, firstname, lastname, email, password) 
        VALUES ('$email', 00002, 00002, 1, null, '$firstName', '$lastName', '$email', '$password')";
      
$result=mysql_query($sql);
//$row=mysql_fetch_array($result);
//$active=$row['active'];
//$count=mysql_num_rows($result);

if (!mysql_query($bd,$sql))
{
   echo "$sql";
   die('Error: ' . mysql_error($bd));
   echo "$email";
}
else
   echo "One record added";


mysqli_close($bd);

?>

