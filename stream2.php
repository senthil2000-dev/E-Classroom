<?php
require_once("includes/header.php");
require_once("includes/classes/VideoElement2.php");
require_once("includes/classes/VideoControls.php");
require_once("includes/classes/Comment.php");
require_once("includes/classes/CommentsContainer.php");

if(!isset($_GET["id"])) {
    echo "No url passed into page";
    exit();
}

$video=new Video($con, $_GET["id"], $userLoggedInObj);
$video->incrementViews();
$username=$userLoggedInObj->getUsername();
$query=$con->prepare("SELECT statusPaused FROM users WHERE username=:user");
$query->bindParam(":user", $username);
$query->execute();
$status=$query->fetchColumn();
$query=$con->prepare("SELECT * FROM history WHERE user=:user AND videoId=:videoId AND statusPaused=:status");
$query->bindParam(":user", $username);
$query->bindParam(":videoId", $_GET["id"]);
$query->bindParam(":status", $status);
$query->execute();
if($query->rowCount()==0) {
    $query=$con->prepare("INSERT INTO history(user, videoId, statusPaused) VALUES(:user, :videoId, :status)");
    $query->bindParam(":user", $username);
    $query->bindParam(":videoId", $_GET["id"]);
    $query->bindParam(":status", $status);
    $query->execute();
}
?>
<script src="assets/js/videoOperations.js"></script>
<script src="assets/js/commentActions.js"></script>

<div class="streamLeftColumn">

<?php
    $videoElement = new VideoElement2($video);
    echo $videoElement->play(true);

    $videoInfo = new VideoControls($con, $video, $userLoggedInObj);
    echo $videoInfo->create();

    $commentsContainer = new CommentsContainer($con, $video, $userLoggedInObj);
    echo $commentsContainer->create();
    foreach($_SESSION["ids"] as $id1) {
            $videos[]=new Video($con, $id1, $usernameLoggedIn);
    }
    $videoMatrix= new VideoMatrix($con, $userLoggedInObj);
    ?>

</div>

<div class="recommendations" id="playvideos">
    <?php
        echo $videoMatrix->create2($videos, null, false, $_GET["id"]);
    ?>
</div>

<?php require_once("includes/footer.php"); ?>