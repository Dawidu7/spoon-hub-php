<?php 
  include "session.php";
  
  session_destroy();

  header("Location: /spoon-hub/login.php");
?>