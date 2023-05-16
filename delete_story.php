<?php
require_once "functions/useful.php";
require "config/connectdb.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$id = $_GET['id'];
//Retrive the story author
$sql_story = $pdo->prepare('SELECT author FROM story WHERE story.id = ?');
$sql_story->execute([$id]);
$story = $sql_story->fetch(PDO::FETCH_ASSOC);
//If story does not belong to the current user send the user to the my_stories page
if ($story["author"] != $_SESSION['user']) {
    alert("ERROR: Something went wrong");
}

$sql = "DELETE FROM story WHERE id=?;";
$stmt = $pdo->prepare($sql);
if (!($stmt->execute([$id]))) {
    alert("ERROR occurred while deleting the story");
} else {
    //Delete the directory that contains the media files of the story
    if (delete_directory("./files/story_$id/")) {
        header("location: my_stories.php");
    }
}

//Delete a directory
function delete_directory($dir)
{
    //Verify if directory exists
    if (is_dir($dir)) {
        //Obtain the files and directory of the current directory
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            //If other directories exists, use the function recursively to delete them
            //Else if it's a file delete them
            (is_dir("$dir/$file")) ? delete_directory("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    } else {
        //If the directory does not exist return true since there is no files that need deleting
        return true;
    }
}
