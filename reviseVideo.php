<?php
require_once("includes/header.php");
require_once("includes/classes/VideoElement.php");
require_once("includes/classes/VideoDetailsFormProvider.php");
require_once("includes/classes/VideoDetails.php");
require_once("includes/classes/SelectThumbnail.php");

if(!User::isLoggedIn()) {
    header("Location: signIn.php");
}
if(!isset($_GET["videoId"])) {
    echo "No video selected";
    exit();
}

$video=new Video($con, $_GET["videoId"], $userLoggedInObj);
if($video->getUploadedBy()!=$userLoggedInObj->getUsername()) {
    echo "Not your video";
    exit();
}

$detailsMessage="";
if(isset($_POST["saveButton"])) {
    $videoData= new VideoDetails(
        null, 
        $_POST["titleInput"],
        $_POST["descriptionInput"],
        $_POST["degree"],
        $_POST["categoryInput"],
        $userLoggedInObj->getUsername()
    );

    if($videoData->updateDetails($con, $video->getId())) {
        $detailsMessage="<div class='alert alert-success'>
                            <strong>SUCCESS!</strong> Details updated successfully!
                        </div>";
        $video=new Video($con, $_GET["videoId"], $userLoggedInObj);
    }
    else {
        $detailsMessage="<div class='alert alert-danger'>
                            <strong>ERROR!</strong> Something went wrong
                        </div>";
    }
}
?>
<script src="assets/js/modifyVideoActions.js"></script>
<div class="reviseVideoContainer column">

    <div class="message">
        <?php echo $detailsMessage; ?>
    </div>

    <div class="topSection">
        <?php
            $videoElement=new VideoElement($video);
            echo $videoElement->play(false);
            $selectThumbnail=new SelectThumbnail($con, $video);
            echo $selectThumbnail->create();
        ?>
    </div>

    <div class="bottomSection">
        <?php
        $formProvider= new VideoDetailsFormProvider($con);
        echo $formProvider->createEditDetailsForm($video);
        ?>
    </div>

</div>