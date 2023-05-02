<?php
require "verify_login.php";
include "./NavBar.php";
include "./functions/useful.php";

$stmt = $pdo->prepare('SELECT id,name, author FROM story where author=?');
$stmt->execute([$_SESSION['user']]);
$stories = $stmt->fetchAll(PDO::FETCH_ASSOC);
//Pattern to verify if the text is a youtube video
$pattern = "/^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|live\/|shorts\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/";
if (!isset($_GET["videotype"]) || $_GET["videotype"] == null) {
    $_GET["videotype"] = "text";
}
//Get video type
$type = $_GET["videotype"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //If no story was selected show error message and reload the page
    if (!isset($_POST['story'])) {
        message_redirect("ERROR occurred when selecting the story", $redirect);
        die();
    }
    //Get story id
    $storyId = $_POST['story'];

    //Verify if story belongs to the user
    $found = in_array($storyId, array_column($stories, 'id'));
    if (!$found) {
        message_redirect("ERROR please select a valid story", $redirect);
        die();
    }

    //Link to redirect back to page, with options selected, after error occurs
    $redirect = basename(__FILE__) . "?videotype=$type&story=$storyId";

    //Get video duration from hidden input
    if (isset($_POST["duration"]) && $_POST["duration"] > 0) {
        $duration =  $_POST["duration"];
    } else {
        //If the duration is not set show error message
        message_redirect("ERROR occurred when getting the duration", $redirect);
        die();
    }

    //Save the video to a variable, and check the data, according to the video type
    if ($type == "text") {
        $video = $_POST["chooseVideo"];
        $valid = preg_match($pattern, $video,  $matches);
        if (!$valid) {
            message_redirect("ERROR link is not a youtube video", $redirect);
            die();
        } else {
            //Get the youtube video id that is going to be saved in the database
            $video = $matches[1];
        }
    } elseif ($type == "file") {
        $mimeType = mime_content_type($_FILES['chooseVideo']['tmp_name']);
        $fileType = explode('/', $mimeType)[0];
        if (!$fileType == "video") {
            message_redirect("ERROR file is not a valid video", $redirect);
            die();
        }
        //Save the video with a new name
        $video = generate_file_name("video_", "chooseVideo");
        if (!save_file("./files/story_$storyId/video/", $video, "chooseVideo")) {
            message_redirect("ERROR occurred while saving file", $redirect);
            die();
        }
    } else {
        //if the video type is not text or file then there is a error
        die();
    }

    try {

        //After all checks are complete, insert the video reference into the database, 
        //while calculating the order, adding as the last one in the story
        $sql = "INSERT into video(storyId,link,videoType,duration,storyOrder) 
        SELECT ?,?,?,?,coalesce(MAX(storyOrder),0)+ 1 FROM video WHERE  storyId = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$storyId, $video, $type, $duration, $storyId]);
    } catch (Exception $e) {
        echo '<script>alert("ERROR occured while connecting to the database")</script>';
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
    <title>Add video to story</title>
</head>

<body>
    <div class="container mt-3">
        <div class="card">
            <div class="card-header text-center">Add Video to Story</div>
            <div class="card-body">
                <form method="GET" id="formGet">
                    <br>
                    <div class="form-group">
                        <label for="option_youtube">
                            Select type of file:<br>
                        </label>
                        <div class="form-check">
                            <input required class="form-check-input" type="radio" name="videotype" id="option_youtube" value="text" <?= $_GET['videotype'] == 'text' ? "checked" : "" ?>>
                            <label class="form-check-label w-100" for="option_youtube">
                                Youtube
                            </label>
                        </div>
                        <div class="form-check">
                            <input required class="form-check-input" type="radio" name="videotype" id="option_file" value="file" <?= $_GET['videotype'] == 'file' ? "checked" : "" ?>>
                            <label class="form-check-label w-100" for="option_file">
                                File
                            </label>
                        </div>
                        <input type="hidden" id="storyId" name="story" value="<?= isset($_GET['story']) ?  $_GET['story'] : "" ?>" />
                    </div>

                </form>
                <form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="story">Choose Story:</label>
                        <select required id="storySelect" name="story" class="form-select mb-3 w-100" aria-label=".form-select-lg example">
                            <option value="">Select the story</option>
                            <?php
                            foreach ($stories as $story) :
                                $selected = "";
                                if ($story['id'] == $_GET['story']) {
                                    $selected = "selected";
                                }
                                echo "<option value='" . $story['id'] . "'$selected>" . $story['name'] . "</option>";
                            endforeach;
                            ?>
                        </select>
                    </div>
                    <div id="preview"></div>

                    <div class="mb-3">
                        <label for="chooseVideo" class="form-label">Choose Video:</label>
                        <input class="form-control" required name="chooseVideo" type=<?= $_GET["videotype"] ?> <?php echo $_GET["videotype"]  == "file" ?
                                                                                                                    'accept="video/*"' :
                                                                                                                    'placeholder="URL of the youtube video"'; ?> id="chooseVideo">
                    </div>
                    <input type="hidden" id="duration" name="duration" />

                    <input type="submit" name="add_video" class="w-100 btn btn-primary" style="margin-top: 10px">

                </form>
            </div>
        </div>
    </div>
    <script>
        //When changing the radio buttons submit their form
        $('input[type=radio]').change(function() {
            $(this).closest("form").submit();
        });

        //When story selection changes, the selector changes, update the GET params
        $('#storySelect').on('change', function() {
            $('#storyId').attr('value', $('#storySelect').val())
            //Get values and add to link
            var getValues = "?videotype=" + $('input[type=radio]:checked').val() + "&story=" + $('#storySelect').val()
            window.history.replaceState(null, null, getValues);
        });

        // YouTube Player API Reference for iframe Embeds
        // https://developers.google.com/youtube/iframe_api_reference
        var tag = document.createElement('script');
        tag.src = "https://www.youtube.com/iframe_api";
        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

        var player;
        var time;

        function onYouTubeIframeAPIReady(id) {
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
            document.getElementById("duration").setAttribute('value', player.getDuration());
        }


        //When the input used to select the video is changed
        $("#chooseVideo").on("change", function(evt) {
            var input = document.getElementById('chooseVideo');
            //If the type choosen is file
            if ("<?= $_GET["videotype"] ?>" == "file") {
                $("#preview").html('')
                document.getElementById("duration").setAttribute('value', "");
                var fileType = this.files[0]["type"];
                if (fileType.split('/')[0] === 'video') {
                    input.setCustomValidity("");
                    //Add a video tag to preview the selected video
                    $("#preview").html('<div class="embed-responsive embed-responsive-16by9"><video id="videofile" width="420" height="250" controls class="video"></video></div>');
                    var fileInput = document.getElementById('chooseVideo');
                    var fileUrl = window.URL.createObjectURL(fileInput.files[0]);
                    $(".video").attr("src", fileUrl);

                    var videoPlayer = document.getElementById("videofile")
                    //Add the duration to the hidden input
                    videoPlayer.addEventListener('durationchange', function() {
                        document.getElementById("duration").setAttribute('value', videoPlayer.duration);
                    });

                    //Scroll to the bottom of the page, in order to keep the button visible
                    $("html, body").animate({
                        scrollTop: $(document).height()
                    });
                } else {
                    $("#preview").html('')
                    //If the text is not a valid video file then do not allow submission of the form
                    input.setCustomValidity("File is not a valid video");
                }

            } else {
                //Verify if the text is a youtube link
                var url = $(this).val();
                var p = <?= $pattern ?>;
                var matches = url.match(p);
                if (matches) {
                    //If the text is a youtube link show a preview of the video in an iframe
                    input.setCustomValidity("");
                    //Add the youtube video to a iframe by concatenating the link https://www.youtube.com/embed/ with the video id
                    $("#preview").html('<div class="embed-responsive embed-responsive-16by9"><div id="player"></div></div>');

                    //Start the youtube player 
                    onYouTubeIframeAPIReady(matches[1]);
                    //Scroll to the bottom of the page, in order to keep the button visible
                    $("html, body").animate({
                        scrollTop: $(document).height()
                    });
                } else {
                    $("#preview").html('')
                    //If the text is not a valid youtube link then do not allow submission of the form
                    input.setCustomValidity("Youtube video is not valid");
                }
            }
        });
    </script>


</body>

</html>