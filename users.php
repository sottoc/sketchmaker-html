<?php
require 'classes/bootstrap.php';
if(!$user->isAdmin){
    header('Location: /');
    die();
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Manage Users</title>

    <link href="vendor/twbs/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
    <link href="vendor/fortawesome/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="css/jquery.growl.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
</head>

<body class="">
<?php
$mTargetBlank = true;
include_once('includes/navbar.php');
?>

<div class="container appContainer">
  <div class="row">
    <main role="main" class="col-12">
        <div class="d-flex justify-content-between">
            <h4>Users</h4>
            <form class="form-inline" id="search_form">
              <label class="sr-only" for="inlineFormInputGroupUsername2">E-mail</label>
              <div class="input-group input-group-sm mb-2 mr-sm-2">
                <div class="input-group-prepend">
                  <div class="input-group-text">@</div>
                </div>
                <input type="text" class="form-control form-control-sm"  name="email" id="inlineFormInputGroupUsername2" placeholder="E-mail">
              </div>
              <label class="sr-only" for="inlineFormInputName2">Licence</label>
              <input type="text" class="form-control form-control-sm mb-2 mr-sm-2" name="license" id="inlineFormInputName2" placeholder="License">
            
              <button type="submit" class="btn btn-sm btn-primary mb-2">Search</button>
            </form>
            <!-- Button trigger modal -->
            <div>
              <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addUserModal" id="addUserButton">
                <span class="fa fa-plus"></span> Add user
              </button>
            </div>
    
        </div>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
          <tr>
            <th>id</th>
            <th>Admin?</th>
            <th>Email</th>
            <th>Limit</th>
            <th>License</th>
            <th>Added On</th>
            <th>Actions</th>
          </tr>
          </thead>
          <tbody id="usersTable">
          </tbody>
        </table>
      </div>
    </main>
  </div>
</div>
<?php
include_once('includes/footer.php');
?>
<div class="hide" id="sink"></div>
<div id="overlay_full"></div>
<?php include dirname(__FILE__) . '/classes/admin-templates.php'; ?>
<script src="vendor/components/jquery/jquery.js"></script>
<script src="vendor/twbs/bootstrap/assets/js/vendor/popper.min.js"></script>
<script src="vendor/twbs/bootstrap/dist/js/bootstrap.js"></script>
<script src="js/jquery.growl.js"></script>
<script src="js/templates.js"></script>
<script src="js/users.js"></script>
</body>
</html>
