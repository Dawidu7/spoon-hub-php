<?php
  include "env.php";
  
  use Cloudinary\Configuration\Configuration;
  use Cloudinary\Api\Upload\UploadApi;

  Configuration::instance($_ENV["CLOUDINARY_URL"]);

  $upload = new UploadApi();
?>