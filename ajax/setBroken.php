<?php
include("../config.php");

/**
 * Sets broken to 1 to indicate that the link is broken or unaccessible. 
 */

if (isset($_POST["src"])) {
  $query = $conn->prepare("UPDATE `images` SET broken = 1 WHERE imageUrl=:src");
  $query->bindParam(":src", $_POST["src"]);

  $query->execute();
} else {
  echo "No src passed to page";
}
?>