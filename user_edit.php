<?php
require "./functions/useful.php";
require "functions/validate.php";
require "config/connectdb.php";
require "verify_login.php";

// Retrieve user ID from session or any other method
$userId = $_SESSION['user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve updated user information from the form
    $name = $_POST['name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['new_password'];
    $confirmPassword = $_POST['repeat_password'];

    // Update the user's information in the database
    $query = "UPDATE user SET name = ?, email = ?, username = ?";
    $parameters = [$name, $email, $username];

    // Check if a new password is provided
    if (!empty($password)) {
        // Validate and compare the new password with the confirmation
        if ($password != $confirmPassword) {
            message_redirect("Error: The new password and confirmation password do not match", "user_edit.php");
            exit;
        }
        // Hash the new password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        // Include the password update in the query
        $query .= ", password = ?";
        $parameters[] = $hashedPassword;
    }
    $query .= " WHERE username = ?";
    $parameters[] = $userId;
    $stmt = $pdo->prepare($query);
    $stmt->execute($parameters);
    $_SESSION['user'] = $username;
    $userId = $username;
    alert("Profile was successfully updated");
}


// Retrieve user data from the database
$query = "SELECT * FROM user WHERE username = ?";
$stmt = $pdo->prepare($query);
$stmt->bindValue(1, $userId, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <style>
        .invalid-feedback {
            font-size: 14px !important;
        }
    </style>
</head>


<body>
    <?php include "NavBar.php"; ?>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha384-xBuQ/xzmlsLoJpyjoggmTEz8OWUFM0/RC5BsqQBDX2v5cMvDHcMakNTNrHIW2I5f" crossorigin="anonymous"></script>

    <div class="container mt-3 mb-3">
        <div class="card">
            <div class="card-header text-center">Edit Profile</div>
            <div class="card-body">
                <form method="post" id="form-register" action="user_edit.php" class="needs-validation" novalidate>
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" value="<?php echo $user['name']; ?>" maxlength="128" class="form-control" id="name" required pattern="[a-zA-ZÀ-ÖØ-öø-ÿ\. \-]+" name="name">
                        <div class="invalid-feedback">Please input a valid name</div>
                    </div>
                    <!-- Input for the user to insert the username !-->
                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" value="<?php echo $user['username']; ?>" maxlength="128" class="form-control" id="username" required name="username" pattern="[A-Za-z0-9_]{4,20}">
                        <div id="username-error" class="invalid-feedback">Please input a valid username: 4-20 characters and can contain letters, numbers and underscores</div>
                    </div>

                    <!-- Input for the user to insert the email !-->
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input onclick="" value="<?php echo $user['email']; ?>" maxlength="128" type="email" class="form-control" id="email" required name="email">
                        <div id="email-error" class="invalid-feedback">Please input a valid email</div>
                    </div>

                    <!-- Input for the user to insert the password !-->
                    <div class="form-group">
                        <label for="password" class="form-label">New Password</label>
                        <input autocomplete="new-password" type="password" minlength="6" class="form-control" id="password" name="new_password">
                        <div class="invalid-feedback">Please choose a password with a minimum of 6 characters</div>
                    </div>

                    <!-- Input for the user to repeat the password !-->
                    <div class="form-group">
                        <label for="repeat_password">Repeat New Password</label>
                        <input autocomplete="off" type="password" minlength="6" class="form-control" id="repeat_password" name="repeat_password">
                        <div class="invalid-feedback">The Passwords don't match</div>
                    </div>


                    <button type="submit" name="submit_register" class="btn btn-primary">Update</button>
                </form>
            </div>
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
            const emailToTest = $("#email").val()
            if (emailToTest != "<?= $user['email'] ?>") {
                return $.ajax({
                    type: "POST",
                    url: "functions/ajax.php",
                    data: {
                        email_verify: emailToTest
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
        }

        //AJAX used to calls the function verify_username_exists in the file functios/ajax.php
        function usernameVerify() {
            const usernameToTest = $("#username").val()
            if (usernameToTest != "<?= $user['username'] ?>") {
                return $.ajax({
                    type: "POST",
                    url: "functions/ajax.php",
                    data: {
                        username_verify: usernameToTest
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
        $("#repeat_password").focusout(function() {
            var input = document.getElementById('repeat_password');
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