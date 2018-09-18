<?php
$targetBlank = isset($mTargetBlank) ? ' target="_blank"' : '';
?>
<nav class="navbar navbar-expand-md navbar-dark bg-dark">
  <a class="navbar-brand" href="#">Sketch Maker PRO v1.0</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse"
          aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarCollapse">
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link<?php echo $current_page == 'home' ? ' active' : ''; ?>" <?php echo $targetBlank; ?> href="index.php">New Project</a>
      </li>
      <li class="nav-item">
        <a class="openProjectBtn nav-link<?php echo $current_page == 'home_load' ? ' active' : ''; ?>"  <?php echo $targetBlank; ?> href="index.php#loadProject">Load Project</a>
      </li>
      <li class="nav-item">
        <a class="nav-link<?php echo $current_page == 'videos' ? ' active' : '" target="_blank'; ?>" href="videos.php">My videos</a>
      </li>
<?php
if($user->isAdmin):
?>
      <li class="nav-item">
        <a class="nav-link" href="users.php" target="_blank">Users</a>
      </li>
      <li class="nav-item">      
        <a class="nav-link" href="admin.php" target="_blank">Admin</a>
      </li>
<?php
endif;
?>      
    </ul>
  </div>
  <div class="navbar-nav" id="fileUploadProgress">
    <div class="progress">
      <div class="progress-bar" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
  </div>
  <ul class="navbar-nav">
    <li class="nav-item dropdown">
      <a class="nav-item nav-link dropdown-toggle mr-md-2" href="#" id="userDropdownToggle" data-toggle="dropdown">
        <i class="fa fa-user-circle-o"></i>     <?php echo $user->getFullName(); ?>
      </a>
      <div class="dropdown-menu dropdown-menu-right">
        <a class="dropdown-item" href="profile.php">My Profile</a>
        <!--<a class="dropdown-item" href="#">Settings</a>-->
        <a class="dropdown-item" href="logout.php">Logout</a>
      </div>
    </li>
  </ul>
</nav>