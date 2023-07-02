<?php
require_once "./functions/useful.php";
require_once "config/connectdb.php";
if (isset($_POST['durationChangeText'])) {
    $media_id = $_POST['id_story'];
    $textID = $_POST['textID'];
    $duration = $_POST['duration'];
    $sql = "UPDATE text SET duration = ? WHERE id=? and id_story = ?";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$duration, $textID, $media_id])) {
        reload_page();
    } else {
        message_redirect("Something went wrong when changing the text duration", "edit_story.php?id=$id");
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
    <title>Add text to story</title>
</head>

<body>
    <div class="container-sm mt-3">
        <div class="card">
            <div class="card-header text-center">Change text duration</div>
            <div class="card-body">
                <form method="post" id="form-text" enctype="multipart/form-data">
                    <input type="hidden" id="id_story" name="id_story" value="<?= isset($_GET['id']) ?  $_GET['id'] : "" ?>" />
                    <input type="hidden" id="textID" name="textID" />
                    <div class="form-group">
                        <label for="duration">Duration in seconds:</label>
                        <input class="form-control" type="number" id="duration" name="duration" required />
                    </div>
                    <button type="submit" name="durationChangeText" class="w-100 btn btn-primary" style="margin-top: 10px">Change</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>