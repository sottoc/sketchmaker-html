<?php
define('__SKIP_AUTH__',1);

require 'classes/bootstrap.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Reset Password  :: Sketch Maker PRO</title>

  <link href="vendor/twbs/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
  <link href="css/main.css" rel="stylesheet">
</head>

<body class="page-signin">

<div class="container">
<?php
$error = false;
if(empty($_GET['key']))
{
    $error = 'Wrong request. Please do not repeat it again.';
}else{
    if(!$user = \Sart\User::model()->findByAttributes(['reset_key'=>$_GET['key']])){
        $error = 'Invalid reset key!';
    }else{
        list($key,$stamp) = explode('.',$user->reset_key);
        if(time()-$stamp > 86400)
        {
            $error = 'Reset key has been expired. Please use this link to get new one: <a href="/forgot.php"><b>Forgot Password</b></a>';
        }
    }
}
if($error){?>
    <div class="alert alert-danger text-center mx-auto w-50">
        <?php echo $error; ?>
    </div>    
<?php
}else{
?>


  <form class="form-signin" id="resetForm" action="api/reset.php" method="post">
    <h2 class="form-signin-heading text-center">Enter your new password</h2>
    <div id="error_text"></div>
    <label for="inputEmail" class="sr-only">New password</label>
    <input type="hidden" value="<?php echo $user->reset_key; ?>" name="reset_key"/>
    <input type="password" id="inputPassword" name="password" class="form-control rounded" placeholder="Password" required autofocus>
    <input type="password" id="confirmPassword" name="confirmPassword" class="form-control rounded" placeholder="Confirm Password" required >
    <button class="btn btn-lg btn-primary btn-block mt-3" type="submit">Set New Password</button>
    <p class="text-center">
        <a href="login.php" class="btn btn-link  ">Go back to login page</a>
    </p>
  </form>
<?php
}
?>
</div> <!-- /container -->
<div id="overlay_full"></div>
<script src="vendor/components/jquery/jquery.js"></script>
<script src="js/login.js"></script>
</body>
</html>
