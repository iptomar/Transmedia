<?php
require_once "config/connectdb.php";
//Start session if it has not yet started
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

<link rel="stylesheet" href="./style/navbar.css" type="text/css">
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="index.php">
    <img src="./assets/logo.svg" width="200" height="50"></img>
  </a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTransmedia" aria-controls="navbarTransmedia" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarTransmedia">
    <ul class="navbar-nav mx-auto mt-2 mt-lg-0 ">
      <li class="nav-item">
        <a class="nav-link" href="storiesCatalogue.php">Stories Catalogue</a>
      </li>
      <?php
      //Hide links that the user does not have permission to access if they are not logged in
      if (isset($_SESSION["user"])) {
        echo '<li class="nav-item">
          <a class="nav-link" href="my_stories.php">My Stories</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="createStory.php">Create Story</a>
        </li>';
      } ?>
    </ul>
    <ul class="navbar-nav mt-2 mt-lg-0" style="max-width: 200px;">
      <?php
      //Verify if user is logged in, if he is instead of login and register button show logout button
      if (!isset($_SESSION["user"])) {
        echo "<a class='nav-link signin' href='login.php'>Login</a>";
        echo "<a class='nav-link register' href='register.php'>Register</a>";
      } else {
        echo '<li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle btn btn-secondary user-dropdow" id="userDropdown" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  ' . $_SESSION["user"] . '
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                  <li><a class="dropdown-item" href="user_edit.php">Edit Profile</a></li>
                  <li><a class="dropdown-item logout" href="logout.php">Logout</a></li>
                </ul>
              </li>';
      }
      ?>
    </ul>
  </div>
</nav>

<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha384-xBuQ/xzmlsLoJpyjoggmTEz8OWUFM0/RC5BsqQBDX2v5cMvDHcMakNTNrHIW2I5f" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>