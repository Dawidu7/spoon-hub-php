<?php
  include "session.php";
  include "database.php";
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SpoonHub</title>
    <link rel="stylesheet" href="styles/styles.css" />
    <link rel="stylesheet" href="styles/index.css" />
  </head>
  <body>
    <nav>
      <a href="/spoon-hub">Spoon<span>Hub</span></a>
      <ul>
        <?php if($isAuthenticated) { ?>
          <li><a href="/spoon-hub/upload.php">+</a></li>
          <li id="user-dropdown">
            <button <?php if(!empty($user["image"])) echo "style='background-image: url(".$user["image"].")'" ?>>
              <?php if(empty($user["image"])) { ?>
                <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512">
                  <!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                  <path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/>
                </svg>
              <?php } ?>
            </button>
            <ul>
              <li><a href="/spoon-hub/profile.php?name=<?php echo $user["name"] ?>" data-selected>Profile</a></li>
              <li><a href="/spoon-hub/settings.php">Settings</a></li>
              <li><a href="/spoon-hub/logout.php">Logout</a></li>
            </ul>
          </li>
        <?php } else { ?>
          <li><a href="/spoon-hub/login.php">Login</a></li>
          <li><a href="/spoon-hub/register.php">Register</a></li>
        <?php } ?>
      </ul>
    </nav>
    <ul>
      <?php 
        $res = $conn -> query("SELECT videos.id AS video_id, videos.title AS video_title, videos.thumbnail_url AS video_thumbnail, users.id AS user_id, users.name AS user_name, users.image AS user_image FROM videos JOIN users ON videos.user_id = users.id");

        while($row = $res -> fetch_assoc()) {
          $thumbnail = !empty($row["video_thumbnail"]) ? $row["video_thumbnail"] : "public/images/thumbnail-default.jpg";

          echo "<li>";
          echo "<a href='/spoon-hub/watch.php?id=".$row["video_id"]."'>";
          echo "<img src='$thumbnail' />";
          echo "<div>";
          echo "<h3>".$row["video_title"]."</h3>";
          echo "<a id='user' href='/spoon-hub/profile.php?name=".$row["user_name"]."'>";
          if(empty($row["user_image"])) {
            echo '<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512">
              <!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
              <path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/>
            </svg>';
          } else {
            echo "<img src='".$row["user_image"]."' />";
          } 
          echo "<p>".$row["user_name"]."</p>";
          echo "</a>";
          echo "</div>";
          echo "</a>";
          echo "</li>";
        }
      ?>
    </ul>
  </body>
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
</html>
