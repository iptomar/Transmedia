<?php
include "./NavBar.php";

$stmt = $pdo->prepare('SELECT id,name, author FROM story');
$stmt->execute();
$stories = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!isset($_GET["videotype"])) {
    $_GET["videotype"] = "text";
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_GET["videotype"] == "text") {
        $video = $_POST["chooseVideo"];
        $valid = preg_match("/^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/", $video);
        if ($valid) {
            // echo "Add Youtube to " . $_POST['story'];
        }
    } elseif ($_GET["videotype"] == "file") {
        $mimeType = mime_content_type($_FILES['chooseVideo']['tmp_name']);
        $fileType = explode('/', $mimeType)[0];
        if ($fileType == "video") {
            // echo "Add Video File to " . $_POST['story'];
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="./style/add_video_to_story.css">
    <title>Add video to story</title>
</head>

<body>
    <div class="container mt-3">
        <div class="card">
            <div class="card-header text-center">Add Video to Story</div>
            <div class="card-body">
                <form method="GET">
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
                    </div>

                </form>
                <form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="story" style="float: left">Choose Story:</label>
                        <select required name="story" class="form-select form-select-lg mb-3 w-100" aria-label=".form-select-lg example">
                            <option value="">Select the story</option>
                            <?php
                            foreach ($stories as $story) :
                                echo '<option value="' . $story['id'] . '">' . $story['name'] . '</option>';
                            endforeach;
                            ?>
                        </select>
                    </div>
                    <div id="preview"></div>

                    <div class="form-group">
                        <label for="chooseVideo" style="float: left">Choose Video:</label>
                        <input class="form-control" required name="chooseVideo" type=<?= $_GET["videotype"] ?> <?php echo $_GET["videotype"]  == "file" ?
                                                                                                                    'accept="video/*"' : 'placeholder="URL of the youtube video"'; ?> id="chooseVideo">
                        </input>
                    </div>
                    <input type="submit" name="add_video" class="w-100 btn btn-primary" style="margin-top: 10px">

                </form>
            </div>
        </div>
    </div>
    <script>
        $('input[type=radio]').change(function() {
            $(this).closest("form").submit();
        });

        $("#chooseVideo").on("change", function(evt) {
            var input = document.getElementById('chooseVideo');

            if ("<?= $_GET["videotype"] ?>" == "file") {
                var fileType = this.files[0]["type"];
                if (fileType.split('/')[0] === 'video') {
                    input.setCustomValidity("");
                    //Add a video tag to preview the selected video
                    $("#preview").html('<div><video width="420" height="250" controls class="video"></video></div>');
                    var fileInput = document.getElementById('chooseVideo');
                    var fileUrl = window.URL.createObjectURL(fileInput.files[0]);
                    $(".video").attr("src", fileUrl);
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
                var p = /^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
                var matches = url.match(p);
                if (matches) {
                    //If the text is a youtube link show a preview of the video in an iframe
                    input.setCustomValidity("");
                    //Add the youtube video to a iframe by concatenating the link https://www.youtube.com/embed/ with the video id
                    $("#preview").html('<div><iframe width="420" height="315" src="https://www.youtube.com/embed/' + matches[1] + '"</iframe></div>');
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