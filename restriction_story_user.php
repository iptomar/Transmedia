<?php

session_start();

require "config/connectdb.php";

if(isset($_SESSION["user"])){
    echo "<script>
    alert('You need to login to create a story');
    window.location.href=’createStory.php’
    </script>";
}else {
    header("location: createStory.php");
}

?>
