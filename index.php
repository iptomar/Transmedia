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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Transmedia</title>
</head>
<body>
    <?php
        $currPage = 'index';
        include "NavBar.php";
        $index = 'index';
    ?>
</body>
</html>