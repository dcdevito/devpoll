<!DOCTYPE HTML> 
<html>
<head>
<style>
.error {color: #FF0000;}
</style>
</head>
<body> 

<?php

include("config.php");
session_start();

// define variables and set to empty values
$nameErr = $schoolErr = $emailErr = $genderErr = $websiteErr = "";
$firstName = $lastName = $school = $email = $password = $gender = $comment = $website = "";




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
?>

<h2>Stokely LLC Communications School Survey Site</h2>
<p><span class="error">* required field.</span></p>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
   First Name: <input type="text" name="firstName" value="<?php echo $firstName;?>">
   <span class="error">* <?php echo $nameErr;?></span>
   <br><br>
   Last Name: <input type="text" name="lastName" value="<?php echo $lastName;?>">
   <span class="error">* <?php echo $nameErr;?></span>
   <br><br>
   School: <input type="text" name="school" value="<?php echo $school;?>">
   <span class="error">* <?php echo $schoolErr;?></span>
   <br><br>
   E-mail: <input type="text" name="email" value="<?php echo $email;?>">
   <span class="error">* <?php echo $emailErr;?></span>
   <br><br>
   Password: <input type="text" name="password" value="<?php echo $password;?>">
   <span class="error">* <?php echo $passwordErr;?></span>
   <br><br>
   Website: <input type="text" name="website" value="<?php echo $website;?>">
   <span class="error"><?php echo $websiteErr;?></span>
   <br><br>
   Comment: <textarea name="comment" rows="5" cols="40"><?php echo $comment;?></textarea>
   <br><br>
   Gender:
   <input type="radio" name="gender" <?php if (isset($gender) && $gender=="female") echo "checked";?>  value="female">Female
   <input type="radio" name="gender" <?php if (isset($gender) && $gender=="male") echo "checked";?>  value="male">Male
   <span class="error">* <?php echo $genderErr;?></span>
   <br><br>
   <input type="submit" name="submit" value="Create Account"> 
</form>

<?php
echo "<h2>You've Entered (to be eventually saved into the DB):</h2>";
echo $firstName;
echo "<br>";
echo $lastName;
echo "<br>";
echo $school;
echo "<br>";
echo $email;
echo "<br>";
echo $password;
echo "<br>";
echo $website;
echo "<br>";
echo $comment;
echo "<br>";
echo $gender;
?>

</body>
</html>