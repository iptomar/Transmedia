<?php
require "config/connectdb.php";
//Start new or resume existing session
session_start();
//If user is logged in, redirect them to index.php
if (isset($_SESSION["user"])) {
    header("location: index.php");
}
$login_method = '';
$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_method = $_POST["login_method"];
    $password = $_POST["password"];
    
    //Select user with the username or email
    $sql = 'SELECT username,email,password,verified from user WHERE username = ? OR email=? LIMIT 1';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$login_method, $login_method]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    //Verify if there is a user and in case there is verify if the password it's correct
    if (!empty($user) && password_verify($password, $user['password']) == 1) {
        //If user is not verified, don't allow login
        if (!$user['verified']) {
            $error = "Account is not verified. Please look for the verification email in your inbox";
        } else {
            //Set the username as a session variable
            $_SESSION["user"] = $user['username'];
            //Redirect to the index page, in the future it should be changed to the "My Stories" page
            header("location: index.php");
        }
    } else {
        //If the user is not found or the password is wrong, define the error to show in the div
        $error = "Something went wrong, please try again";
    }
}
?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <title>Login</title>
    <style>
        .error {
            font-size: 14px !important;
            color: red;
        }
    </style>
</head>

<body>
    <?php include "NavBar.php"; ?>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha384-xBuQ/xzmlsLoJpyjoggmTEz8OWUFM0/RC5BsqQBDX2v5cMvDHcMakNTNrHIW2I5f" crossorigin="anonymous"></script>

    <div class="container-sm mt-3">
        <div class="card">
            <div class="card-header text-center">Login</div>
            <div class="card-body">
                <form method="post" id="form-login" action="login.php">
                    <!-- Input for the user to insert the email or username !-->
                    <div class="form-group">
                        <label for="login_method" class="form-label">Email or Username</label>
                        <input onclick="" type="text" class="form-control" id="login_method" required name="login_method" value="<?= $login_method ?>">
                    </div>

                    <!-- Input for the user to insert the password !-->
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" required name="password">
                    </div>
                    <div class="error mb-2"><?= $error ?></div>
                    <button type="submit" name="submit" class="btn btn-primary">Login</button>
                </form>
            </div>
        </div>
    </div>

</html>