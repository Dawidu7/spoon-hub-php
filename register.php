<?php
  include "session.php";
  include "database.php";
  include "cloudinary.php";
  use Ramsey\Uuid\Uuid;

  protect("", true);

  if($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $name = $_POST["name"];
    $password = $_POST["password"];
    $passwordConfirm = $_POST["passwordConfirm"];

    $bio = $_POST["bio"];
    $bio = isset($bio) ? "$bio" : NULL;

    if(isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"])) {
      $imageName = $_FILES["image"]["tmp_name"];

      $imageRes = $upload -> upload($imageName, [
        "folder" => "spoon-hub",
        "resource_type" => "image"
      ]);

      $url = $imageRes["secure_url"];
      $image = "$url";
    } else {
      $image = NULL;
    }

    $errors = [
      "email" => 
        empty($email) 
          ? "Field empty"
          : (
            !filter_var($email, FILTER_VALIDATE_EMAIL) 
              ? "Invalid email" 
              : false
          ),
      "name" => 
        empty($name) 
          ? "Field empty" 
          : (
            !preg_match("/^[A-Za-z0-9\s\-]{2,20}$/", $name) 
              ? "Invalid name" 
              : false
          ),
      "password" => 
        empty($password) 
          ? "Field empty" 
          : (
            !preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/", $password) 
              ? "Invalid password" 
              : (
                $password != $passwordConfirm 
                  ? "Passwords do not match" 
                  : false
              )
          ),
      "passwordConfirm" => 
        empty($passwordConfirm) 
          ? "Field empty" 
          : (
            $password != $passwordConfirm 
              ? "Passwords do not match" 
              : false
          ),
    ];

    if(!in_array(true, $errors)) {
      $uuid = Uuid::uuid4();
      $passwordHash = password_hash($password, PASSWORD_DEFAULT);

      $res = $conn -> query(
        "INSERT INTO users (id, email, name, password, bio, image) VALUES".
        " ('".$uuid -> toString()."', '$email', '$name', '$passwordHash', '$bio', '$image')"
      );

      if($res) {
        header("Location: /spoon-hub/login.php");
      } else {
        $error = $conn -> error;
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
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" class="styled" enctype="multipart/form-data">
      <h1>Register</h1>
      <section>
        <label for="email">Email<sup>*</sup></label>
        <input type="email" name="email" id="email" value="<?php echo isset($email) ? $email : "" ?>" />
        <?php echo $errors["email"] != false ? "<span>".$errors["email"].".</span>" : "" ?> 
      </section>
      <section>
        <label for="name">Name<sup>*</sup></label>
        <input type="text" name="name" id="name" value="<?php echo isset($name) ? $name : "" ?>" />
        <?php echo $errors["name"] != false ? "<span>".$errors["name"].".</span>" : "" ?> 
      </section>
      <section>
        <label for="password">Password<sup>*</sup></label>
        <input type="password" name="password" id="password" />
        <?php echo $errors["password"] != false ? "<span>".$errors["password"].".</span>" : "" ?> 
      </section>
      <section>
        <label for="passwordConfirm">Confirm Password<sup>*</sup></label>
        <input type="password" name="passwordConfirm" id="passwordConfirm" />
        <?php echo $errors["passwordConfirm"] != false ? "<span>".$errors["passwordConfirm"].".</span>" : "" ?> 
      </section>
      <section>
        <label for="bio">Bio</label>
        <textarea name="bio" id="bio"></textarea>
      </section>
      <section>
        <label for="image">Image</label>
        <input type="file" name="image" id="image" accept="image/jpeg, image/png" />
      </section>
      <input type="submit" value="Register" />
    </form>
  </body>
</html>
