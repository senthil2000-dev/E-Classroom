<?php
require_once("includes/header.php");

if(!User::isLoggedIn()) {
    header("Location:signIn.php");
}

$subscriptionsGenerator = new SubscriptionsGenerator($con, $userLoggedInObj);
$videos=$subscriptionsGenerator->getVideos();

$videoMatrix = new VideoMatrix($con, $userLoggedInObj);
?>
<div class="largeVideoMatrixContainer">
<?php 
if(sizeof($videos)>0) {
    echo $videoMatrix->createLarge($videos, "Videos from your subscriptions", false);
}
else {
    echo "No videos to show";
}
?>
</div>