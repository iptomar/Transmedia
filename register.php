<?php
require "config/connectdb.php";
if (isset($_POST["submit"])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $name = $_POST["name"];

    //Generate the password hash along with the salt
    $password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO user (name, email, username, password ) VALUES (?,?,?,?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $email, $username, $password]);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Register User</title>
</head>

<body>
    <?php
    ?>
    <form method="post" action="register.php">
        <!-- Form textbox for the user to insert the name !-->
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" required name="name">
        </div>

        <!-- Form textbox for the user to insert the email !-->
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" required name="email">
        </div>

        <!-- Form textbox for the user to insert the name !-->
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" required name="username">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" required name="password">
        </div>

        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
    </form>
</body>

</html>