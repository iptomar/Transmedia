<?php 
require "../config/connectdb.php";

//Depensing on the data received verify email or username
if (isset($_POST['email_verify'])) {
    verify_email_exists();
}else if(isset($_POST['username_verify'])){
    verify_username_exists();
}

//Verify if the email exists in the database
function verify_email_exists(){
    global $pdo;
    $sql = 'SELECT email from user WHERE email=? LIMIT 1';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_POST['email_verify']]);
    if($stmt->fetchColumn()){
        //if the value exists 
        echo "true";
    }else{
        //if the value doesn't exists echo false
        echo "false";
    };
}

//Verify if the username exists in the database
function verify_username_exists(){
    global $pdo;
    $sql = 'SELECT username from user WHERE username=? LIMIT 1';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_POST['username_verify']]);
    if($stmt->fetchColumn()){
        //if the value exists echo true
        echo "true";
    }else{
        //if the value doesn't exists echo true
        echo "false";
    };
}

?>