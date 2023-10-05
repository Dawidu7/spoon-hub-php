<?php
  require_once "vendor/autoload.php";

  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__, ".env.local");
  $dotenv -> load();
?>