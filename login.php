<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Login  :: Sketch Maker PRO</title>

  <link href="vendor/twbs/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
  <link href="css/main.css" rel="stylesheet">
</head>

<body class="page-signin">

<div class="container">

  <form class="form-signin" id="loginForm" action="api/login.php" method="post">
    <h2 class="form-signin-heading text-center">Please sign in</h2>
    <label for="inputEmail" class="sr-only">Email address</label>
    <input type="email" id="inputEmail" name="email" class="form-control" placeholder="Email address" required autofocus>
    <label for="inputPassword" class="sr-only">Password</label>
    <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Password" required>
<!--    <div class="checkbox">-->
<!--      <label>-->
<!--        <input type="checkbox" value="remember-me"> Remember me-->
<!--      </label>-->
<!--    </div>-->
    <button class="btn btn-lg btn-primary btn-block" type="submit" id="loginButton">Sign in</button>
    <p class="text-center">
        <a href="forgot.php" class="btn btn-link ">Forgot Details - Click Here</a>
    </p>
  </form>

</div> <!-- /container -->
<?php
include_once('includes/footer.php');
?>
<script src="vendor/components/jquery/jquery.js"></script>
<script src="js/login.js"></script>
</body>
</html>
