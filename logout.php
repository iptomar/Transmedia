<?php
session_start();
//Unset the Session Variable user
unset($_SESSION['user']);
//Redirect to index.php
header("location: index.php");
