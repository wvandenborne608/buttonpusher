<?php

  error_reporting(E_ALL);
  ini_set('display_errors', '1');
  date_default_timezone_set('UTC');
  
  include("functions.php");
  
  $pythonScript        = "web-emulate.py"; //"web.py" for real.
  $logFile             = "entries.log"; //for logging each button press action with a timestamp
  $clientIp            = getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR');  
  $path                = realpath(dirname(__FILE__));
  $currentTime         = time();
  $timeOutWarning      = 6; // wait xx seconds after previous press.
  $daysNotAllowed      = ["Mon", "Sat"];
  $timeNotAllowedStart = 1730; // not allowed to press the button after ...
  $timeNotAllowedEnd   = 0745; 
  $stringAlertInfo     = ""; //for inserting an alert message into the HTML below.
  $stringAlertDanger   = ""; //for inserting an error message into the HTML below.
        
  if( !empty($_POST) ){ //if form has been submitted.
    $allowedToPress = allowedToPress($path, $logFile, $currentTime, $timeOutWarning, $daysNotAllowed, $timeNotAllowedStart, $timeNotAllowedEnd);
    if ($allowedToPress=="ok") { //are we allowed to press the button?
      file_put_contents("$path/$logFile", $currentTime ."|". $clientIp ."\n", FILE_APPEND | LOCK_EX); //write to entries log
      exec("sudo python $path/python/$pythonScript", $stdout, $error); //execute python script to press the button.
      if(!empty($stdout)) $stringAlertInfo   = '<div class="alert alert-info">' . (!empty($stdout[0]) ? $stdout[0] : null) . '</div>';
      if(!empty($error) ) $stringAlertDanger = '<div class="alert alert-danger">' . (!empty($error[0]) ? $error[0] : null) .'</div>';
    } else { //if not allowed, then show a message.
      $stringAlertDanger = '<div class="alert alert-danger">' .$allowedToPress . '</div>';
    } //end else if
  } //end if

?>



<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js" ></script>
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
  <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
  <!-- Optional theme -->
  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap-theme.min.css">
  <!-- Latest compiled and minified JavaScript -->
  <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4 text-center">    
            <h1><i class="glyphicon glyphicon-hand-right"></i> Button Presser</h1>
            <form action="index.php" method="post" id="pressform">
               <input type="hidden" name="dummy" value="dummy">
               <input id="pressme" type="submit" class="btn-lg btn-primary" value="Press Me">
            </form>
        </div>
        <div class="col-md-4"></div>
    </div>
    <div class="row">
        <div class="col-md-12"></div>
            <?php echo $stringAlertInfo; ?>
            <?php echo $stringAlertDanger; ?>
        </div>
    </div>
</div>

<script type="text/javascript">
  $('#pressform').on('submit', function () {
    $('#pressme').prop('disabled', true);
    $("#pressme").prop('value', 'Pressing ...');
  });
  $("div.alert").delay(2000).fadeOut("slow");
</script>

</body>

</html>
