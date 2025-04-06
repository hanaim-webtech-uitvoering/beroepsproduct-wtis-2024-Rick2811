<?php


$db_host = 'database_server';
$db_name = 'pizzeria';

$db_user    = 'sa';
$db_password = 'abc123!@#'; 

$verbinding = new PDO('sqlsrv:Server=' . $db_host . ';Database=' . $db_name . ';ConnectionPooling=0;TrustServerCertificate=1', $db_user, $db_password);

unset($db_password);

$verbinding->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function maakVerbinding() {
  global $verbinding;
  return $verbinding;
}

?>