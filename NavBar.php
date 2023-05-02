<?php
require_once "config/connectdb.php";
//Start session if it has not yet started
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

<link rel="stylesheet" href="./style/navbar.css" type="text/css">

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="index.php">
    <img src="./assets/logo.svg" width="200" height="50"></img>
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
    <ul class="navbar-nav mx-auto mt-2 mt-lg-0">
      <?php
      //Hide links that the user does not have permission to access if they are not logged in
      if (isset($_SESSION["user"])) {
        echo '<li class="nav-item">
        <a class="nav-link" href="createStory.php">Create Story</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="my_stories.php">My Stories</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="addVideoToStory.php">Add Video</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="addAudioToStory.php">Add Audio</a>
      </li>';
      } ?>
    </ul>
    <?php
    //Verify if user is logged in, if he is instead of login and register button show logout button
    if (!isset($_SESSION["user"])) {
      echo "<a class='nav-link signin' href='login.php'>Login</a>";
      echo "<a class='nav-link register' href='register.php'>Register</a>";
    } else {
      echo "<a class='nav-link logout' href='logout.php'>Logout</a>";
    }
    ?>
  </div>
</nav>
<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha384-xBuQ/xzmlsLoJpyjoggmTEz8OWUFM0/RC5BsqQBDX2v5cMvDHcMakNTNrHIW2I5f" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>