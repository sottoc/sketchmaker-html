<?php
define('__SKIP_AUTH__',1);

require '../classes/bootstrap.php';
ob_start();
$debug = ob_get_clean();
echo json_encode([
  'result' => $auth->login()->id,
  'debug' => $debug
]);