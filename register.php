<?php
require "config/connectdb.php";
require "functions/validate.php";
session_start();
//If user is logged in, redirect them to index.php
if (isset($_SESSION["user"])) {
    header("location: index.php");
}
$name = '';
$email = '';
$username = '';
$password =  '';
$repeat_pass =  '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $repeat_pass = $_POST["repeat-password"];
    //Verify if email and username don't exist in the database
    $sql = 'SELECT username,email from user WHERE username = ? OR email=? LIMIT 1';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username, $email]);
    if ($stmt->fetchColumn()) {
        //If username or email is not unique give a error
        echo '<script>alert("Something went wrong, please try again")</script>';
    } else {
        $error = verify_register_input($name, $username, $email, $password, $repeat_pass);
        if ($error == null) {
            //Generate the password hash along with the salt
            $password = password_hash($password, PASSWORD_DEFAULT);
            //Insert new user into the user table in the database
            $sql = "INSERT INTO user (name, email, username, password ) VALUES (?,?,?,?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $email, $username, $password]);
            //Call function which will generate the verification key and send the email
            $ver_key = generate_email_verification($pdo, $email);
        } else {
            //If the input verification on the client side failed and the passwords don't match, show error message
            echo "<script>alert('$error, please input a valid value and try again')</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register User</title>
    <style>
        .invalid-feedback {
            font-size: 14px !important;
        }
    </style>
</head>

<body>
    <?php include "NavBar.php"; ?>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha384-xBuQ/xzmlsLoJpyjoggmTEz8OWUFM0/RC5BsqQBDX2v5cMvDHcMakNTNrHIW2I5f" crossorigin="anonymous"></script>

    <div class="container-sm mt-3">
        <div class="card">
            <div class="card-header text-center">Register</div>
            <div class="card-body">
                <form method="post" id="form-register" action="register.php" class="needs-validation" novalidate>
                    <!-- Input for the user to insert the name !-->
                    <div class="form-group">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" maxlength="128" class="form-control" id="name" required pattern="[a-zA-ZÀ-ÖØ-öø-ÿ. -]+" name="name" value="<?= $name ?>">
                        <div class="invalid-feedback">Please input a valid name</div>
                    </div>

                    <!-- Input for the user to insert the username !-->
                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" maxlength="128" class="form-control" id="username" required name="username" pattern="[A-Za-z0-9_]{4,20}" value="<?= $username ?>">
                        <div id="username-error" class="invalid-feedback">Please input a valid username: 4-20 characters and can contain letters, numbers and underscores</div>
                    </div>

                    <!-- Input for the user to insert the email !-->
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input onclick="" maxlength="128" type="email" class="form-control" id="email" required name="email" value="<?= $email ?>">
                        <div id="email-error" class="invalid-feedback">Please input a valid email</div>
                    </div>

                    <!-- Input for the user to insert the password !-->
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" minlength="6" class="form-control" id="password" required name="password">
                        <div class="invalid-feedback">Please choose a password with a minimum of 6 characters</div>
                    </div>

                    <!-- Input for the user to repeat the password !-->
                    <div class="form-group">
                        <label for="repeat-password">Repeat Password</label>
                        <input type="password" minlength="6" class="form-control" id="repeat-password" required name="repeat-password">
                        <div class="invalid-feedback">The Passwords don't match</div>
                    </div>

                    <button type="submit" name="submit_register" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        // Disable form submissions if there are invalid fields
        (function() {
            'use strict'
            var form = document.getElementById('form-register');
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })()

        //AJAX used to calls the function verify_email_exists in the file functios/ajax.php
        function emailVerify() {
            return $.ajax({
                type: "POST",
                url: "functions/ajax.php",
                data: {
                    email_verify: $("#email").val()
                },
                success: function(data) {
                    var input = document.getElementById('email');
                    var error = document.getElementById('email-error');
                    if (data == "true") {
                        error.innerHTML = "Email is already in use";
                        input.setCustomValidity("Email is already in use");
                    } else {
                        error.innerHTML = "Please input a valid email";
                        input.setCustomValidity('');
                    }
                }
            });
        }

        //AJAX used to calls the function verify_username_exists in the file functios/ajax.php
        function usernameVerify() {
            return $.ajax({
                type: "POST",
                url: "functions/ajax.php",
                data: {
                    username_verify: $("#username").val()
                },
                success: function(data) {
                    var input = document.getElementById('username');
                    var error = document.getElementById('username-error');
                    if (data == "true") {
                        error.innerHTML = "Username is already in use. Please try another";
                        input.setCustomValidity("Username is already in use");
                    } else {
                        error.innerHTML = "Please input a valid username: 4-20 characters and can contain letters, numbers and underscores";
                        input.setCustomValidity('');
                    }
                }
            });
        }


        //Stop form submission until the AJAX that verifies if the email and username are valid finishes
        $('#form-register').submit(function(event, options) {
            //Stop submit before running the AJAX
            event.preventDefault();
            event.stopPropagation();
            //Call the functions that use AJAX to verify if the email and username are unique
            var emailAjax = emailVerify();
            var userAjax = usernameVerify();
            //When both ajax functions end try submitting the form
            $.when(emailAjax, userAjax).done(function(emailResult, userResult) {
                var form = document.getElementById('form-register');
                //if the form is valid submit
                if (form.checkValidity()) {
                    $('form').unbind('submit').submit();
                }
            });
        })


        //On focus out of the repeat password input verify if the input matches the password input
        $("#repeat-password").focusout(function() {
            var input = document.getElementById('repeat-password');
            if (input.value != document.getElementById('password').value) {
                input.setCustomValidity("The Passwords don't match");
            } else {
                // input is valid -- reset the error message
                input.setCustomValidity('');
            }
        });

        //On focus out of the email input call the function email_verify that uses AJAX 
        $("#email").focusout(function() {
            emailVerify();
        });

        //On focus out of the username input call the function email_verify that uses AJAX 
        $("#username").focusout(function() {
            usernameVerify()
        });
    </script>
    <?php
    include "footer.php";
    ?>
</body>

</html>