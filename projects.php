<?php
require 'classes/bootstrap.php';
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>List of Projects</title>

  <link href="vendor/twbs/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
  <link href="vendor/fortawesome/font-awesome/css/font-awesome.css" rel="stylesheet">
  <link href="css/main.css" rel="stylesheet">
</head>

<body class="page-dashboard">
<header>
  <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
    <a class="navbar-brand" href="#">List of Projects</a>
    <button class="navbar-toggler d-lg-none" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarsExampleDefault">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="api/logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </nav>
</header>

<div class="container-fluid">
  <div class="row">
    <main role="main" class="col-12">
      <h4>Projects</h4>

    </main>
  </div>
</div>

<div class="hide" id="sink"></div>

<?php include dirname(__FILE__) . '/classes/templates.php'; ?>
<script src="vendor/components/jquery/jquery.js"></script>
<script src="vendor/twbs/bootstrap/assets/js/vendor/popper.min.js"></script>
<script src="vendor/twbs/bootstrap/dist/js/bootstrap.js"></script>
<script src="js/templates.js"></script>
</body>
</html>