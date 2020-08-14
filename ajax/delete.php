<?php
require_once("../includes/config.php");
$id=$_POST["id"];
$query=$con->prepare("DELETE FROM searchhistory WHERE id=:id");
$query->bindParam(":id", $id);
$query->execute();
$status=0;
$query=$con->prepare("SELECT count(*) as'count' FROM searchhistory WHERE username=:username AND statusPaused=:status");
$query->bindParam(":username", $username);
$query->bindParam(":status", $status);
$username=$_SESSION["userLoggedIn"];
$query->execute();
$data=$query->fetch(PDO::FETCH_ASSOC);
echo $data["count"];
?>