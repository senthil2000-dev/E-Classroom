<!DOCTYPE html>
<html>
<head>
<title>E-Classroom</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="assets/css/style.css">
<?php
require_once("includes/classes/Video.php");
require_once("includes/classes/VideoSegment.php");
require_once("includes/config.php");
    if(isset($_POST["id"])) {
        $id=$_POST["id"];
        $operation=$_POST["operation"];
        $video=new Video($con, $id, $_SESSION["userLoggedIn"]);
        $item=new VideoSegment($video, true);
        echo $item->create();
        if($operation==1) {
           array_push($_SESSION["videoIds"], $id); 
        }
        elseif ($operation==0) {
            $key = array_search($id, $_SESSION["videoIds"]);
            if (false !== $key){
                $length=sizeof($_SESSION["videoIds"]);
                unset($_SESSION["videoIds"][$key]);
            }

            for($i=$key; $i<$length-1; $i++) {
                $_SESSION["videoIds"][$i]=$_SESSION["videoIds"][$i+1];
            }
            unset($_SESSION["videoIds"][$i]);

        }
        
    }
    if(isset($_POST["tableName"])) {
        if(isset($_POST["tableName"])) {
            if(empty($_SESSION["videoIds"])) {
                header("Location: createPlaylist.php");
                exit();
            }
            $query=$con->prepare("INSERT INTO playlists(uploadedBy, playlistName) VALUES(:uploadedBy, :playlistName)");
            $query->bindParam(":uploadedBy", $_SESSION["userLoggedIn"]);
            $query->bindParam(":playlistName", $_POST["tableName"]);
            $query->execute();
            $playlistId=$con->lastInsertId();
            $playlistName=$_POST["tableName"].$playlistId;
            $query=$con->prepare("CREATE TABLE $playlistName(ID INT NOT NULL AUTO_INCREMENT,  
                                                VIDEOID VARCHAR (255) NOT NULL,  
                                                PRIMARY KEY (ID)  )");
            $query->execute();
            echo "<div class='alert alert-success'>Playlist created</div>";
            $id1=$_SESSION["videoIds"][0];
            $i=0;
            foreach($_SESSION["videoIds"] as $id) {
                $query=$con->prepare("INSERT INTO $playlistName(VIDEOID) VALUES(:videoId)");
                $query->bindParam(":videoId", $id);
                if($query->execute()) {
                    $i++;
                }
            }
            unset($_SESSION["videoIds"]);
            $video=new Video($con, $id1, $_SESSION["userLoggedIn"]);
            $item=new VideoSegment($video, true);
            $query=$con->prepare("SELECT timeUploaded FROM playlists WHERE id=:id");
            $query->bindParam(":id", $playlistId);
            $query->execute();
            $a=$query->fetchColumn();
            $thumbnail= $item->create2($a, $_POST["tableName"], $playlistId, $i);
            echo "<div id='mainContentContainer'>
                <div class='videoMatrix large'>
                    $thumbnail
                </div>
                </div>";
        }
    }

?>
