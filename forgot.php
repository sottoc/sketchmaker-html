<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Forgot Password  :: Sketch Maker PRO</title>

  <link href="vendor/twbs/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
  <link href="css/main.css" rel="stylesheet">
</head>

<body class="page-signin">

<div class="container">

  <form class="form-signin" id="forgotForm" action="api/forgot.php" method="post">
    <h2 class="form-signin-heading text-center">Enter your email</h2>
    <div id="error_text" class="text-danger text-center my-1"></div>
    <label for="inputEmail" class="sr-only">Email address</label>
    <input type="email" id="inputEmail" name="email" class="form-control rounded" placeholder="Email address" required autofocus>
    <button class="btn btn-lg btn-primary btn-block mt-3" type="submit">Restore Access</button>
    <p class="text-center">
        <a href="login.php" class="btn btn-link  ">Go back to login page</a>
    </p>
  </form>

</div> <!-- /container -->
<div id="overlay_full"></div>
<script src="vendor/components/jquery/jquery.js"></script>
<script src="js/login.js"></script>
</body>
</html>
