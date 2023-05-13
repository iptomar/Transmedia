<?php
require_once "./functions/useful.php";
require_once "config/connectdb.php";
if (isset($_POST['orderdownVideo'])) {
    // Get the ID of the selected video media file to be moved down in order
    $video_id = $_POST['orderdownVideo'];
    // Retrieve information about the selected video media file
    $video_change = $pdo->prepare('SELECT id, storyOrder FROM video WHERE id = ?');
    $video_change->execute([$video_id]);
    $videoReorder = $video_change->fetch(PDO::FETCH_ASSOC);
    // Determine the current and new story order values for the selected video media file
    $current_order = $videoReorder['storyOrder'];
    $new_order = $current_order - 1;
    //Check if the new order is valid
    if ($new_order == 0) {
        //Redirect to the story edit page with an error message if the new order is zero
        message_redirect("Something went wrong  the value is zero", "edit_story.php?id=$id");
    }
    // Swap the story order values of the selected video media file and the file below it
    if (!swapValues($pdo, $id, $current_order, $new_order, "video", "storyOrder", "storyId")) {
        //If the swap operation fails, display an error message and redirect the user to the "edit_story" page with the appropriate ID parameter
        message_redirect("Something went wrong when changing the order", "edit_story.php?id=$id");
    }
} else if (isset($_POST['orderupVideo'])) {
    //Retrieve the ID of the video to be reordered from the form data
    $video_id = $_POST['orderupVideo'];
    // Retrieve information about the selected video media file
    $video_change = $pdo->prepare('SELECT id, storyOrder FROM video WHERE id = ?');
    $video_change->execute([$_POST['orderupVideo']]);
    $videoReorder = $video_change->fetch(PDO::FETCH_ASSOC);
    $current_order = $videoReorder['storyOrder'];
    //Calculate the new order value by adding 1 to the current order
    $new_order = $current_order + 1;
    // Swap the story order values of the selected video media file and the file above it
    if (!swapValues($pdo, $id, $current_order, $new_order, "video", "storyOrder", "storyId")) {
        //If the swap operation fails, display an error message and redirect the user to the "edit_story" page with the appropriate ID parameter
        message_redirect("Something went wrong when changing the order", "edit_story.php?id=$id");
    }
} else if (isset($_POST['deleteVideo'])) {
    // Delete the video selected
    $video_id = $_POST['deleteVideo'];
    // Get the video's information
    $stmt = $pdo->prepare('SELECT id, link, storyId, storyOrder, videoType FROM video WHERE id = ?');
    $stmt->execute([$video_id]);
    $video_change =  $stmt->fetch(PDO::FETCH_ASSOC);

    // Define SQL statements to delete the selected video file and update the storyOrder of subsequent files
    $sql = "DELETE FROM video WHERE id = ?";
    $sql2 = "UPDATE video SET storyOrder = storyOrder - 1 WHERE storyOrder > ? and storyId = ?";

    // Delete the video file from the server (if it is a 'file' type video)
    $alter_video_file = $video_change['link'];
    $storyId = $video_change['storyId'];
    if ($video_change['videoType'] == 'file') {
        delete_file("./files/story_$storyId/video/$alter_video_file");
    }

    // start a transaction
    $pdo->beginTransaction();
    //Run the command to delete the video from the DB
    $stmt = $pdo->prepare($sql);
    //Run the command to update the videos that had higher storyOrder values
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
