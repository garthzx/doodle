<?php
include("../config.php");

/**
 * Updates the click value of the clicked image.
 */

if (isset($_POST["imageUrl"])) {
  $query = $conn->prepare("UPDATE `images` SET clicks = clicks + 1 WHERE imageUrl = :imageUrl");
  $query->bindParam(":imageUrl", $_POST["imageUrl"]);

  $query->execute();
} else {
  echo "No image URL passed to page"; // just for debugging
}

?>