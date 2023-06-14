<?php
require_once "./functions/useful.php";
require_once "config/connectdb.php";
if (isset($_POST['orderdownText'])) {
    // Get the ID of the selected text to be moved down in order
    $text_id = $_POST['orderdownText'];
    // Retrieve information about the selected text
    $text_change = $pdo->prepare('SELECT id, storyOrder FROM text WHERE id = ?');
    $text_change->execute([$text_id]);
    $textReorder = $text_change->fetch(PDO::FETCH_ASSOC);
    // Determine the current and new story order values for the selected text
    $current_order = $textReorder['storyOrder'];
    $new_order = $current_order - 1;
    //Check if the new order is valid
    if ($new_order == 0) {
        //Redirect to the story edit page with an error message if the new order is zero
        message_redirect("Something went wrong  the value is zero", "edit_story.php?id=$id");
    }
    // Swap the story order values of the selected text media file and the file below it
    if (!swapValues($pdo, $id, $current_order, $new_order, "text", "storyOrder", "id_story")) {
        //If the swap operation fails, display an error message and redirect the user to the "edit_story" page with the appropriate ID parameter
        message_redirect("Something went wrong when changing the order", "edit_story.php?id=$id");
    }
} else if (isset($_POST['orderupText'])) {
    //Retrieve the ID of the text to be reordered from the form data
    $text_id = $_POST['orderupText'];
    // Retrieve information about the selected text media file
    $text_change = $pdo->prepare('SELECT id, storyOrder FROM text WHERE id = ?');
    $text_change->execute([$_POST['orderupText']]);
    $textReorder = $text_change->fetch(PDO::FETCH_ASSOC);
    $current_order = $textReorder['storyOrder'];
    //Calculate the new order value by adding 1 to the current order
    $new_order = $current_order + 1;
    // Swap the story order values of the selected text media file and the file above it
    if (!swapValues($pdo, $id, $current_order, $new_order, "text", "storyOrder", "id_story")) {
        //If the swap operation fails, display an error message and redirect the user to the "edit_story" page with the appropriate ID parameter
        message_redirect("Something went wrong when changing the order", "edit_story.php?id=$id");
    }
} else if (isset($_POST['deleteText'])) {
    // Get the ID of the selected image file to be deleted
    $media_id = $_POST['deleteText'];
    // Retrieve information about the selected image file
    $stmt = $pdo->prepare('SELECT id, text, id_story, storyOrder FROM text WHERE id = ?');
    $stmt->execute([$media_id]);
    $media_alter =  $stmt->fetch(PDO::FETCH_ASSOC);

    // Define SQL statements to delete the selected image file and update the storyOrder of subsequent files
    $sql = "DELETE FROM text WHERE id = ?";
    $sql2 = "UPDATE text SET storyOrder = storyOrder - 1 WHERE storyOrder > ? and id_story = ?";

    // start a transaction
    $pdo->beginTransaction();
    $stmt = $pdo->prepare($sql);
    $stmt2 = $pdo->prepare($sql2);
    if ($stmt->execute([$media_id]) && $stmt2->execute([$media_alter['storyOrder'], $media_alter['id_story']])) {
        // commit the transaction
        if (!$pdo->commit()) {
            message_redirect("Something went wrong when deleting the image", "edit_story.php?id=$id");
        }
    } else {
        //If a error occurs rollBack
        $pdo->rollBack();
        message_redirect("Something went wrong when deleting the image", "edit_story.php?id=$id");
    }
