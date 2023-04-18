<?php
require "config/connectdb.php";

$story = $pdo->prepare('SELECT name,description,author FROM story WHERE id= ?');
$story->execute([$_GET['id']]);
$storyFetch = $story->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Story Selected</title>
</head>
<body>
    <?php
    include "NavBar.php";
    ?>
    <div class="Name">
        <label for="name" style="font-size:20px; font-weight: bold;">Name</label>
        <p>
        <?php
            print($storyFetch['name'])
        ?>
        </p>
    </div>

    <div class="description">
        <label for="description" style="font-size:20px; font-weight: bold;">Description</label>
        <p>
        <?php
            if(print($storyFetch['description'] = null)){
                print("no description");
            }else{
                print($storyFetch['description']);
            }
        ?>
        </p>
    </div>

    <div class="author">
        <label for="author" style="font-size:20px; font-weight: bold;">Author</label>
        <p>
        <?php
            print($storyFetch['author'])
        ?>
        </p>
    </div>
</body>
</html>