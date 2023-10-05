<?php
  include "session.php";
  include "cloudinary.php";
  include "database.php";

  protect("login.php");

  if($_SERVER["REQUEST_METHOD"] == "POST") {
    $videoName = $_FILES["file"]["tmp_name"];

    $videoRes = $upload -> upload($videoName, [
      "folder" => "spoon-hub",
      "resource_type" => "video"
    ]);

    if(isset($_FILES["thumbnail"]) && !empty($_FILES["thumbnail"]["tmp_name"])) {
      $thumbnailName = $_FILES["thumbnail"]["tmp_name"];

      $thumbnailRes = $upload -> upload($thumbnailName, [
        "folder" => "spoon-hub",
        "resource_type" => "image"
      ]);

      $thumbnailUrl = $thumbnailRes["secure_url"];
    } else {
      $thumbnailUrl = NULL;
    }

    $title = $_POST["title"];
    $description = $_POST["description"];
    $url = $videoRes["secure_url"];

    $res = $conn -> query(
      "INSERT INTO videos (title, description, url, user_id, thumbnail_url) VALUES ('$title', '$description', '$url', '".$user["id"]."', '$thumbnailUrl')"
    );

    if($res) {
      header("Location: /spoon-hub/profile.php?name=".$user["name"]);
    } else {
      $error = $conn -> error;
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SpoonHub</title>
    <link rel="stylesheet" href="styles/styles.css" />
  </head>
  <body>
    <nav>
      <a href="/spoon-hub">Spoon<span>Hub</span></a>
      <ul>
        <li><a href="/spoon-hub/upload.php">+</a></li>
        <li id="user-dropdown">
          <button <?php if(empty($user["image"])) echo "style='background-image: url(".$user["image"].")'" ?>>
            <?php if(!empty($user["image"])) { ?>
              <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512">
                <!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                <path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/>
              </svg>
            <?php } ?>
          </button>
          <ul>
            <li><a href="/spoon-hub/profile.php?name=<?php echo $user["name"] ?>">Profile</a></li>
            <li><a href="/spoon-hub/settings.php">Settings</a></li>
            <li><a href="/spoon-hub/logout.php">Logout</a></li>
          </ul>
        </li>
      </ul>
    </nav>
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" class="styled" enctype="multipart/form-data">
      <h1>Upload</h1>
      <section>
        <label for="title">Title<sup>*</sup></label>
        <input type="text" name="title" id="title" />
        <?php echo $errors["title"] != false ? "<span>".$errors["title"].".</span>" : "" ?> 
      </section>
      <section>
        <label for="file">File<sup>*</sup></label>
        <input type="file" name="file" id="file" accept="video/*" />
      </section>
      <section>
        <label for="thumbnail">Thumbnail</label>
        <input type="file" name="thumbnail" id="thumbnail" accept="image/png, image/jpeg" />
      </section>
      <section>
        <label for="description">Description</label>
        <textarea type="text" name="description" id="description"></textarea>
        <?php echo $errors["description"] != false ? "<span>".$errors["description"].".</span>" : "" ?> 
      </section>
      <input type="submit" value="Upload" />
    </form>
    <script>
      const userBtn = document.querySelector("nav ul button")
      const userBtnDropdown = document.querySelector("nav ul ul")

      userBtn.addEventListener("click", () => {
        userBtnDropdown.classList.toggle("active");
      })

      window.addEventListener("click", e => {
        if(!userBtn.contains(e.target)) {
          userBtnDropdown.classList.remove("active");
        }
      })
    </script>
  </body>
</html>