<?php
require "config/connectdb.php";
require "NavBar.php";
if (!isset($_GET["key"])) {
    die("<h3 class='w-100 text-center  mt-5'>Invalid link</h2>");
}

$verkey = $_GET["key"];
//select the id of the user that has the verification key passed via the parameter "key"
$qry = $pdo->prepare('SELECT id, verificationKey FROM user WHERE verificationKey = ?');
$qry->execute([$verkey]);
$results = $qry->fetch();
//if the verification key is found
if ($qry->rowCount() == 1) {
    //update the user the key belongs to as verified and remove the verification key from the db
    $qry = $pdo->prepare('UPDATE user SET verified = ?, verificationKey = ? WHERE id = ?');
    $result = $qry->execute([true, '', $results["id"]]);
    if ($qry->rowCount() > 0) {
        echo "<h3 class='w-100 text-center mt-5'>Email was successfully verified</h2>";
    } else {
        die("<h3 class='w-100 text-center mt-5'>Something went wrong while verifying the account, please try again</h2>");
    }
} else {
    die("<h3 class='w-100 text-center  mt-5'>Invalid key</h2>");
}
