<?php 
ob_start();

try {
  $conn = new PDO("mysql:dbname=doodle;host=localhost", "root", "");
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
}
catch (PDOException $e) {
  echo "Connection Failed" . $e->getMessage();
}

?>