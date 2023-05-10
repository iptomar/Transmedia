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
//Pattern to verify if the text is a youtube video
$pattern = "/^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|live\/|shorts\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/";



if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_video'])) {
    //Get video type
    $type = $_POST["videotype"];
    //If no story was selected show error message and reload the page
    if (!isset($_POST['id'])) {
        alert("ERROR occurred when selecting the story");
    }
    //Get story id
    $storyId = $_POST['id'];

    $valid = true;
    //Verify if story belongs to the user
    $found = in_array($storyId, array_column($stories, 'id'));
    if (!$found) {
        $valid = false;
        alert("ERROR please select a valid story");
    }

    //Get video duration from hidden input
    if (isset($_POST["durationVideo"]) && $_POST["durationVideo"] > 0) {
        $duration =  $_POST["durationVideo"];
    } else {
        //If the duration is not set show error message
        alert("ERROR occurred when getting the duration");
        $valid = false;
    }

    //Save the video to a variable, and check the data, according to the video type
    if ($type == "text" && $valid) {
        $video = $_POST["chooseVideo"];
        $validYoutube = preg_match($pattern, $video,  $matches);
        if (!$validYoutube) {
            alert("ERROR link is not a youtube video");
        } else {
            //Get the youtube video id that is going to be saved in the database
            $video = $matches[1];
        }
    } elseif ($type == "file" && $valid) {
        $mimeType = mime_content_type($_FILES['chooseVideo']['tmp_name']);
        $fileType = explode('/', $mimeType)[0];
        if (!$fileType == "video") {
            alert("ERROR file is not a valid video");
            die();
        }
        //Save the video with a new name
        $video = generate_file_name("video_", "chooseVideo");
        if (!save_file("./files/story_$storyId/video/", $video, "chooseVideo")) {
            alert("ERROR occurred while saving file");
            die();
        }
    }

    if ($valid) {
        try {

            //After all checks are complete, insert the video reference into the database, 
            //while calculating the order, adding as the last one in the story
            $sql = "INSERT into video(storyId,link,videoType,duration,storyOrder) 
            SELECT ?,?,?,?,coalesce(MAX(storyOrder),0)+ 1 FROM video WHERE  storyId = ?;";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$storyId, $video, $type, $duration, $storyId]);
            reload_page();
        } catch (Exception $e) {
            echo '<script>alert("ERROR occured while connecting to the database")</script>';
        }
    }
} else {
    $type = "text";
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
    <title>Add video to story</title>
</head>

<body>
    <div class="container mt-3">
        <div class="card">
            <div class="card-header text-center">Add Video to Story</div>
            <div class="card-body">
                <br>
                <form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="option_youtube">
                            Select type of file:<br>
                        </label>
                        <div class="form-check">
                            <input onchange="changeFileType()" required class="form-check-input" type="radio" name="videotype" id="option_youtube" value="text" <?= $type == 'text' ? "checked" : "" ?>>
                            <label class="form-check-label w-100" for="option_youtube">
                                Youtube
                            </label>
                        </div>
                        <div class="form-check">
                            <input onchange="changeFileType()" required class="form-check-input" type="radio" name="videotype" id="option_file" value="file" <?= $type  == 'file' ? "checked" : "" ?>>
                            <label class="form-check-label w-100" for="option_file">
                                File
                            </label>
                        </div>
                        <input type="hidden" id="storyId" name="id" value="<?= isset($_GET['id']) ?  $_GET['id'] : "" ?>" />
                    </div>
                    <div id="previewAdd"></div>

                    <div class="mb-3">
                        <label for="chooseVideo" class="form-label">Choose Video:</label>
                        <input id="chooseVideo" class="form-control" required name="chooseVideo">
                    </div>
                    <input type="hidden" id="durationVideo" name="durationVideo" />

                    <input type="submit" name="add_video" class="w-100 btn btn-primary" style="margin-top: 10px">

                </form>
            </div>
        </div>
    </div>
    <script>
        document.querySelector('#option_youtube').dispatchEvent(new Event('change'));

        function changeFileType() {
            $("#previewAdd").html('')

            const fileTypeSelected = document.querySelector('input[name="videotype"]:checked').value;
            const fileInput = document.querySelector('#chooseVideo');
            if (fileTypeSelected === 'text') {
                fileInput.setAttribute('type', 'text');
                fileInput.setAttribute('placeholder', 'URL of the youtube video');

            } else if (fileTypeSelected === 'file') {
                <?php $type = "file" ?>
                fileInput.setAttribute('type', 'file');
                fileInput.setAttribute('accept', "video/*");
            }
        }
        // YouTube Player API Reference for iframe Embeds
        // https://developers.google.com/youtube/iframe_api_reference
        var tag = document.createElement('script');
        tag.src = "https://www.youtube.com/iframe_api";
        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

        var players = document.getElementsByClassName('player');
        var playerObjects = [];

        //After the Youtube FrameAPI is ready
        function onYouTubeIframeAPIReady() {
        }

        function initPlayer(id) {
            player = new YT.Player('player', {
                height: '390',
                width: '640',
                videoId: id,
                playerVars: {
                    'autoplay': 0,
                    'controls': 1
                },
                events: {
                    'onReady': onPlayerReady
                }
            });

        }

        //When the youtube player is ready the API calls this function
        function onPlayerReady(event) {
            document.getElementById("durationVideo").setAttribute('value', player.getDuration());
        }


        //When the input used to select the video is changed
        $("#chooseVideo").on("change", function(evt) {
            const fileTypeSelected = document.querySelector('input[name="videotype"]:checked').value;

            var input = document.getElementById('chooseVideo');
            //If the type choosen is file
            if (fileTypeSelected == "file") {
                console.log(fileType)
                $("#previewAdd").html('')
                document.getElementById("durationVideo").setAttribute('value', "");
                var fileType = this.files[0]["type"];
                if (fileType.split('/')[0] === 'video') {
                    input.setCustomValidity("");
                    //Add a video tag to previewAdd the selected video
                    $("#previewAdd").html('<div class="embed-responsive embed-responsive-16by9"><video id="videofile" width="420" height="250" controls class="video"></video></div>');
                    var fileInput = document.getElementById('chooseVideo');
                    var fileUrl = window.URL.createObjectURL(fileInput.files[0]);
                    $(".video").attr("src", fileUrl);

                    var videoPlayer = document.getElementById("videofile")
                    //Add the duration to the hidden input
                    videoPlayer.addEventListener('durationchange', function() {
                        document.getElementById("durationVideo").setAttribute('value', videoPlayer.duration);
                    });

                    //Scroll to the bottom of the page, in order to keep the button visible
                    $("html, body").animate({
                        scrollTop: $(document).height()
                    });
                } else {
                    $("#previewAdd").html('')
                    //If the text is not a valid video file then do not allow submission of the form
                    input.setCustomValidity("File is not a valid video");
                }

            } else {
                //Verify if the text is a youtube link
                var url = $(this).val();
                var p = <?= $pattern ?>;
                var matches = url.match(p);
                if (matches) {
                    //If the text is a youtube link show a previewAdd of the video in an iframe
                    input.setCustomValidity("");
                    //Add the youtube video to a iframe by concatenating the link https://www.youtube.com/embed/ with the video id
                    $("#previewAdd").html('<div class="embed-responsive embed-responsive-16by9"><div id="player"></div></div>');

                    //Start the youtube player 
                    id = matches[1];
                    initPlayer(id);

                    //Scroll to the bottom of the page, in order to keep the button visible
                    $("html, body").animate({
                        scrollTop: $(document).height()
                    });
                } else {
                    $("#previewAdd").html('')
                    //If the text is not a valid youtube link then do not allow submission of the form
                    input.setCustomValidity("Youtube video is not valid");
                }

            }
        });
    </script>

</body>

</html>