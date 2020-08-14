<?php
require_once("includes/header.php");
$videos=array();
$status=0;
$query=$con->prepare("SELECT videoId FROM history WHERE statusPaused=:status AND user=:username AND time > (NOW() - INTERVAL :days DAY) ORDER BY id DESC");
$query->bindParam(":status", $status);
$query->bindParam(":username", $username);
$query->bindParam(":days", $days);
if(isset($_GET["rangeValue"])) 
    $days=$_GET["rangeValue"];
else 
    $days=1826;
$username=$userLoggedInObj->getUsername();
$query->execute();

while($row=$query->fetch(PDO::FETCH_ASSOC)) {
    $videos[]=new Video($con, $row["videoId"], $userLoggedInObj);
}

$videoMatrix = new VideoMatrix($con, $userLoggedInObj);
?>
<div class="largeVideoMatrixContainer">
<?php 
if(sizeof($videos)>0) {
    echo $videoMatrix->createLarge($videos, "Videos that you have watched", false, true, $days);
}
else {
    echo $videoMatrix->createLarge($videos, "No videos to show", false, true, $days);;
}
?>
</div>