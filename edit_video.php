<?php
require_once "./functions/useful.php";
require_once "config/connectdb.php";
if (isset($_POST['orderdownVideo'])) {
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
    if (!swapValues($pdo, $id, $current_order, $new_order, "video", "storyOrder", "storyId")) {
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
    if (!swapValues($pdo, $id, $current_order, $new_order, "video", "storyOrder", "storyId")) {
        message_redirect("Something went wrong when changing the order", "edit_story.php?id=$id");
    }
} else if (isset($_POST['deleteVideo'])) {
    //Delete the video selected
    $video_id = $_POST['deleteVideo'];
    $stmt = $pdo->prepare('SELECT id, link, storyId, storyOrder, videoType FROM video WHERE id = ?');
    $stmt->execute([$video_id]);
    $video_change =  $stmt->fetch(PDO::FETCH_ASSOC);
    $sql = "DELETE FROM video WHERE id = ?";
    //Update the storyOrder of the videos after the one deleted to reflet the change
    $sql2 = "UPDATE video SET storyOrder = storyOrder - 1 WHERE storyOrder > ? and storyId = ?";

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
