<?php
require "../config/connectdb.php";
require "validate.php";
//Depensing on the data received verify email or username
if (isset($_POST['email_verify'])) {
    echo (verify_email_exists($_POST['email_verify'])) ?  "true" : "false";
} else if (isset($_POST['username_verify'])) {
    echo verify_username_exists($_POST['username_verify']) ? "true" : "false";
}