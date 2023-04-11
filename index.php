<?php
require "config/connectdb.php";

/*Example of how to execute a SELECT with PDO
$stmt = $pdo->query('SELECT id, title FROM story');
$stories = $stmt->fetchAll(PDO::FETCH_ASSOC);*/

/*Generate password hash with salt
echo '<p>Joana = '.password_hash("joana123", PASSWORD_DEFAULT).'</p>'; 
echo '<p>Tiago = '.password_hash("tiago123", PASSWORD_DEFAULT).'</p>';*/


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="./style/index.css" type="text/css">
    <title>Transmedia</title>
</head>
<body>
    <?php
        $currPage = 'index';
        include "NavBar.php";
        $index = 'index';
    ?>
    
    <div>
        <div class = "content_text">
            <div class = "logo">
                <img src="./assets/landing_logo.svg"></img>
            </div>
            <div class = "text">
                TRANS<br>
                <strong>MEDIA</strong>
            </div>
        </div>

        <div class="availablestories">
            <a href="#">Available Stories</a>
        </div>
    <div>
</body>
</html>