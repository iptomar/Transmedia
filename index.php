<?php
require "config/connectdb.php";
echo "Transmedia";

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
    <title>Transmedia</title>
</head>
<body>
    <a href= "createStory.php">Create Story</a>
</body>
</html>