<?php
//echo $_SESSION["user"];
require "restriction_story_user.php";

if (isset($_POST['submitButton'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    //The author is the currently logged in user's username, saved in the session variable 'user'
    $author = $_SESSION["user"];

    // sql query para inserir dados na base de dados
    $sql = "INSERT INTO Story (name, description, author) VALUES (?, ?, ?)";
    $pdo->prepare($sql)->execute([$name, $description, $author]);

    $id = $pdo->lastInsertId();
    header("location: edit_story.php?id=$id");
}

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
    $currPage = 'createStory';
    include "NavBar.php";
    $createStory = 'createStory';
    ?>
    <div class="container mt-3 mb-3">
        <div class="card">
            <div class="card-header text-center">Create Story</div>
            <div class="card-body">
                <form method="POST">
                    <!-- Tell the user to insert the Name !-->
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input class="form-control" id="name" required name="name" type="text">
                    </div>

                    <!-- Tell the user to insert the Description !-->
                    <div class="form-group">
                        <label for="description">Description</label>
                        <input class="form-control" id="description" name="description" type="text">
                    </div>
                    <button type="submit" name="submitButton" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
    <?php
    include "footer.php";
    ?>
</body>

</html>