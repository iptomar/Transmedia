<?php
include "NavBar.php";
if (!isset($_GET["id"])) {
    $_GET["id"] = "";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="./style/add_video_to_story.css">
    <title>Story Creation Options</title>
</head>

<body>
    <h3 class="w-100 text-center mt-5 mb-3">Story was successfully created</h3>
    <div class="w-100 text-center">
        <a href="addVideoToStory.php?id=<?= $_GET["id"] ?>" class="btn btn-outline-primary mb-3" style="width:200px">Add Videos</a><br>
        <a href="#" class="btn btn-outline-primary mb-3" style="width:200px">Add Audio</a><br>
        <a href="#" class="btn btn-outline-primary mb-3" style="width:200px">Add Images</a><br>
        <a href="#" class="btn btn-outline-primary mb-3" style="width:200px">Add Text</a><br>
    </div>
    <?php
    include "footer.php";
    ?>
</body>

</html>