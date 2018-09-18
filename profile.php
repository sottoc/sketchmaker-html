<?php
require 'classes/bootstrap.php';

//Process form sumbission
if(!empty($_POST))
{
    $error = false;
    //Check old password
    if($_POST['old_pswd'])
    {
        $check = \Sart\User::model()->findByAttributes([
            'email'=>$user->email,
            'password'=>md5($_POST['old_pswd'])
        ]);
        if(!$check)
        {
            $error = true;
            \Sart\Alert::flashError('Invalid old password!');        
        }else{
        
            if($_POST['new_pswd'] !== $_POST['confirm_pswd'])
            {
                $error = true;
                \Sart\Alert::flashError('Password and Password Confirmation do not match!');        
            }else{
                $user->password = md5($_POST['new_pswd']);
            }
        }
        
    }
    
    if($_POST['firstname'])
    {
        $user->firstname = htmlspecialchars(strip_tags($_POST['firstname']));
    }
    
    if($_POST['lastname'])
    {
        $user->lastname = htmlspecialchars(strip_tags($_POST['lastname']));
    }    
    
    if(!$error)
    {
        if($user->save())
        {
            \Sart\Alert::flashSuccess('User info has been updated!');            
        }else{
            \Sart\Alert::flashError('Error during update user info!');            
        }
    }
    

}

$sort = get_sort();

$videos = \Sart\Video::getVideosForGrid($user->id,$sort['order'] );

//Some service variables
//@todo: move to class
$page_title = 'Profile';
$current_page = 'profile';

?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title><?php echo $page_title; ?></title>

  <link href="vendor/twbs/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
  <link href="vendor/fortawesome/font-awesome/css/font-awesome.css" rel="stylesheet">
  <link href="css/main.css" rel="stylesheet">
</head>

<body class="">
<?php
include_once('includes/navbar.php');
?>

<div class="containe appContainer">
    <div class="row">
        <main role="main" class="col-12">
            <h1>Profile</h1>
            <a href="index.php" class="btn btn-primary mb-3 mt-2">New Project</a>
<?php
    \Sart\Alert::render();
?>            
            <form method="post" id="profile_form" class="card card-body bg-faded">
                <div class="row mb-4">
                    <div class="col-sm-5 ">
                        <fieldset>
                            <legend>Login Info</legend>
                            <div class="form-group">
                                <label>Email(login)</label>
                                <input type="email" disabled class="form-control" name="" value="<?php echo $user->email; ?>" placeholder="Email"/>
                            </div>                            
                            <div class="form-group">
                                <label>Old Password</label>
                                <input type="password" class="form-control" name="old_pswd" value="" placeholder="Old password"/>
                            </div>
                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" id="password" class="form-control" name="new_pswd" value="" placeholder="New Password"/>
                            </div>
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <input type="password" id="confirm_password" class="form-control" name="confirm_pswd" value="" placeholder="Confirm Password"/>
                            </div>                             
                        </fieldset>                        
                    </div>
                    <div class="col-sm-5 offset-sm-2">
                        <fieldset>
                            <legend>Personal Info</legend>
                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text" class="form-control" name="firstname" value="<?php echo $user->firstname; ?>" placeholder="First Name" required/>
                            </div>
                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" class="form-control" name="lastname" value="<?php echo $user->lastname; ?>" placeholder="Last Name" required/>
                            </div>
                        </fieldset>
                    </div>                    
                </div>
                <div class="row ">
                    <div class="col-sm-2 offset-sm-5">
                        <button type="submit" class="btn btn-primary btn-block btn-lg"><i class="fa fa-save"></i> Save</button>
                    </div>
                </div>
            </form>    
        </main>
    </div>
</div>
<?php
include_once('includes/footer.php');
?>
<div class="hide" id="sink"></div>

<script src="vendor/components/jquery/jquery.js"></script>
<script src="vendor/twbs/bootstrap/assets/js/vendor/popper.min.js"></script>
<script src="vendor/twbs/bootstrap/dist/js/bootstrap.js"></script>
<script type="text/javascript">
var password = document.getElementById("password"),
    confirm_password = document.getElementById("confirm_password");

function validatePassword(){
  if(password.value != confirm_password.value) {
    confirm_password.setCustomValidity("Passwords Don't Match");
  } else {
    confirm_password.setCustomValidity('');
  }
}

password.onchange = validatePassword;
confirm_password.onkeyup = validatePassword;
    
</script>
</body>
</html>
