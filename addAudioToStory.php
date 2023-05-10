<?php
require_once "verify_login.php";
require_once "Navbar.php";
require_once "config/connectdb.php";
include_once "./functions/useful.php";
//If id of story is not set
if (!isset($_GET['id']) || $_GET['id'] == null) {
    message_redirect("ERROR: Something went wrong", "my_stories.php");
    exit();
}

$stmt = $pdo->prepare('SELECT id,name, author FROM story where author=?');
$stmt->execute([$_SESSION['user']]);
$stories = $stmt->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_audio'])) {
    //Get story id
    $storyId = $_POST['id'];

    //Get video duration from hidden input
    if (isset($_POST["durationAudio"]) && $_POST["durationAudio"] > 0) {
        $duration =  $_POST["durationAudio"];
    } else {
        //If the duration is not set show error message
        alert("ERROR occurred when getting the duration");
        reload_page();
    }
    //Get audio name
    $audio_name = $_FILES['my_audio']['name'];
    $tmp_name = $_FILES['my_audio']['tmp_name'];

    //Get the audio extension
    $audio_ex = pathinfo($audio_name, PATHINFO_EXTENSION);
    //Convert the audio to loweer case to be able to compare with the allowed extensions
    $audio_ex = strtolower($audio_ex);
    //Allowed Extensions
    $allowed_extensions = array("mp3", 'wav');

    if (in_array($audio_ex, $allowed_extensions)) {
        //Save the audio with a new name
        $audio = generate_file_name("audio_", "my_audio");
        if (!save_file("./files/story_$storyId/audio/", $audio, "my_audio")) {
            echo '<p>Error while saving the audio</p>';
        }


        try {
            $sql = "INSERT into audio(id_story,audio, duration,storyOrder) 
            SELECT ?,?,?,coalesce(MAX(storyOrder),0)+ 1 FROM audio WHERE id_story = ?;";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$storyId, $audio, $duration, $storyId]);
            reload_page();
        } catch (Exception $e) {
            echo '<script>alert("ERROR occured while connecting to the database")</script>';
        }
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
    <title>Add Audio to story</title>
</head>

<body>
    <div class="container-sm mt-3">
        <div class="card">
            <div class="card-header text-center">Add Audio</div>
            <div class="card-body">
                <form method="post" id="form-audio" enctype="multipart/form-data">
                    <input type="hidden" id="storyId" name="id" value="<?= isset($_GET['id']) ?  $_GET['id'] : "" ?>" />
                    <input type="hidden" id="durationAudio" name="durationAudio" />

                    <div id="previewAudio"></div>

                    <div class="form-group">
                        <label for="audioFile">Choose Audio:</label>
                        <input accept=".mp3,.wav,.ogg" type="file" class="form-control-file" id="audioFile" name="my_audio">
                    </div>
                    <button type="submit" name="add_audio" class="w-100 btn btn-primary" style="margin-top: 10px">Add Audio</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        //When the input used to select the video is changed
        $("#audioFile").on("change", function(evt) {
            var input = document.getElementById('audioFile');
            var fileType = this.files[0]["type"];
            $("#previewAudio").html('')
            document.getElementById("durationAudio").setAttribute('value', "");

            if (fileType.split('/')[0] === 'audio' && canPlayAudio(fileType)) {

                input.setCustomValidity("");
                $("#previewAudio").html('<audio class="w-100 mb-3 mt-3 audio" id="audiofile" controls>Your browser does not support the audio tag.</audio>');
                var fileInput = document.getElementById('audioFile');
                var fileUrl = window.URL.createObjectURL(fileInput.files[0]);
                $(".audio").attr("src", fileUrl);

                var audioPlayer = document.getElementById("audiofile")
                //Add the duration to the hidden input
                audioPlayer.addEventListener('loadedmetadata', function() {
                    document.getElementById("durationAudio").setAttribute('value', audioPlayer.duration);
                });

            } else {
                //If the text is not a valid video file then do not allow submission of the form
                input.setCustomValidity("File is not a valid audio");
            }
        });

        function canPlayAudio(mimeType) {
            var audio = document.createElement('audio');
            return !!(audio.canPlayType && audio.canPlayType(mimeType).replace(/no/, ''));
        }
    </script>
</body>

</html>