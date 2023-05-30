<?php
require_once "./functions/useful.php";
require_once "config/connectdb.php";
if (isset($_POST['durationChange'])) {
    $media_id = $_POST['storyID'];
    $imageID = $_POST['imageID'];
    $duration = $_POST['duration'];
    $sql = "UPDATE image SET duration = ? WHERE id=? and storyID = ?";


    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$duration,$imageID, $media_id])) {
        reload_page();
    } else {
        message_redirect("Something went wrong when changing the image duration", "edit_story.php?id=$id");
    }
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
    <title>Add image to story</title>
</head>

<body>
    <div class="container-sm mt-3">
        <div class="card">
            <div class="card-header text-center">Change image duration</div>
            <div class="card-body">
                <form method="post" id="form-image" enctype="multipart/form-data">
                    <input type="hidden" id="storyID" name="storyID" value="<?= isset($_GET['id']) ?  $_GET['id'] : "" ?>" />
                    <input type="hidden" id="imageID" name="imageID" />
                    <div class="form-group">
                        <label for="duration">Duration in seconds:</label>
                        <input class="form-control" type="number" id="duration" name="duration" required />
                    </div>
                    <button type="submit" name="durationChange" class="w-100 btn btn-primary" style="margin-top: 10px">Change</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>