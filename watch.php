<?php 
  include "session.php";
  include "database.php";

  $user = $conn -> query("SELECT * FROM users WHERE id = (SELECT user_id FROM videos WHERE id = '".$_GET["id"]."') LIMIT 1") -> fetch_assoc();

  if($_SERVER["REQUEST_METHOD"] == "POST") {
    if($_GET["edit"] == "true" && $_SESSION["user"]["id"] == $user["id"]) {
      $res = $conn -> query("DELETE FROM videos WHERE id = '".$_GET["id"]."'");
  
      if($res) {
        header("Location: /spoon-hub/profile.php?name=".$user["name"]);
      }
    }
    else {
      $type = $_POST["type"];

      if($type == "true") {
        $res = $conn -> query(
          "INSERT INTO subscriptions (user_id, subscriber_id) VALUES ('".$user["id"]."', '".$_SESSION["user"]["id"]."')"
        );
      } else {
        $res = $conn -> query(
          "DELETE FROM subscriptions WHERE user_id = '".$user["id"]."' AND subscriber_id = '".$_SESSION["user"]["id"]."'"
        );
      }

      if($res) {
        header("Location: /spoon-hub/watch.php?id=".$_GET["id"]);
      }
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
    <link rel="stylesheet" href="styles/watch.css" />
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
              <li><a href="/spoon-hub/profile.php?name=<?php echo $user["name"] ?>">Profile</a></li>
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
    <main>
      <?php
        $video = $conn -> query("SELECT * FROM videos WHERE id = '".$_GET["id"]."' LIMIT 1") -> fetch_assoc();

        echo "<video controls>";
        echo "<source src='".$video["url"]."' type='video/mp4' />";
        echo "</video>";
      ?>
      <div>
        <section>
          <h1>
            <?php echo $video["title"] ?>
            <?php if($_SESSION["user"]["id"] == $user["id"]) { ?>
              <form action="<?php echo $_SERVER["PHP_SELF"]."?id=".$video["id"]."&edit=true" ?>" method="POST">
                <input type="submit" value="Delete" />
              </form>
            <?php } ?>
          </h1>
          <?php
            $datetime = new DateTime($video["created_at"]);
            $interval = $datetime -> diff(new DateTime());

            $timeAgo = "";
        
            if ($interval->y > 0) {
                $timeAgo .= $interval->y . " year" . ($interval->y > 1 ? "s" : "") . " ";
            } elseif ($interval->m > 0) {
                $timeAgo .= $interval->m . " month" . ($interval->m > 1 ? "s" : "") . " ";
            } elseif ($interval->d > 0) {
                $timeAgo .= $interval->d . " day" . ($interval->d > 1 ? "s" : "") . " ";
            } elseif ($interval->h > 0) {
                $timeAgo .= $interval->h . " hour" . ($interval->h > 1 ? "s" : "") . " ";
            } elseif ($interval->i > 0) {
                $timeAgo .= $interval->i . " minute" . ($interval->i > 1 ? "s" : "") . " ";
            } else {
                $timeAgo .= "a few seconds ";
            }
        
            $timeAgo .= "ago";

            echo "<h4>Posted ".$timeAgo."</h4>"
          ?>
          <p><?php echo $video["description"] ?></p>
        </section>
        <section>
          <a href="/spoon-hub/profile.php?name=<?php echo $user["name"] ?>">
            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512">
              <!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
              <path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/>
            </svg>
            <?php
              $res = $conn -> query(
                "SELECT * FROM subscriptions WHERE user_id = '".$user["id"]."' AND subscriber_id = '".$_SESSION["user"]["id"]."'"
              );

              $isSubscribed = $res -> num_rows > 0;
            ?>
            <div>
              <?php echo $user["name"] ?>
              <?php 
                $count = $conn -> query("SELECT COUNT(*) as count FROM subscriptions WHERE user_id = '".$user["id"]."'") -> fetch_assoc()["count"];

                echo "<p><b>$count</b> subscribers</p>";
              ?>
            </div>
          </a>
          <?php if($_SESSION["user"]["id"] != $user["id"]) { ?>
            <form action="<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"] ?>" method="POST">
              <input type="hidden" name="type" value="<?php echo $isSubscribed ? "false" : "true" ?>" />
              <input type="submit" id="subscribe" value="<?php echo $isSubscribed ? "Unsubscribe" : "Subscribe" ?>">
            </form>
          <?php } ?>
        </section>
      </div>
    </main>
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