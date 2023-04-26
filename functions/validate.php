<?php

function generate_email_verification(PDO $pdo, $email)
{
    //Generate the email verification key
    $ver_key = md5("email" . $email) . bin2hex(openssl_random_pseudo_bytes(16));

    //Update the user in order to insert the verification key in the database
    $stmt = $pdo->prepare('UPDATE user SET verificationKey = ? WHERE email = ?');
    $stmt->execute([$ver_key, $email]);


    //Send the email with the verification link, this can fail if no STMP server is set up
    $to = $email;
    $link = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']);
    $link_verify = $link . "/verify.php?key=$ver_key";
    $subject = "Transmedia Email Verification";
    $message = "Please click the following link to confirm your email:\n $link_verify";
    $headers = "From: transmedia.gp@gmail.com";

    //If the email is not successfully sent show an alert with an error message
    if (!@mail($to, $subject, $message, $headers)) {
        echo "<script>
                alert('An error occured while sending the verification email, please try to login and request a new verification email');
                window.location.replace('index.php');        
            </script>";
    } else {
        //If the verification key was sent show an alert warning the user
        echo "<script>
                alert('An email was sent to $email. Please click the link in it to verify your account');   
                window.location.replace('index.php');          
            </script>";
    }
    return $ver_key;
}


//Verify if the email exists in the database
function verify_email_exists($email)
{
    global $pdo;
    $sql = 'SELECT email from user WHERE email=? LIMIT 1';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    if ($stmt->fetchColumn()) {
        //if the value exists return true
        return true;
    } else {
        //if the value doesn't exists return false
        return false;
    };
}

//Verify if the username exists in the database
function verify_username_exists($username)
{
    global $pdo;
    $sql = 'SELECT username from user WHERE username=? LIMIT 1';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    if ($stmt->fetchColumn()) {
        //if the value exists return true
        return true;
    } else {
        //if the value doesn't exists return false
        return false;
    };
}

//Verify if name is valid, return false if it isn't and true if it is
function verify_name($name)
{
    if (empty($name) || !preg_match('/^[a-zA-ZÀ-ÖØ-öø-ÿ. -]*$/', $name)) {
        return false;
    }
    return true;
}

//Verify if username is valid, return false if it isn't and true if it is
function verify_username($username)
{
    if (empty($username) || !preg_match('/^[A-Za-z0-9_]{4,20}$/', $username)) {
        return false;
    }
    return true;
}

//Verify if email is valid, return false if it isn't and true if it is
function verify_email($email)
{
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    return true;
}

//Verify if password is valid, return false if it isn't and true if it is
function verify_password($password)
{
    if (empty($password) || strlen($password) < 6) {
        return false;
    }
    return true;
}

//Verify if repeat password is valid, return false if it isn't and true if it is
function verify_repeat_password($password, $repeat_pass)
{
    if (empty($repeat_pass) || $password != $repeat_pass) {
        return false;
    }
    return true;
}


//Verify if the values in the register page are correct, if they're not send the corresponding error message
function verify_register_input($name, $username, $email, $password, $repeat_pass)
{
    if (!verify_name($name)) return "Name is not valid";
    if (!verify_username($username)) return "Username is not valid it should have between 4 and 20 characters and can only contain letters, numbers and underscores";
    if (!verify_email($email)) return "Email is not valid";
    if (!verify_password($password)) return "Password is not valid it should have a minimum of 6 characters";
    if (!verify_repeat_password($password, $repeat_pass)) return "Passwords don't match";
    if (verify_email_exists($email)) return "Email already exits";
    if (verify_username_exists($email)) return "Username already exits";

    return null;
}
