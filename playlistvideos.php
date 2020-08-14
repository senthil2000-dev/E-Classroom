<?php
require_once("includes/header.php");
require_once("includes/classes/Video.php");
require_once("includes/classes/VideoSegment.php");
if(!isset($_GET["id"])) {
  echo "No file sent to page"; 
  exit();
}

$id=$_GET["id"];
$videos=array();
$query=$con->prepare("SELECT * FROM playlists WHERE id=:id");
$query->bindParam(":id", $id);
$query->execute();
$row=$query->fetch(PDO::FETCH_ASSOC);
$name=$row["playlistName"].$id;
$query=$con->prepare("SELECT VIDEOID FROM $name");
$query->execute();
while($row=$query->fetch(PDO::FETCH_ASSOC)) {
    $videos[]=new Video($con, $row["VIDEOID"], $usernameLoggedIn);
}
$videoMatrix= new VideoMatrix($con, $userLoggedInObj);
?>

<div class="largeVideoMatrixContainer">
    <?php
        echo $videoMatrix->createLarge2($videos, sizeof($videos)." videos", false, $id);
    ?>
</div>