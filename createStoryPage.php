<?php
require "config/connectdb.php";


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Create the story</title>
</head>
<body>
<?php
    $currPage = 'storyPage';
    include "NavBar.php";
    $storyPage = 'storyPage';
    ?>

    <div class="Name">
        <label for="name">Name</label>
    </div>

    <div class="description">
        <label for="description">Description</label>
    </div>

    <div class="author">
        <label for="author">Author</label>
    </div>

</body>
</html>