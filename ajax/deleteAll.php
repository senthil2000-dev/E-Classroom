<?php
require_once("../includes/config.php");
$query=$con->prepare("DELETE FROM searchhistory");
$query->execute();
?>