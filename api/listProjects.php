<?php
ob_start();
require '../classes/bootstrap.php';

$projects = new spir1donov\Projects($db->getInstance());

$list = $projects->getProjectsList($user->id);

$bootboxFormat = [];

foreach ($list as $project) {
  $bootboxFormat[] = [
    'text' => $project['name'],
    'value' => $project['data'],
    'projectId' => $project['id']
  ];
}

$debug = ob_get_clean();

echo json_encode([
  'result' => $bootboxFormat,
  'debug' => $debug
]);
