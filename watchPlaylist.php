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

$query=$con->prepare("SELECT playlistName FROM playlists WHERE id=:id");
$query->bindParam(":id", $_GET["id"]);
$query->execute();
$name=$query->fetchColumn();
$table=$name.$_GET["id"];
$query=$con->prepare("SELECT * FROM $table");
$query->execute();
$_SESSION["ids"]=array();
$count=$query->rowCount();
    while($row=$query->fetch(PDO::FETCH_ASSOC)) {
        $_SESSION["ids"][]=$row["VIDEOID"];
    }
$_SESSION["num"]=0;
$url="stream2.php?id=".$_SESSION["ids"][0];
header("Location: $url");
?>

<?php require_once("includes/footer.php"); ?>