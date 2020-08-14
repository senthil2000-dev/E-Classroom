<?php
require_once("includes/header.php");
require_once("includes/classes/ChannelGenerator.php");
if(isset($_GET["username"])) {
    $channelName=$_GET["username"];
}
else {
    echo "Channel not found";
    exit();
}
$channelGenerator=new ChannelGenerator($con, $userLoggedInObj, $channelName);
echo $channelGenerator->create();
?>