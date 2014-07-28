<!doctype html>
<html>
<?php
// display form if user has not clicked submit
if (!isset($_POST["submit"]))
  {
  ?>
<form method="post" action="<?php echo $_SERVER["PHP_SELF"];?>">
<head>
  <title>Devpoll with Polymer</title>
  <meta name="viewport" content="width=device-width, minimum-scale=1.0, initial-scale=1.0, user-scalable=yes">
  <script src="../components/platform/platform.js"></script>
  <link rel="import" href="../components/font-roboto/roboto.html">
    
    <!--Added from tutorial-->
    <link rel="import" href="../components/core-header-panel/core-header-panel.html">
    <link rel="import" href="../components/core-toolbar/core-toolbar.html">
    <link rel="import" href="../components/paper-tabs/paper-tabs.html">
    <link rel="import" href="../components/core-icons/core-icons.html">
    <link rel="import" href="../components/paper-input/paper-input.html">
    <link rel="import" href="../components/paper-button/paper-button.html">
      
    <link rel="import" href="post-list.html">
  
    
  <style>
  html,body {
    height: 100%;
    margin: 0;
    background-color: #E5E5E5;
    font-family: 'RobotoDraft', sans-serif;
  }
  core-header-panel {
    height: 100%;
    overflow: auto;
    -webkit-overflow-scrolling: touch; 
  }
  core-toolbar {
    background: #03a9f4;
    color: white;
  }
  paper-tabs {
    width: 100%;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
  }
  .container {
    width: 80%;
    margin: 50px auto;
  }
  @media (min-width: 481px) {
    paper-tabs {
      width: 700px;
    }
    .container {
      width: 500px;
    }

  }
  </style>
</head>

<body unresolved touch-action="auto">
  
  <!--Toolbar added from tutorial-->
  <core-header-panel>
      
    <core-toolbar>
      <!--Tabs added from tutorial-->
        <paper-tabs valueattr="name" selected="home" self-end>
        <paper-tab name="home">HOME</paper-tab>
        <paper-tab name="favorites">FAVORITES</paper-tab>
        <paper-tab name="createSurvey">CREATE NEW SURVEY</paper-tab>
        <paper-tab name="sendSurvey">SEND SURVEY</paper-tab>
        
        </paper-tabs>

    </core-toolbar>

    <!-- main page content will go here -->
  
      <div class="container" layout vertical center>
      <post-card>
        <paper-dialog id="dialog" heading="Launch?" transition="paper-dialog-transition-bottom">
          <paper-input floatingLabel label="From" name="from"></paper-input>
          <paper-input floatingLabel label="Subject" name="subject"></paper-input>
          <paper-input floatingLabel multiline label="message"></paper-input>
          <br></br>
          <paper-button name="submit" label="Send Survey" affirmative default raisedButton ></paper-button>
        </paper-dialog>
      </post-card>
      
      
      <!--<post-list show="all"></post-list>-->
    </div>
  </core-header-panel>
  
</body>

</html>
  <?php 
  }
else
  // the user has submitted the form
  {
  // Check if the "from" input field is filled out
  if (isset($_POST["from"]))
    {
    $from = $_POST["from"]; // sender
    $subject = $_POST["subject"];
    $message = $_POST["message"];
    // message lines should not exceed 70 characters (PHP rule), so wrap it
    $message = wordwrap($message, 70);
    // send mail
    mail("ddevito@gmail.com",$subject,$message,"From: $from\n");
    echo "Thank you for sending us feedback";
    }
  }
?>
