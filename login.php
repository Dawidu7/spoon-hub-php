<?php
  include "session.php";
  include "database.php";
  include "utils.php";

  protect("", true);

  if($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $password = $_POST["password"];

    $errors = [
      "name" => 
        empty($name) 
          ? "Field empty" 
          : (
            strpos($name, "@") != false 
              ? (
                !filter_var($name, FILTER_VALIDATE_EMAIL)
                  ? "Invalid email" 
                  : false
                )
              : (
                !preg_match("/^[A-Za-z0-9\s\-]{2,20}$/", $name) 
                  ? "Invalid name" 
                  : false
              )
          ),
      "password" => 
        empty($password) 
          ? "Field empty" 
          : (
            !preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/", $password) 
              ? "Invalid password" 
              : false
          ),
    ];

    if(!in_array(true, $errors)) {
      $res = $conn -> query(
        "SELECT * FROM users WHERE email = '$name' OR name = '$name'"
      );

      if($res) {
        $user = $res -> fetch_assoc();

        if($user) {
          if(password_verify($password, $user["password"])) {
            session_start();
            $_SESSION["user"] = $user;
            header("Location: /spoon-hub");
          } else {
            $errors["password"] = "Incorrect password";
          }
        } else {
          $errors["name"] = "User not found";
        }
      } else {
        $errors["name"] = "User not found";
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
  </head>
  <body>
    <nav>
      <a href="/spoon-hub">Spoon<span>Hub</span></a>
      <ul>
        <li><a href="/spoon-hub/login.php">Login</a></li>
        <li><a href="/spoon-hub/register.php">Register</a></li>
      </ul>
    </nav>  
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" class="styled">
      <h1>Login</h1>
      <section>
        <label for="name">Email or Name</label>
        <input type="text" name="name" id="name" value="<?php echo isset($name) ? $name : "" ?>" />
        <?php echo $errors["name"] != false ? "<span>".$errors["name"].".</span>" : "" ?> 
      </section>
      <section>
        <label for="password">Password</label>
        <input type="password" name="password" id="password" />
        <?php echo $errors["password"] != false ? "<span>".$errors["password"].".</span>" : "" ?> 
      </section>
      <input type="submit" value="Login" />
    </form>
  </body>
</html>
