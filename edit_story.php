<?php
require "./functions/useful.php";
require "config/connectdb.php";
require "NavBar.php";
//If id of story is not set
if (!isset($_GET['id'])) {
    message_redirect("ERROR: Something went wrong", "my_stories.php");
}
$id = $_GET['id'];
$name = '';
$description = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['cancel'])) {
        header("location: selectedStoryPage.php?id=$id");
        exit();
    }
    $name = $_POST['name'];
    $description = $_POST['description'];
    $qry = $pdo->prepare('UPDATE story SET name = ?, description = ? WHERE id = ?');
    $result = $qry->execute([$_POST['name'],  $_POST['description'], $id]);
    if ($qry->rowCount() > 0) {
        message_redirect("Story was successfully updated", "selectedStoryPage.php?id=$id");
    } else {
        alert("Something went wrong while updating the story, please try again");
    }
} else {
    $sql_story = $pdo->prepare('SELECT name, description, author FROM story WHERE story.id = ?');
    $sql_story->execute([$_GET['id']]);
    $story = $sql_story->fetch(PDO::FETCH_ASSOC);
    //If story does not belong to the current user send the user to the selectedStoryPage
    if ($story["author"] != $_SESSION['user']) {
        message_redirect("ERROR: Something went wrong", "my_stories.php");
    }
    $name = $story['name'];
    $description = $story['description'];

}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Edit story <?= $name ?></title>
    <link rel="stylesheet" href="./style/edit_story.css">
</head>

<body>
    <div class="container mt-3 mb-3">
        <div class="card">
            <div class="card-header text-center">Edit Story</div>
            <div class="card-body">
                <form method="POST" action="edit_story.php?id=<?= $id; ?>">
                    <!-- Tell the user to insert the Name !-->
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input class="form-control" id="name" required name="name" type="text" value="<?= $name; ?>">
                    </div>

                    <!-- Tell the user to insert the Description !-->
                    <div class="form-group">
                        <label for="description">Description</label>
                        <input class="form-control" id="description" required name="description" type="text" value="<?= $description ?>">
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <button type="submit" name="submit" class="btn btn-primary w-100">Submit</button>
                        </div>
                        <div class="col-6">
                            <button type="submit" name="cancel" class="btn btn-danger w-100" formnovalidate>Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php
    include "footer.php";
    ?>

</body>

</html>