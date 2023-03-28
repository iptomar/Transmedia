<?php
require "config/connectdb.php";
echo "Transmedia";

if (isset($_POST['submitButton'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $author = $_POST['author'];
  
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
    $createStory = 'createStory';
    ?>
<form>
    <!-- Tell the user to insert the Name !-->
    <div class="form-group">
        <label for="name">Name</label>
        <input class="form-control" id = "name" required name="name" type="text">
    </div>

    <!-- Tell the user to insert the Description !-->
    <div class="form-group">
        <label for="description">Description</label>
        <input class="form-control" id = "description" required name="description" type="text">
    </div>

    <!-- Tell the user to insert the Author !-->
    <div class="form-group">
        <label for="author">Author</label>
        <input class="form-control" id = "author" required name="author" type="text">
    </div>
  <button type="submit" name="submitButton" class="btn btn-primary">Submit</button>
</form>
</body>
</html>