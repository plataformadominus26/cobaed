<?php 
  require_once __DIR__ . '/../config.php';
  $db = new mysqli(cfg('db.host'), cfg('db.user'), cfg('db.pwd'), cfg('db.name'));
  if ($db->connect_errno) {
  echo "Failed to connect to MySQL: (" . $db->connect_errno . ") " . $db->connect_error;

}

?>
