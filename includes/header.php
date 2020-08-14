<?php 
require_once("includes/config.php");
require_once("includes/classes/ButtonHtmlGenerator.php");
require_once("includes/classes/User.php");
require_once("includes/classes/Video.php");
require_once("includes/classes/VideoMatrix.php");
require_once("includes/classes/VideoSegment.php");
require_once("includes/classes/SubscriptionsGenerator.php");
require_once("includes/classes/NavigationBar.php");

$usernameLoggedIn=User::isLoggedIn() ? $_SESSION["userLoggedIn"] : "";
$userLoggedInObj = new User($con, $usernameLoggedIn);
?>
<!DOCTYPE html>
<html>
<head>
<title>E-Classroom</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="assets/css/all.min.css">
<link rel="stylesheet" type="text/css" href="assets/css/style.css">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
<script src="assets/js/commonActions.js"></script>

<script src="assets/js/subscriberActions.js"></script>

</head>
<body>
    <div id="pageContainer">
        
            <div id="mastHeadContainer">
            <button class="navShowHide">
            <img src="assets/images/icons/menu.png">
            </button>
            <a class="logoContainer" href="index.php">
                <img src="assets/images/icons/eClassroomLogo.jpg" title="logo" alt="Site logo">
            </a>

            <div class="searchBarContainer">
                <form action="search.php" method="GET">
                    <input type="text" class="searchBar" name="term" placeholder="Search...">
                    <button class="searchButton">
                    <img src="assets/images/icons/search.png">
                    </button>
                </form>
            </div>

            <div class="rightIcons">
            <?php
            if($usernameLoggedIn=="") {
                echo
                "<a onclick='notSignedIn()' class='uploadButton'>";
            }
            else {
                echo
                "<a href='upload.php' class='uploadButton'>";
            }
            ?>
                <img class='upload' src='assets/images/icons/upload.png'>
                </a>
                <?php echo ButtonHtmlGenerator::createSelfChannelNavButton($con, $usernameLoggedIn); ?>
            </div>
            </div>

        <div id="sideBarContainer" style="display:none;">
            <?php
            $navBar=new NavigationBar($con, $userLoggedInObj);
            echo $navBar->create();
            ?>
        </div>

        <div id="mainSectionContainer">
        <div id="mainContentContainer">