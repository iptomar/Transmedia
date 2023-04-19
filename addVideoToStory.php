<!-- NEEDS IMPROVEMENTS IN SORCE CHANGING (CHANGE TO CHECK BOX, FOR EXAMPLE) -->

<?php

include "./NavBar.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="./style/add_video_to_story.css">
    <title>Add video to story</title>
</head>

<body>
    <form method="POST" style="padding-top: 15%;">
        <label for="YT-CB">
            Type 'file' or 'text' and click the button to change input source type<br>
            If other or no text is typed, input source defaults to 'file'
        </label>
        <br>
        <input name="YT-CB" id="YT-CB" type="text" />
        <input type="submit" value="Change file source" />
    </form>
    <form method="POST">
        <div class="form-group d-inline-block">
            <label for="chooseVideo" style="float: left">Choose Video</label>
            <input class="form-control" type=<?php
                                                if (isset($_POST["YT-CB"]) && $_POST["YT-CB"] != "") {
                                                    echo $_POST["YT-CB"];
                                                } elseif (isset($_SESSION["inputFile"])) {
                                                        echo $_SESSION["inputFile"];
                                                    } else {
                                                        $_SESSION["inputFile"] = "file";
                                                        echo $_SESSION["inputFile"];
                                                }

                                                ?> id="chooseVideo" required name="chooseVideo">



            </input>
            <input type="submit" value="Send" class="btn btn-primary" style="margin-top: 10px">
        </div>
    </form>
</body>

</html>

<?php



echo $_POST["chooseVideo"];

?>