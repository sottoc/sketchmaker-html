<?php
ob_start();
require '../classes/bootstrap.php';

$debug = ob_get_clean();
if ($user->isAdmin === '1') {
  echo json_encode([
    //@todo: should be changed to use new user object
    'result' => $auth->addUser(),
    'debug' => $debug
  ]);
}