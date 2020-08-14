<?php
require_once("../includes/config.php");
$id=$_POST["id"];
$username=$_SESSION["userLoggedIn"];
$status=0;
$query=$con->prepare("DELETE FROM history WHERE videoId=:id AND user=:user AND statusPaused=:status");
$query->bindParam(":id", $id);
$query->bindParam(":user", $username);
$query->bindParam(":status", $status);
$query->execute();
$query=$con->prepare("SELECT count(*) as'count' FROM history WHERE user=:username AND statusPaused=:status");
$query->bindParam(":username", $username);
$query->bindParam(":status", $status);
$query->execute();
$data=$query->fetch(PDO::FETCH_ASSOC);
echo $data["count"];
?>