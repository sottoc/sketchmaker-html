<?php
set_include_path(get_include_path().':'.dirname(__FILE__).'/../classes');
require dirname(__FILE__) . '/../vendor/autoload.php';

session_start();
$_SESSION['is_server_side'] = true;

$db = new spir1donov\Database();
$user = new spir1donov\Authentication($db->getInstance());
$projects = new spir1donov\Projects($db->getInstance());

$project = $projects->getProject($_GET['project']);
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>CanvasRecorder</title>
  <link href="../css/main.css" rel="stylesheet">
</head>

<body class="theApp">
  <div id="mainCanvasContainer" class="embed-responsive-item theCanvas-server"></div>

  <script src="../vendor/components/jquery/jquery.js"></script>
  <script src="../vendor/twbs/bootstrap/assets/js/vendor/popper.min.js"></script>
  <script src="../js/konva.js"></script>

  <script src="../js/download.js"></script>
  <script src="../js/Whammy.js"></script>
  <script src="../js/CCapture.js"></script>
<script src="../js/templates.js"></script>
  <script src="../js/server_side.js?v=24082018"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js"></script>
  <script src="../js/common.js?v=29082018"></script>
  <script>
    var project = '<?php echo $project['data']; ?>'
      , projectName = '<?php echo $project['name']; ?>'
      , projectId = '<?php echo $project['id']; ?>'
      , requestId = '<?php echo (int)$_GET['requestId']?>';
    var recorder = new CanvasRecorder('mainCanvasContainer', document, Konva,new Templates(), CCapture, jQuery);
    recorder.createStage(project);
    recorder.loadAllFontsFromStage();
    recorder.setProjectName(projectName);
    recorder.setProjectId(projectId);
    recorder.setRequestId(requestId);
    //recorder.loadAllFontsFromStage(function(){
    //    //record with loaded fonts
    //    recorder.recordVideo();
    //}, function(){
    //    //we still need to record video even if fonts loading fault
    //    recorder.recordVideo();        
    //});
    
    recorder.recordWhenReady();
  </script>
</body>
</html>
