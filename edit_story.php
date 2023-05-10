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
    } else if (isset($_POST['submit'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $qry = $pdo->prepare('UPDATE story SET name = ?, description = ? WHERE id = ?');
        $result = $qry->execute([$_POST['name'],  $_POST['description'], $id]);
        if ($qry->rowCount() > 0) {
            message_redirect("Story was successfully updated", "edit_story.php?id=$id");
        } else {
            alert("Something went wrong while updating the story, please try again");
        }
    } else if (isset($_POST['orderdownVideo'])) {
        //Turn the order of the video selected down
        $video_id = $_POST['orderdownVideo'];
        $video_change = $pdo->prepare('SELECT id, storyOrder FROM video WHERE id = ?');
        $video_change->execute([$video_id]);
        $videoReorder = $video_change->fetch(PDO::FETCH_ASSOC);
        $current_order = $videoReorder['storyOrder'];
        $new_order = $current_order - 1;
        if ($new_order == 0) {
            message_redirect("Something went wrong  the value is zero", "edit_story.php?id=$id");
        }
        if (!swapValues($pdo, $id, $current_order, $new_order)) {
            message_redirect("Something went wrong when changing the order", "edit_story.php?id=$id");
        }
    } else if (isset($_POST['orderupVideo'])) {
        //Turn the order of the video selected up
        $video_id = $_POST['orderupVideo'];
        $video_change = $pdo->prepare('SELECT id, storyOrder FROM video WHERE id = ?');
        $video_change->execute([$_POST['orderupVideo']]);
        $videoReorder = $video_change->fetch(PDO::FETCH_ASSOC);
        $current_order = $videoReorder['storyOrder'];
        $new_order = $current_order + 1;
        if (!swapValues($pdo, $id, $current_order, $new_order)) {
            message_redirect("Something went wrong when changing the order", "edit_story.php?id=$id");
        }
    } else if (isset($_POST['deleteVideo'])) {
        //Delete the video selected
        $video_id = $_POST['deleteVideo'];
        $stmt = $pdo->prepare('SELECT id, storyId, storyOrder FROM video WHERE id = ?');
        $stmt->execute([$video_id]);
        $video_change =  $stmt->fetch(PDO::FETCH_ASSOC);

        $sql = "DELETE FROM video WHERE id = ?";
        //Update the storyOrder of the videos after the one deleted to reflet the change
        $sql2 = "UPDATE video SET storyOrder = storyOrder - 1 WHERE storyOrder > ? and storyId = ?";

        // start a transaction
        $pdo->beginTransaction();

        $stmt = $pdo->prepare($sql);
        $stmt2 = $pdo->prepare($sql2);

        if ($stmt->execute([$video_id]) && $stmt2->execute([$video_change['storyOrder'], $video_change['storyId']])) {
            // commit the transaction
            if (!$pdo->commit()) {
                message_redirect("Something went wrong when deleting the video", "edit_story.php?id=$id");
            }
        } else {
            //If a error occurs rollBack
            $pdo->rollBack();
            message_redirect("Something went wrong when deleting the video", "edit_story.php?id=$id");
        }
    }
}

//Swap the values of the storyOrder
function swapValues($pdo, $storyID, $value1, $value2)
{

    $new_order1 = -$value2;
    $new_order2 = -$value1;

    // Update the storyOrder to it's new values, but in negative, to avoid a unique key constraint violation.
    $sql1 = "UPDATE video SET storyOrder = 
            CASE
                WHEN storyOrder = :order1 THEN :new_order1
                WHEN storyOrder = :order2 THEN :new_order2
            END 
        WHERE storyOrder IN (" . $value1 . ", " . $value2 . ") AND storyID = :id";

    //Set the values changed to positive
    $sql2 = "UPDATE video SET storyOrder = -storyOrder 
    WHERE storyOrder IN (:new_order1, :new_order2) AND storyID = :id";

    // start a transaction
    $pdo->beginTransaction();

    // prepare and execute the first statement, that commits the new values in negative
    $stmt1 = $pdo->prepare($sql1);
    $stmt1->bindParam(':new_order1', $new_order1);
    $stmt1->bindParam(':new_order2', $new_order2);
    $stmt1->bindParam(':id', $storyID);
    $stmt1->bindParam(':order1', $value1);
    $stmt1->bindParam(':order2', $value2);

    $stmt1->execute();

    // prepare and execute the second statement to return the values to positive
    $stmt2 = $pdo->prepare($sql2);
    $stmt2->execute([
        ':new_order1' => $new_order1,
        ':new_order2' => $new_order2,
        ':id' => $storyID
    ]);
    // commit the transaction
    if ($pdo->commit()) {
        return true;
    }
    return false;
}

//Get the story
$sql_story = $pdo->prepare('SELECT name, description, author FROM story WHERE story.id = ?');
$sql_story->execute([$_GET['id']]);
$story = $sql_story->fetch(PDO::FETCH_ASSOC);
//If story does not belong to the current user send the user to the selectedStoryPage
if ($story["author"] != $_SESSION['user']) {
    message_redirect("ERROR: Something went wrong", "my_stories.php");
}


$name = $story['name'];
$description = $story['description'];

// Fetch the story videos
$sql_videos = $pdo->prepare('SELECT id, link, storyId, videoType, storyOrder,duration FROM video WHERE storyId = ? ORDER BY storyOrder;');
$sql_videos->execute([$_GET['id']]);
$videos = $sql_videos->fetchAll(PDO::FETCH_ASSOC);

// Fetch the story videos
$sql_audios = $pdo->prepare('SELECT id, id_story, audio, storyOrder,duration FROM audio WHERE id_story = ? ORDER BY storyOrder;');
$sql_audios->execute([$_GET['id']]);
$audios = $sql_audios->fetchAll(PDO::FETCH_ASSOC);

// Calculate total duration of all videos
$total_duration = 0;
foreach ($videos as $video) {
    $total_duration += $video['duration'];
}
$audios_duration = 0;
foreach ($audios as $audio) {
    $audios_duration += $audio['duration'];
}

//Use the larger duration as the total duration
if ($audios_duration > $total_duration) {
    $total_duration = $audios_duration;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://www.youtube.com/iframe_api"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Edit story <?= $name ?></title>
    <link rel="stylesheet" href="./style/edit_story.css">
</head>

<body>

    <div class="modal fade" id="addVideo" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <?php include "addVideoToStory.php"; ?>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addAudio" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <?php include "addAudioToStory.php"; ?>

                </div>
            </div>
        </div>
    </div>

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
                        <input class="form-control" id="description" name="description" type="text" value="<?= $description ?>">
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
        <div class="card mt-3">
            <div class="card-header text-center">Edit Videos</div>

            <div class="preview-box">
                <div class="w-100 mb-3">
                    <div id="preview"></div>
                </div>
            </div>

            <div class="card-body">
                <div class="form-group mb-3">

                    <div class="form-group mb-3">

                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addVideo">Add Video</button>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addAudio">Add Audio</button>
                    </div>
                    <form method="POST" action="edit_story.php?id=<?= $id; ?>">
                        <div class="video-scroller">
                            <div class="medias-wrapper">
                                <?php
                                $time = 0;
                                $numItems = count($videos);
                                $i = 0;
                                foreach ($videos as $video) {

                                    echo "<div class='video-container' data-duration='" . $video["duration"] . "'>";
                                    echo '<div class="container videoButtons p-0">
                                            <div class="row p-0 m-0">';
                                    if ($i != 0)
                                        echo    '<div class="col-sm p-0">
                                                        <button type="submit" name="orderdownVideo" value="' . $video["id"] . '" class="w-100 btn-primary"><</button>
                                                    </div>';

                                    echo    '<div class="col-sm  p-0">
                                                    <button type="submit" onclick="confirmDelete()" id="deleteVideo" name="deleteVideo" value="' . $video["id"] . '" class="w-100  btn-danger">X</button>
                                                </div>';
                                    if ($i < $numItems - 1)
                                        echo    '<div class="col-sm  p-0">
                                                    <button type="submit" name="orderupVideo" value="' . $video["id"] . '" class="w-100 btn-primary">></button> 
                                                </div>';
                                    echo    '</div>
                                        </div>';
                                    if ($video["videoType"] == "file") {
                                        echo '<video controls src="./files/story_' . $video["storyId"] . '/video/' . $video["link"] . '"></video>';
                                    } elseif ($video["videoType"] == "text") {
                                        echo '<div class="player" data-video-id="' . $video["link"] . '"></div>';
                                    }
                                    echo '<span class="duration"></span>';
                                    echo "</div>";
                                    $i++;
                                } ?>
                            </div>
                            <div class="mt-1 duration-line"></div>

                            <div class="medias-wrapper mt-3">
                                <?php
                                $time = 0;
                                $numItems = count($audios);
                                $i = 0;
                                foreach ($audios as $audio) {
                                    echo "<div class='audio-container mt-2' data-duration='" . $audio["duration"] . "'>";
                                    echo '<audio class="w-100" controls src="./files/story_' . $audio["id_story"] . '/audio/' . $audio["audio"] . '"></audio>';
                                    echo '<span class="duration"></span>';
                                    echo "</div>";
                                    $i++;
                                } ?>
                            </div>
                            <div class="mt-1 duration-line"></div>

                        </div>
                </div>
                </form>

            </div>
        </div>
    </div>


    </div>

    <?php
    include "footer.php";
    ?>

    <script>
        function confirmDelete() {
            const confirmed = confirm('Are you sure you want to delete this?');
            if (!confirmed) {
                event.preventDefault(); // prevent the form from submitting if the user doesn't confirm
            }
        }
        var videos = [];
        var audios = [];
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
        //API is loaded 
        if (typeof YT !== 'undefined' && YT.loaded) {
            setYTPlayers()
            setVideoContainer()

        }
        //After the Youtube FrameAPI is ready
        function onYouTubeIframeAPIReady() {
            setYTPlayers()
            //Set some of the video-container div's parameters dynamically
            setVideoContainer()
        }

        function setYTPlayers() {

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

        function previewAudio(nextAudio) {
            const clonedAudio = nextAudio.cloneNode(true);
            if (clonedAudio.tagName === 'AUDIO') {
                // Handle HTML5 video
                clonedAudio.addEventListener("ended", function() {
                    previewAudio(audios[audios.length - 1]);
                });
                clonedAudio.setAttribute('autoplay', 'true'); // set autoplay attribute to start playing the audio
                clonedAudio.setAttribute('controls', 'true'); // set controls attribute to display the audio controls
                clonedAudio.play();
            }
            const preview = document.querySelector('#preview');
            //Add the Video to the preview div
            preview.innerHTML = '';
            preview.appendChild(clonedAudio);
            preview.className = 'w-100 mt-3';
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
            time = 0;
            const containers = document.querySelectorAll('.medias-wrapper .video-container');
            containers.forEach(container => {
                //Add the video element to the videos array
                const videoElement = container.querySelector('iframe, video');
                //Set the id of the video with the format video_<index in the videos array>
                videoElement.setAttribute('id', 'video_' + (videos.length));
                videos.push(videoElement);

                if (totalDuration > 3600) {
                    const wrapper = document.querySelector('.medias-wrapper');
                    const percentageWrapper = (~~(totalDuration / 3600) * 10) + 100;
                    const line = document.querySelector('.duration-line');
                    line.style.width = `${percentageWrapper}%`;

                    wrapper.style.width = `${percentageWrapper}%`;
                }
                //Set the video width depending on it's length
                const duration = parseInt(container.dataset.duration);
                const percentage = (duration / totalDuration) * 100;
                container.style.width = `${percentage}%`;

                //Add to the time of the story the current video time
                time += duration;
                const durationElement = container.querySelector('.duration');
                //Format the time of the video
                durationElement.textContent = timeFormat(time);

                container.addEventListener('click', (e) => {
                    //Don't trigger if button is pressed
                    if ($(e.target).is("button")) {
                        return;
                    } else {
                        const iframeOrVideo = container.querySelector('iframe, video');
                        previewVideo(iframeOrVideo)
                    }

                });


            });

            setAudioContainer();
        }

        function setAudioContainer() {
            time = 0;
            console.log("SET AUDIO")
            const containers = document.querySelectorAll('.medias-wrapper .audio-container');
            containers.forEach(container => {
                console.log("CONTAINERS")
                //Add the video element to the videos array
                const audioElement = container.querySelector('audio');
                //Set the id of the video with the format video_<index in the videos array>
                audioElement.setAttribute('id', 'audio_' + (audios.length));
                audios.push(audioElement);

                //Set the video width depending on it's length
                const duration = parseInt(container.dataset.duration);
                const percentage = (duration / totalDuration) * 100;
                container.style.width = `${percentage}%`;

                //Add to the time of the story the current video time
                time += duration;
                const durationElement = container.querySelector('.duration');
                console.log(durationElement)
                //Format the time of the video
                durationElement.textContent = timeFormat(time);

                container.addEventListener('click', (e) => {
                    //Don't trigger if button is pressed
                    if ($(e.target).is("button")) {
                        return;
                    } else {
                        const audio = container.querySelector('audio');
                        previewAudio(audio);
                    }

                });


            });
        }
    </script>
</body>

</html>