<?php 
  include "session.php";
  include "database.php";

  if(isset($_GET["name"]) && !empty($_GET["name"])) {
    $res = $conn -> query(
      "SELECT * FROM users WHERE name = '".$_GET["name"]."' LIMIT 1"
    );

    $user = $res -> fetch_assoc();
  }

  if($_SERVER["REQUEST_METHOD"] == "POST") {
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
      header("Location: /spoon-hub/profile.php?name=".$user["name"]);
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
    <link rel="stylesheet" href="styles/profile.css" />
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
    <main>
      <?php if(empty($user["image"])) { ?>
        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512">
          <!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
          <path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/>
        </svg>
      <?php } else { ?>
        <img src="<?php echo $user["image"] ?>" />
      <?php } ?>
      <section>
        <h1><?php echo $user["name"] ?></h1>
        <p><?php echo $user["bio"] ?></p>
      </section>
      <div>
        <?php if($_SESSION["user"]["id"] != $user["id"]) { ?>
          <form action="<?php echo $_SERVER["PHP_SELF"]."?name=".$user["name"] ?>" method="POST">
            <?php
              $res = $conn -> query(
                "SELECT * FROM subscriptions WHERE user_id = '".$user["id"]."' AND subscriber_id = '".$_SESSION["user"]["id"]."'"
              );

              $isSubscribed = $res -> num_rows > 0;
            ?>
            <input type="hidden" name="type" value="<?php echo $isSubscribed ? "false" : "true" ?>" />
            <input type="submit" id="subscribe" value="<?php echo $isSubscribed ? "Unsubscribe" : "Subscribe" ?>">
          </form>
        <?php } 
          $count = $conn -> query("SELECT COUNT(*) as count FROM subscriptions WHERE user_id = '".$user["id"]."'") -> fetch_assoc()["count"];

          echo "<p><b>$count</b> subscribers</p>";
        ?>
      </div>
    </main>
    <ul>
      <?php
        $res = $conn -> query("SELECT * FROM videos WHERE user_id = '".$user["id"]."'");

        while($row = $res -> fetch_assoc()) {
          $thumbnail = !empty($row["thumbnail_url"]) ? $row["thumbnail_url"] : "public/images/thumbnail-default.jpg";

          echo "<li>";
            echo "<a href='/spoon-hub/watch.php?id=".$row["id"]."'>";
              echo "<img src='$thumbnail' />";
              echo "<h3>".$row["title"]."</h3>";
            echo "</a>";
          echo "</li>";
        }
      ?>
    </ul>
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