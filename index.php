<?php require_once("includes/header.php"); ?>

<div class="videoSection">
    <?php

    $subscriptionsGenerator=new SubscriptionsGenerator($con, $userLoggedInObj);
    $subscriptionVideos=$subscriptionsGenerator->getVideos();

    $videoMatrix=new VideoMatrix($con, $userLoggedInObj);

    if(User::isLoggedIn() && sizeof($subscriptionVideos)>0) {
        echo $videoMatrix->create($subscriptionVideos, "Subscriptions", false);
    }

    echo $videoMatrix->create(null, "Latest", false);


    ?>

</div>

<?php require_once("includes/footer.php"); ?>