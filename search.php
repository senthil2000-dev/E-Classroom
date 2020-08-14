<?php
require_once("includes/header.php");
require_once("includes/classes/SearchResultsProvider.php");

if(!isset($_GET["term"]) || $_GET["term"]=="") {
    echo "You must enter a search term";
    exit();
}

$term=$_GET["term"];
$url="search.php?term=".$term;
$username=$userLoggedInObj->getUsername();
$query=$con->prepare("SELECT statusPaused2 FROM users WHERE username=:user");
$query->bindParam(":user", $username);
$query->execute();
$status=$query->fetchColumn();
$query=$con->prepare("SELECT * FROM searchhistory WHERE username=:user AND searchTopic=:term AND statusPaused=:status");
$query=$con->prepare("INSERT INTO searchhistory(searchTopic, username, searchResults, statusPaused) VALUES(:searchTopic, :username, :searchResults, :status)");
$query->bindParam(":searchTopic", $term);
$query->bindParam(":username", $usernameLoggedIn);
$query->bindParam(":searchResults", $url);
$query->bindParam(":status", $status);
$query->execute();

if(!isset($_GET["orderBy"])||$_GET["orderBy"]=="views") {
    $orderBy="views";
}
else {
    $orderBy="uploadDate";
}

$searchResultsProvider = new SearchResultsProvider($con, $userLoggedInObj);
$videos=$searchResultsProvider->getVideos($term, $orderBy);

$videoMatrix= new VideoMatrix($con, $userLoggedInObj);
?>

<div class="largeVideoMatrixContainer">
    <?php
    if(sizeof($videos)>1) {
        echo $videoMatrix->createLarge($videos, sizeof($videos) . " results found", true);
    }
    elseif(sizeof($videos)==1) {
        echo $videoMatrix->createLarge($videos, sizeof($videos) . " result found", true);
    }
    else {
        echo "No results found";
    }
    ?>
</div>
<?php
require_once("includes/footer.php");
?>