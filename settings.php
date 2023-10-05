<?php
  include "session.php";
  include "database.php";

  protect("login.php");

  if($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $name = $_POST["name"];
    /* $password = $_POST["password"];
    $passwordConfirm = $_POST["passwordConfirm"]; */

    $errors = [
      "email" => filter_var($email, FILTER_VALIDATE_EMAIL) || empty($email) ? false : "Invalid email",
      "name" => preg_match("/^[A-Za-z0-9\s\-]{2,20}$/", $name) || empty($name) ? false : "Invalid name",
      /* "password" => 
        preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/", $password) 
        || empty($password)  && empty($passwordHash)
          ? false 
          : (
            $password != $passwordConfirm 
              ? "Passwords do not match" 
              : false
          ),
      "passwordConfirm" => 
        $password == $passwordConfirm 
        || empty($password) && empty($passwordHash) 
          ? false 
          : "Passwords do not match", */
    ];

    if($errors["email"] == false && $email != $user["email"]) {
      $emailRes = $conn -> query("UPDATE users SET email = '$email' WHERE id = '".$user["id"]."'");

      if($emailRes) {
        $_SESSION["user"]["email"] = $email;
      } else {
        $errors["email"] = $conn -> error;
      }
    }
    if($errors["name"] == false && $name != $user["name"]) {
      $nameRes = $conn -> query("UPDATE users SET name = '$name' WHERE id = '".$user["id"]."'");

      if($nameRes) {
        $_SESSION["user"]["name"] = $name;
      } else {
        $errors["name"] = $conn -> error;
      }
    }
    /* if($errors["password"] == false && !empty("password")) {
      $passwordHash = password_hash($password, PASSWORD_DEFAULT);

      $passwordRes = $conn -> query("UPDATE users SET password = '$passwordHash' WHERE id = '".$user["id"]."'");

      if($passwordRes) {
        $_SESSION["user"]["password"] = $passwordHash;
      } else {
        $errors["password"] = $conn -> error;
      }
    } */

    if(!in_array(true, $errors)) {
      header("Location: /spoon-hub/profile.php?name=".$_SESSION["user"]["name"]);
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
    <link rel="stylesheet" href="styles/settings.css" />
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
            <li><a href="/spoon-hub/settings.php" data-selected>Settings</a></li>
            <li><a href="/spoon-hub/logout.php">Logout</a></li>
          </ul>
        </li>
      </ul>
    </nav>
    <main>
      <h1>Settings</h1>
      <hr />
      <form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="POST" enctype="multipart/form-data">
        <section>
          <label for="email">Email</label>
          <input type="email" name="email" id="email" value="<?php echo isset($user["email"]) ? $user["email"] : "" ?>" />
          <?php echo $errors["email"] != false ? "<span>".$errors["email"].".</span>" : "" ?> 
        </section>
        <section>
          <label for="name">Name</label>
          <input type="text" name="name" id="name" value="<?php echo isset($user["name"]) ? $user["name"] : "" ?>" />
          <?php echo $errors["name"] != false ? "<span>".$errors["name"].".</span>" : "" ?> 
        </section>
        <!-- <section>
          <label for="password">Password</label>
          <input type="password" name="password" id="password" />
          <?php // echo $errors["password"] != false ? "<span>".$errors["password"].".</span>" : "" ?> 
        </section>
        <section>
          <label for="passwordConfirm">Confirm Password</label>
          <input type="password" name="passwordConfirm" id="passwordConfirm" />
          <?php // echo $errors["passwordConfirm"] != false ? "<span>".$errors["passwordConfirm"].".</span>" : "" ?> 
        </section> -->
        <input type="submit" value="Update" />
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