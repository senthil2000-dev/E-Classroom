<?php
require_once("includes/header.php");
require_once("includes/classes/VideoElement.php");
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
    $videoElement = new VideoElement($video);
    echo $videoElement->play(true);

    $videoInfo = new VideoControls($con, $video, $userLoggedInObj);
    echo $videoInfo->create();

    $commentsContainer = new CommentsContainer($con, $video, $userLoggedInObj);
    echo $commentsContainer->create();
?>

</div>

<div class="recommendations">
    <?php
    $videoMatrix=new VideoMatrix($con, $userLoggedInObj);
    echo $videoMatrix->create(1, null, false);
    ?>
</div>
<?php $text=$_SERVER['PHP_SELF'];
$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : '';
$url=$protocol.$_SERVER["HTTP_HOST"].$text."?id=".$_GET["id"]; ?>
<!-- Trigger/Open The Modal -->
<!-- The Modal -->
<div id="myModal" class="modal1">

  <!-- Modal content -->
  <div class="modal-content1">
    <div class="modal-header1">
      <span class="close1">&times;</span>
      <h2>Share video through</h2>
    </div>
    <div class="modal-body1" id='bodyMargin'>
      <a id='blue' href="https://twitter.com/intent/tweet?text=<?php echo $url; ?>">
      <i class="fab fa-twitter fa-3x" aria-hidden="true"></i>
      <span>Twitter</span>
      </a>
      <a href="https://web.whatsapp.com/send?text=<?php echo $url; ?>" data-action="share/whatsapp/share">
      <i class="fab fa-whatsapp-square green fa-3x"></i>
      <span id='whats'>Whatsapp</span>
      </a>
      <a href="mailto:?subject=I wanted you to see this site&amp;body=Check out this video <?php echo $url; ?> ."
        title="Share by Email">
        <i class="fas fa-envelope fa-3x red" id='marginLeft1' aria-hidden="true"></i>
        <span id='marginLeft2'>Email</span>
      </a>
    </div>
    <div class="modal-footer1">
      <h5>Made by E-Classroom</h5>
    </div>
  </div>

</div>

<?php require_once("includes/footer.php"); ?>