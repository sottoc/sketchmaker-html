<?php
require 'classes/bootstrap.php';

use \Sart\Video;

$sort = get_sort();

$videos = \Sart\Video::getVideosForGrid($user->id,$sort['order'] );

//Some service variables
//@todo: move to class
$page_title = 'My Videos';
$current_page = 'videos';

?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <!--<meta http-equiv="refresh" content="30">-->
  
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title><?php echo $page_title; ?></title>
  <link href="vendor/twbs/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
  <link href="vendor/fortawesome/font-awesome/css/font-awesome.css" rel="stylesheet">
  <link href="css/jquery.growl.css" rel="stylesheet">
  <link href="css/main.css" rel="stylesheet">
</head>

<body class="">
<?php
include_once('includes/navbar.php');
?>

<div class="container-fluid appContainer fw-1280">
    <div class="row">
        <main role="main" class="col-12">
            <h1>My Videos</h1>
            <a href="index.php" class="btn btn-primary mb-3 mt-2">New Project</a>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                          <th>Project Name</th>
                          <th>Format</th>
                          <th>Resolution</th>
                          <th>Progress</th>
                          <th><a href="?sort=<?php $s_ord =  get_sort('created');echo $s_ord['url']; ?>" class="<?php echo $s_ord['asc_desc_class'];?>">Created</a></th>
                          <th><a href="?sort=<?php $s_ord =  get_sort('updated');echo $s_ord['url']; ?>" class="<?php echo $s_ord['asc_desc_class'];?>">Updated</a></th>
                          <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="usersTable">
<?php
        foreach($videos as $video)
        {
            ?>
                <tr class=>
                    <td><?php echo htmlspecialchars($video->name); ?></td>
                    <td><?php echo $video->fileFormat; ?></td>
                    <td><?php echo $video->videoResolution; ?></td>
                    <td>
                        <div class="progress" id="video_progress_<?php echo $video->id; ?>">
                            <div class="progress-bar" role="progressbar" style="width: <?php echo ($video->status == Video::STATUS_RENDERED ? 100 : $video->progress); ?>%" ></div>
                        </div>                        
                    </td>
                    <td><?php echo $video->created; ?></td>
                    <td><?php echo $video->updated; ?></td>
                    <td><button class="btn btn-danger deleteButton" data-vid="<?php echo $video->id; ?>">Delete</button><?php
                    if($video->status == \Sart\Video::STATUS_RENDERED && $link = $video->getDownloadLink()){?>
                    <a class="btn btn-primary" target="_blank" href="<?php echo $link; ?>">Download</a>
<?php                        
                    }
                    ?></td>
                </tr>
<?php        
    }
?>
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

<?php include dirname(__FILE__) . '/classes/templates.php'; ?>
<script src="vendor/components/jquery/jquery.js"></script>
<script src="vendor/twbs/bootstrap/assets/js/vendor/popper.min.js"></script>
<script src="vendor/twbs/bootstrap/dist/js/bootstrap.js"></script>
<script src="js/jquery.growl.js"></script>
<script src="js/templates.js"></script>
<script src="js/videos.js"></script>
<script src="js/channel.js"></script>
<script type="text/javascript">
    channel.init('videos');
</script>
</body>
</html>