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
        // If the user clicked cancel, redirect them to the story page and exit
        header("location: selectedStoryPage.php?id=$id");
        exit();
    } else if (isset($_POST['submit'])) {
        // If the user clicked submit, update the story with the new name and description
        $name = $_POST['name'];
        $description = $_POST['description'];
        $qry = $pdo->prepare('UPDATE story SET name = ?, description = ? WHERE id = ?');
        $result = $qry->execute([$_POST['name'],  $_POST['description'], $id]);
        if ($qry->rowCount() > 0) {
            // If the update was successful, redirect to the edit page with a success message
            message_redirect("Story was successfully updated", "edit_story.php?id=$id");
        } else {
            // If the update failed, display an error message
            alert("Something went wrong while updating the story, please try again");
        }
    } else if (isset($_POST['orderdownVideo']) || isset($_POST['orderupVideo']) || isset($_POST['deleteVideo'])) {
        //If the user clicked a button that edits, or deletes, a video insert the edit_video file
        require("edit_video.php");
    } else if (isset($_POST['orderdownAudio']) || isset($_POST['orderupAudio']) || isset($_POST['deleteAudio'])) {
        //If the user clicked a button that edits, or deletes, a audio insert the edit_audio file
        require("edit_audio.php");
    } else if (isset($_POST['orderdownImage']) || isset($_POST['orderupImage']) || isset($_POST['deleteImage'])) {
        //If the user clicked a button that edits, or deletes, a image insert the edit_image file
        require("edit_image.php");
    }
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

// Fetch the story audio
$sql_audios = $pdo->prepare('SELECT id, id_story, audio, storyOrder,duration FROM audio WHERE id_story = ? ORDER BY storyOrder;');
$sql_audios->execute([$_GET['id']]);
$audios = $sql_audios->fetchAll(PDO::FETCH_ASSOC);

// Fetch the story image
$sql_image = $pdo->prepare('SELECT id, image, storyId, storyOrder,duration FROM image WHERE storyId = ? ORDER BY storyOrder;');
$sql_image->execute([$_GET['id']]);
$images = $sql_image->fetchAll(PDO::FETCH_ASSOC);

// Calculate total duration of all videos
$total_duration = 0;
foreach ($videos as $video) {
    $total_duration += $video['duration'];
}
$audios_duration = 0;
foreach ($audios as $audio) {
    $audios_duration += $audio['duration'];
}
$images_duration = 0;
foreach ($images as $image) {
    $images_duration += $image['duration'];
}

//Use the larger duration as the total duration
if ($audios_duration > $total_duration) {
    $total_duration = $audios_duration;
} else if ($images_duration > $total_duration) {
    $total_duration = $images_duration;
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

    <div class="modal fade" id="addImage" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <?php include "addImageToStory.php"; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="changeImgDuration" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <?php include "edit_image_duration.php"; ?>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addText" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <?php include "addTextToStory.php"; ?>

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
            <div class="card-header text-center">
                Edit Media
                <p class="m-0 p-0" style="font-size: 14px;">Changes made to this form are permanent</p>
            </div>

            <div class="preview-box w-0">
                <div class="w-100 mb-3 text-center">
                    <div class="p-0 mb-3 w-100 preview-title" style="font-size: 14px;">
                        <p class="m-0 p-0">Preview Media</p>
                        <p class="m-0 p-0" style="font-size: 10px;">Click on any media to preview it</p>
                    </div>
                    <div id="preview"></div>
                </div>
            </div>

            <div class="card-body">
                <div class="form-group mb-3">

                    <div class="form-group mb-3">

                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addVideo">Add Video</button>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addAudio">Add Audio</button>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addImage">Add Image</button>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addText">Add Text</button>
                    </div>
                    <form method="POST" action="edit_story.php?id=<?= $id; ?>">
                        <div class="video-scroller">

                            <div class="medias-wrapper">
                                <p class="d-flex align-items-center justify-content-center rotated" style="color:white; margin: auto 1px;">Videos</p>
                                <?php
                                $time = 0;
                                $numItems = count($videos);
                                $i = 0;
                                foreach ($videos as $video) {

                                    echo "<div class='media-container video-container' data-duration='" . $video["duration"] . "'>";
                                    echo '<div class="media-buttons p-0">
                                            <div class="row p-0 m-0">';
                                    if ($i != 0)
                                        echo    '<div class="col p-0">
                                                        <button type="submit" name="orderdownVideo" value="' . $video["id"] . '" class="w-100 btn-primary"><</button>
                                                    </div>';

                                    echo    '<div class="col  p-0">
                                                    <button type="submit" onclick="confirmDelete()" id="deleteVideo" name="deleteVideo" value="' . $video["id"] . '" class="w-100  btn-danger">X</button>
                                                </div>';
                                    if ($i < $numItems - 1)
                                        echo    '<div class="col  p-0">
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
                                <p class="d-flex align-items-center justify-content-center rotated" style="color:white; margin: auto 1px;">Audios</p>
                                <?php
                                $time = 0;
                                $numItems = count($audios);
                                $i = 0;
                                foreach ($audios as $audio) {
                                    echo "<div class='media-container audio-container' data-duration='" . $audio["duration"] . "'>";
                                    echo '<div class="media-buttons p-0">
                                            <div class="row p-0 m-0">';
                                    if ($i != 0)
                                        echo    '<div class="col p-0">
                                                    <button type="submit" name="orderdownAudio" value="' . $audio["id"] . '" class="w-100 btn-primary"><</button>
                                                </div>';
                                    echo        '<div class="col  p-0">
                                                    <button type="submit" onclick="confirmDelete()" id="deleteAudio" name="deleteAudio" value="' . $audio["id"] . '" class="w-100  btn-danger">X</button>
                                                </div>';
                                    if ($i < $numItems - 1)
                                        echo    '<div class="col  p-0">
                                                    <button type="submit" name="orderupAudio" value="' . $audio["id"] . '" class="w-100 btn-primary">></button> 
                                                </div>';
                                    echo    '</div>
                                        </div>';
                                    echo '<audio class="w-100" controls src="./files/story_' . $audio["id_story"] . '/audio/' . $audio["audio"] . '"></audio>';
                                    echo '<span class="duration"></span>';
                                    echo "</div>";
                                    $i++;
                                } ?>
                            </div>
                            <div class="mt-1 duration-line"></div>

                            <div class="medias-wrapper mt-3">
                                <p class="d-flex align-items-center justify-content-center rotated" style="color:white; margin: auto 1px;">Images</p>
                                <?php
                                $time = 0;
                                $numItems = count($images);
                                $i = 0;
                                foreach ($images as $image) {
                                    echo "<div class='media-container image-container' data-duration='" . $image["duration"] . "'>";
                                    echo '<div class="media-buttons p-0">
                                            <div class="row p-0 m-0">';
                                    if ($i != 0)
                                        echo    '<div class="col p-0">
                                                    <button type="submit" name="orderdownImage" value="' . $image["id"] . '" class="w-100 btn-primary"><</button>
                                                </div>';
                                    echo        '<div class="col  p-0">
                                                    <button type="submit" onclick="confirmDelete()" id="deleteImage" name="deleteImage" value="' . $image["id"] . '" class="w-100  btn-danger">X</button>
                                                </div>';
                                    if ($i < $numItems - 1)
                                        echo    '<div class="col  p-0">
                                                    <button type="submit" name="orderupImage" value="' . $image["id"] . '" class="w-100 btn-primary">></button> 
                                                </div>';
                                    echo    '</div>
                                        </div>';
                                    echo '<div class="img-div" ><img style="height:100px; width: auto; max-width:100%" src="./files/story_' . $image["storyId"] . '/image/' . $image["image"] . '"></img></div>';
                                    echo '<button  type="button" class="btn-primary w-100" data-toggle="modal" data-target="#changeImgDuration" data-duration="'. $image["duration"].'"  data-image="'. $image["id"].'" class="w-100  btn-primary">🕑</button>';
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
        $('#changeImgDuration').on('show.bs.modal', function(event) {
            console.log("MODAL OPENED")
            var button = $(event.relatedTarget) 
            var duration = button.data('duration') 
            var id = button.data('image') 
            var modal = $(this)
            modal.find('.modal-body #duration').val(duration)
            modal.find('.modal-body #imageID').val(id)
        })
        // Function prompts the user to confirm the delete before submitting the form
        function confirmDelete() {
            const confirmed = confirm('Are you sure you want to delete this?');
            if (!confirmed) {
                event.preventDefault(); // prevent the form from submitting if the user doesn't confirm
            }
        }
        // An array to store video file data
        var videos = [];

        // An array to store audio file data
        var audios = [];

        // The total duration of audio or video files, the sum of duration that is larger
        const totalDuration = <?= $total_duration ?>;


        var time = 0;

        // Function that formats the duration of the audio and video files into hours, minutes, and seconds
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

        // Receives a video element and displays it the preview element
        function previewVideo(nextVideo) {
            // Clone the video element
            const clonedVideo = nextVideo.cloneNode(true);
            // Check if the cloned video is an iframe
            if (clonedVideo.tagName === 'IFRAME') {
                // Remove class attribute
                clonedVideo.removeAttribute('class');
                // Get video ID from data attribute
                const videoId = clonedVideo.dataset.videoId;
                // Create a new YouTube player object with the video ID and settings
                const playerObject = new YT.Player(clonedVideo, {
                    height: '390',
                    width: '640',
                    videoId: videoId,
                    playerVars: {
                        'autoplay': 1,
                        'controls': 1
                    },
                    events: {
                        'onReady': onPreviewVideoReady,
                        'onStateChange': onPreviewVideoChange
                    }
                });
            }
            // Check if the cloned video is a HTML5 video element 
            else if (clonedVideo.tagName === 'VIDEO') {
                // Handle HTML5 video
                clonedVideo.addEventListener("ended", function() {
                    // Play the last video in the array when current video ends
                    previewVideo(videos[videos.length - 1]);
                });
                clonedVideo.setAttribute('autoplay', 'true'); // add autoplay attribute to start playing the video
                clonedVideo.setAttribute('controls', 'true'); // add controls attribute to display the video controls
                clonedVideo.play();
            }
            const preview = document.querySelector('#preview');
            // Get the preview element and replace its contents with the cloned video
            preview.innerHTML = '';
            preview.appendChild(clonedVideo);
            // Add CSS classes to make the preview element responsive
            preview.classList.add('embed-responsive', 'embed-responsive-16by9');
        }

        // Preview an audio element
        function previewAudio(nextAudio) {
            // Clone the audio element
            const clonedAudio = nextAudio.cloneNode(true);
            // Check if the cloned element is an audio element
            if (clonedAudio.tagName === 'AUDIO') {
                // Handle when the audio ends by replaying the last audio
                clonedAudio.addEventListener("ended", function() {
                    previewAudio(audios[audios.length - 1]);
                });
                // Set autoplay attribute to start playing the audio
                clonedAudio.setAttribute('autoplay', 'true');
                // Set controls attribute to display the audio controls
                clonedAudio.setAttribute('controls', 'true');
                // Start playing the audio
                clonedAudio.play();
            }
            // Get the preview element
            const preview = document.querySelector('#preview');
            // Clear any existing content in the preview element
            preview.innerHTML = '';
            // Add the cloned audio element to the preview element
            preview.appendChild(clonedAudio);
            // Add CSS classes to the preview element
            preview.className = 'w-100 mt-3';
        }

        // Preview an image element
        function previewImage(image) {
            // Clone the image element
            const clonedImage = image.getAttribute("src");
            // Get the preview element
            const preview = document.querySelector('#preview');
            // Clear any existing content in the preview element
            preview.innerHTML = '';
            // Add the cloned audio element to the preview element
            preview.innerHTML = '<img src="' + clonedImage + '"></img>';
            // Add CSS classes to the preview element
            preview.className = 'w-100 mt-3';
        }

        // The Youtube Frame API will call this function when the video player is ready.
        function onPreviewVideoReady(event) {
            // Play the video when it is ready
            event.target.playVideo();
        }

        function onPreviewVideoChange(event) {
            // When the YouTube video ends
            if (event.data == YT.PlayerState.ENDED) {
                // Get the ID of the iframe
                const iframeId = event.target.getIframe().id;
                // Extract the video ID from the iframe ID using a regular expression
                const matches = iframeId.match(/\d+/);
                const videoArrayid = matches ? parseInt(matches[0]) : null;
                // If there are more videos after the current one in the array
                if (videoArrayid < videos.length - 1) {
                    // Start the preview of the next video
                    previewVideo(videos[videoArrayid + 1])
                }
            }
        }




        // Function to set the video container
        function setVideoContainer() {
            // Initialize time to zero
            time = 0;
            // If the total duration is greater than 3600 seconds, adjust the wrapper and the line size
            if (totalDuration > 3600) {
                adjustWrapperLineSize(0);
            }

            // Get all the video containers in the story 
            const containers = document.querySelectorAll('.medias-wrapper .video-container');
            containers.forEach(container => {
                const videoElement = container.querySelector('iframe, video');
                videoElement.setAttribute('id', 'video_' + (videos.length));
                //add videoElement to the videos array
                videos.push(videoElement);

                // Get the duration of the video 
                var duration = parseInt(container.dataset.duration);
                adjustContainer(container, duration);

                // Add the duration of the current video to the video time passed and format the time element
                time += duration;
                const durationElement = container.querySelector('.duration');
                //Format the time of the video
                durationElement.textContent = timeFormat(time);

                // Add a click listener to the container that will preview the video when clicked
                container.addEventListener('click', (e) => {
                    //Don't trigger if button is pressed
                    if ($(e.target).is("button")) {
                        // If a button was clicked, don't preview the video
                        return;
                    } else {
                        // If not, get the iframe or video element and preview the video
                        const iframeOrVideo = container.querySelector('iframe, video');
                        previewVideo(iframeOrVideo)
                    }
                });
            });

            // Call the setAudioContainer function
            setAudioContainer();
        }

        // This function sets up the audio containers in the story
        function setAudioContainer() {
            time = 0;
            // Get all the audio containers in the story
            const containers = document.querySelectorAll('.medias-wrapper .audio-container');
            // Loop through each container
            containers.forEach(container => {
                const audioElement = container.querySelector('audio');
                // Set the id of the audio with the format audio_<index in the audios array>
                audioElement.setAttribute('id', 'audio_' + (audios.length));
                // Add the audio element to the audios array
                audios.push(audioElement);

                // Get the duration of the audio 
                var duration = parseInt(container.dataset.duration);
                adjustContainer(container, duration)

                // Add to the time of the story the current audio time
                time += duration;
                const durationElement = container.querySelector('.duration');
                // Format the time of the audio
                durationElement.textContent = timeFormat(time);

                // Add click event listener to the container
                container.addEventListener('click', (e) => {
                    // Don't trigger if button is pressed
                    if ($(e.target).is("button")) {
                        return;
                    } else {
                        const audio = container.querySelector('audio');
                        // Preview the audio when container is clicked
                        previewAudio(audio);
                    }

                });
            });
            setImageContainer();
        }

        function setImageContainer() {
            time = 0;
            // Get all the audio containers in the story
            const containers = document.querySelectorAll('.medias-wrapper .image-container');
            // Loop through each container
            containers.forEach(container => {
                // Get the duration of the video 
                var duration = parseInt(container.dataset.duration);

                adjustContainer(container, duration);

                // Add the duration of the current video to the video time passed and format the time element
                time += duration;
                const durationElement = container.querySelector('.duration');
                //Format the time of the video
                durationElement.textContent = timeFormat(time);

                // Add click event listener to the container
                container.addEventListener('click', (e) => {
                    // Don't trigger if button is pressed
                    if ($(e.target).is("button")) {
                        return;
                    } else {
                        const image = container.querySelector('img');
                        // Preview the audio when container is clicked
                        previewImage(image);
                    }

                });
            });
        }

        // This function adjusts the size of the media wrapper and duration lines 
        //The perct parameter represents the additional percentage to be added to the width of the wrappers and lines.
        function adjustWrapperLineSize(perct) {
            //Get all media wrappers
            const wrappers = document.querySelectorAll('.medias-wrapper');
            //Calculate the base percentage of the wrapper size, with a minimum of 100%
            const percentageWrapper = (~~(totalDuration / 3600) * 10) + 100 + perct;

            //Set the width of each wrapper to the calculated percentage
            wrappers.forEach(wrapper => {
                wrapper.style.width = `${percentageWrapper}%`;
            });

            //Get all duration lines
            const lines = document.querySelectorAll('.duration-line');
            //Set the width of each line to the calculated percentage
            lines.forEach(line => {
                line.style.width = `${percentageWrapper}%`;
            });

        }

        function adjustContainer(containerToAdjust, durationContainer) {
            // Initialize the smallAdjust variable to 100
            var smallAdjust = 100;
            do {
                // Set the audio width depending on its length
                var percentage = (durationContainer / totalDuration) * 100;
                containerToAdjust.style.width = `${percentage}%`;
                // If the container width is less than 100px, adjust the line size
                if (containerToAdjust.clientWidth < 100) {
                    adjustWrapperLineSize(smallAdjust);
                    smallAdjust += 10;
                }
            } while (containerToAdjust.clientWidth < 100);
        }
    </script>
</body>

</html>