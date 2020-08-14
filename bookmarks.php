<?php
require_once("includes/header.php");
require_once("includes/classes/BookmarksProvider.php");

if(!User::isLoggedIn()) {
    header("Location:signIn.php");
}

$bookmarksProvider = new BookmarksProvider($con, $userLoggedInObj);
$videos=$bookmarksProvider->getVideos();

$videoMatrix = new VideoMatrix($con, $userLoggedInObj);
?>
<div class="largeVideoMatrixContainer">
<?php 
if(sizeof($videos)>0) {
    echo $videoMatrix->createLarge($videos, "Bookmarked videos", false);
}
else {
    echo "No videos to show";
}
?>
</div>