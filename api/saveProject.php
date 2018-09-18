<?php
ob_start();
require '../classes/bootstrap.php';

$projects = new spir1donov\Projects($db->getInstance());

$result = $projects->saveProject([
  'name' => $_POST['name'],
  'data' => $_POST['data'],
  'owner' => $user->id,
  'duration'=>(float)$_POST['duration'],
]);

$debug = ob_get_clean();

echo json_encode([
  'result' => $result,
  'debug' => $debug
]);
