<?php
require_once("includes/header.php");
require_once("includes/classes/VideoDetailsFormProvider.php");
if (isset($_POST["submitButton"]))
{   $content = "";
    foreach($_SESSION["videoIds"] as $id) {
        $query=$con->prepare("SELECT filePath from videos WHERE id=:id");
        $query->bindParam(":id", $id);
        $query->execute();
        $fileLoc=$query->fetchColumn();
        $content .= "file " . $fileLoc . "\n";
    }
    file_put_contents("mergeNames.txt", $content);
    $ffmpegPath=realpath("ffmpeg/bin/ffmpeg.exe");
 
    $command = "$ffmpegPath -f concat -i mergeNames.txt -c copy merged.mp4";
    system($command);
    ?>
    <div class="column">
    <?php
    $formProvider=new VideoDetailsFormProvider($con);
    echo $formProvider->createRecordForm();
    ?>
    </div>
    <div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-labelledby="loadingModal" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
            
            <div class="modal-body">
                Please wait. This might take a while.
                <img src="assets/images/icons/loading-spinner.gif">
            </div>
            
            </div>
        </div>
    </div>
    <script>
        var formData = new FormData();
        var getFileBlob = function (url, cb) {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", url);
        xhr.responseType = "blob";
        xhr.addEventListener('load', function() {
            cb(xhr.response);
        });
        xhr.send();
};

var blobToFile = function (blob, name) {
        var file = new File([blob], name, {
                type: 'video/mp4'
            });
            return file;
};

var getFileObject = function(filePathOrUrl, cb) {
       getFileBlob(filePathOrUrl, function (blob) {
          cb(blobToFile(blob, 'merged.mp4'));
       });
};

getFileObject('merged.mp4', function (fileObject) {
     console.log(fileObject);
     formData.append('fileInput', fileObject);
}); 
        
        document.getElementById("saveRecording").addEventListener("click", function(){
            event.preventDefault();
            console.log(this);
            this.value=1;
            var secondForm = jQuery(document.forms['recordForm']).serializeArray();
            for (var i=0; i<secondForm.length; i++)
                formData.append(secondForm[i].name, secondForm[i].value);
            formData.append("uploadButton", "1");
            $("#loadingModal").modal("show");
            uploadVideoToServer(formData);
        });
        function uploadVideoToServer(formData) {
        $.ajax({
                url: 'uploading.php',
                method: 'POST',
                data: formData,
                contentType:false,
                cache:false,
                processData:false,
                success: function(response) {
                    console.log(response);
                    $.post("ajax/deleteMerged.php").done(function(){
                        console.log("done");
                        window.location="index.php";
                    });
                }
            })
        }
    </script>
<?php

}
else {
?>

<div class="videoSection">
    <?php
    $_SESSION["videoIds"]=array();
    $videos=array();

    $query=$con->prepare("SELECT * FROM videos WHERE uploadedBy=:uploadedBy");
    $query->bindParam(":uploadedBy", $usernameLoggedIn);
    $query->execute();

    if($usernameLoggedIn=="") {
        echo "You must sign in to merge videos";
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
    echo $videoMatrix->create($videos, "Add your videos to merge by clicking on them", false);
    echo "Added videos
            <div class='createPlaylist'>
            <div class='videoMatrix large' id='added'>
                
            </div>
            <form method='POST' id='playlistName' hidden> 
                <input type='submit' name='submitButton'></form>
            </div>";
            ?>
</div>
<?php
}
require_once("includes/footer.php"); ?>