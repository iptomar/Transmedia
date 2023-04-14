<?php
require "config/connectdb.php";

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
                    <div class="error"><?= $error?></div>
                    <button type="submit" name="submit" class="btn btn-primary">Login</button>
                </form>
            </div>
        </div>
    </div>

</html>