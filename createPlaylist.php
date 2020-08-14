<?php require_once("includes/header.php");
?>
<div class="videoSection">
    <?php
    $_SESSION["videoIds"]=array();
    $videos=array();

    $query=$con->prepare("SELECT * FROM videos WHERE uploadedBy=:uploadedBy");
    $query->bindParam(":uploadedBy", $usernameLoggedIn);
    $query->execute();

    if($usernameLoggedIn=="") {
        echo "You must sign in to create a playlist";
        exit();
    }

    while($row=$query->fetch(PDO::FETCH_ASSOC)) {
        $videos[]=new Video($con, $row, $userLoggedInObj);
    }

    $videoMatrix=new VideoMatrix($con, $userLoggedInObj);
    if(empty($videos)) {
        echo "You haven't uploaded any videos";
        exit(0);
    }
    echo $videoMatrix->create($videos, "Add your videos by clicking on them to create a playlist", false);
    echo "Added videos
            <div class='createPlaylist'>
            <div class='videoMatrix large' id='added'>
                
            </div>
            <form action='playlist.php' method='POST' id='playlistName' hidden> 
                <input type='text' name='tableName' placeholder='Playlistname' required>
                <input type='submit' name='submitButton'></form>
            </div>";
            ?>
</div>

<?php require_once("includes/footer.php"); ?>