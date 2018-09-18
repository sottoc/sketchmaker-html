<?php
ob_start();
require '../classes/bootstrap.php';

$debug = ob_get_clean();
if ($user->isAdmin === '1') {
  echo json_encode([
    'result' => $auth->getUsersList(),
    'debug' => $debug
  ]);
}