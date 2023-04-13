<?php
include("../config.php");

/**
 * Updates the click value of the selected link.
 */

if (isset($_POST["linkId"])) {
  $query = $conn->prepare("UPDATE `sites` SET clicks = clicks + 1 WHERE id = :id");
  $query->bindParam(":id", $_POST["linkId"]);

  $query->execute();
} else {
  echo "No link passed to page"; // just for debugging
}

?>