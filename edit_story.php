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

    // Fetch the story videos
    $sql_videos = $pdo->prepare('SELECT id, link, storyId, videoType, storyOrder,duration FROM video WHERE storyId = ?');
    $sql_videos->execute([$_GET['id']]);
    $videos = $sql_videos->fetchAll(PDO::FETCH_ASSOC);

    // Calculate total duration of all videos
    $total_duration = 0;
    foreach ($videos as $video) {
        $total_duration += $video['duration'];
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

                    <div class="form-group mb-3">
                        <label for="description">Videos</label>
                        <div class="w-100 mb-3">
                        <div id="preview"></div></div>
                        <div class="videos-wrapper">
                            <?php
                            $time = 0;
                            foreach ($videos as $video) {
                                echo "<div class='video-container' data-duration='" . $video["duration"] . "'>";
                                if ($video["videoType"] == "file") {
                                    echo '<video controls src="./files/story_' . $video["storyId"] . '/video/' . $video["link"] . '"></video>';
                                } elseif ($video["videoType"] == "text") {
                                    echo '<div class="player" data-video-id="' . $video["link"] . '"></div>';
                                }
                                echo '<span class="duration"></span>';

                                echo "</div>";
                            } ?>
                        </div>
                        <div class="mt-1 duration-line"></div>
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

    <script>
        var videos = [];
        const totalDuration = <?= $total_duration ?>;
        var time = 0;

        //Format the time
        function timeFormat(duration) {
            duration = parseInt(duration);
            // Hours, minutes and seconds
            const hrs = ~~(duration / 3600);
            const mins = ~~((duration % 3600) / 60);
            const secs = ~~duration % 60;
            let ret = "";
            if (hrs > 0) {
                ret += "" + hrs + ":" + (mins < 10 ? "0" : "");
            }
            ret += "" + mins + ":" + (secs < 10 ? "0" : "");
            ret += "" + secs;
            return ret;
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
            for (var i = 0; i < players.length; i++) {
                var player = players[i];
                var videoId = player.getAttribute('data-video-id');
                var playerObject = new YT.Player(player, {
                    height: '390',
                    width: '640',
                    videoId: videoId,
                    playerVars: {
                        'autoplay': 0,
                        'controls': 1
                    },
                });
                playerObjects.push(playerObject);
                var index = playerObjects.indexOf(playerObject);
                playerObject.getIframe().setAttribute('data-index', index);
            }

            //Set some of the video-container div's parameters dynamically
            setVideoContainer()
        }

        function previewVideo(nextVideo) {
            const clonedVideo = nextVideo.cloneNode(true);
            if (clonedVideo.tagName === 'IFRAME') {
                clonedVideo.removeAttribute('class');
                // Handle iframe video
                const videoId = clonedVideo.dataset.videoId;
                const playerObject = new YT.Player(clonedVideo, {
                    height: '390',
                    width: '640',
                    videoId: videoId,
                    playerVars: {
                        'autoplay': 1,
                        'controls': 1
                    },
                    events: {
                        'onReady': onPreviewReady,
                        'onStateChange': onPreviewChange
                    }
                });
            } else if (clonedVideo.tagName === 'VIDEO') {
                // Handle HTML5 video
                clonedVideo.addEventListener("ended", function() {
                    previewVideo(videos[videos.length - 1]);
                });
                clonedVideo.setAttribute('autoplay', 'true'); // add autoplay attribute to start playing the video
                clonedVideo.setAttribute('controls', 'true'); // add controls attribute to display the video controls
                clonedVideo.play();
            }
            const preview = document.querySelector('#preview');
            //Add the Video to the preview div
            preview.innerHTML = '';
            preview.appendChild(clonedVideo);
            preview.classList.add('embed-responsive', 'embed-responsive-16by9');
        }
        // The Youtube Frame API will call this function when the video player is ready.
        function onPreviewReady(event) {
            event.target.playVideo();
        }

        function onPreviewChange(event) {
            //When youtube video ends
            if (event.data == YT.PlayerState.ENDED) {
                const iframeId = event.target.getIframe().id;
                const matches = iframeId.match(/\d+/);
                const videoArrayid = matches ? parseInt(matches[0]) : null;
                //If there is more videos after in the array videos
                if (videoArrayid < videos.length - 1) {
                    //Start the preview of the next video
                    previewVideo(videos[videoArrayid + 1])
                }
            }
        }




        function setVideoContainer() {
            const containers = document.querySelectorAll('.videos-wrapper .video-container');
            containers.forEach(container => {
                //Add the video element to the videos array
                const videoElement = container.querySelector('iframe, video');
                //Set the id of the video with the format video_<index in the videos array>
                videoElement.setAttribute('id', 'video_' + (videos.length));
                videos.push(videoElement);

                //Set the video width depending on it's length
                const duration = parseInt(container.dataset.duration);
                const percentage = (duration / totalDuration) * 100;
                container.style.width = `${percentage}%`;
                //Add to the time of the story the current video time
                time += duration;
                const durationElement = container.querySelector('.duration');
                //Format the time of the video
                durationElement.textContent = timeFormat(time);

                container.addEventListener('click', () => {
                    const iframeOrVideo = container.querySelector('iframe, video');
                    previewVideo(iframeOrVideo)
                });


            });
        }
    </script>
</body>

</html>