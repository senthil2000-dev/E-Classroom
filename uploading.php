<?php 
require_once("includes/header.php");
require_once("includes/classes/VideoDetails.php");
require_once("includes/classes/VideoRendering.php");
if(!isset($_POST["uploadButton"])){
    echo "No file sent to page.";
    exit();
}

$videoDetails= new VideoDetails(
                            $_FILES["fileInput"],
                            $_POST["titleInput"],
                            $_POST["descriptionInput"],
                            $_POST["degree"],
                            $_POST["categoryInput"],
                            $userLoggedInObj->getUsername()
                        );

$videoRendering= new VideoRendering($con);
$wasSuccessful=$videoRendering->upload($videoDetails);

if($wasSuccessful){
    echo "Video uploaded successfully";
}
 ?>