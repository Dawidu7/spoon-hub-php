<?php
  session_start();

  $user = $_SESSION["user"];
  $isAuthenticated = isset($user) ? true : false;

  // TODO: Make the protect function work
  function protect($redirectUrl = "login.php", $reverse = false) {
    if($reverse ? isset($_SESSION["user"]) : !isset($_SESSION["user"])) {
      header("Location: /spoon-hub/$redirectUrl");
    }
  }
?>