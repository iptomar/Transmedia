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
<html>
  <head>
    <title text-align: center>Transmedia - Share Your Stories</title>
    <style>
      h1, p {
        font-family: Arial;
      }
    </style>
  </head>
  <body>
    <?php
        $currPage = 'index';
        include "NavBar.php";
        $index = 'index';
    ?>
    <h1>Welcome to Transmedia</h1>
    <p></p>
    <p>Transmedia is a platform where you can share your stories and videos about anything.</p>
    <?php
        include "footer.php";
    ?>
  </body>
</html>

