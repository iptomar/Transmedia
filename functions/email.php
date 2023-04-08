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
    $result = mail($to, $subject, $message, $headers);

    //If the email is successfully sent return the verification key else return null 
    if ($result) {
        return $ver_key;
    }
    return null;
}
