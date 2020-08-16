<?php
require_once("includes/header.php");
require_once("includes/classes/VideoDetailsFormProvider.php");

  if($userLoggedInObj->getUsername()==""){
    echo "You must be logged in to upload a video";
    exit();
  }
  if(!isset($_SESSION["success"])) {
    $_SESSION["userTrying"]="1";
    header("Location:authenticate.php");
  }
?>
<style>
    #my-preview {
    height: 75vh;
    width: 75vw;
    margin: 0 10vw;
    }
</style>
<div class="column">
<button class="btn btn-success" id="btn-start-recording">Start Recording</button>
<button class="btn btn-danger" id="btn-stop-recording" disabled="disabled">Stop Recording</button>
<p><strong>NOTE:</strong> The latest recorded version will be saved to server</p>
<hr>
<video id="my-preview" controls autoplay></video>
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
<script src="https://cdn.webrtc-experiment.com/RecordRTC.js"></script>
<script src="https://webrtc.github.io/adapter/adapter-latest.js"></script>
<script>
    var video = document.getElementById('my-preview');
    var formData = new FormData();
    var recorder;
    document.getElementById('btn-start-recording').addEventListener("click", function(){
        this.disabled = true;
        navigator.mediaDevices.getUserMedia({
            audio: true, 
            video: true
        }).then(function(stream) {
            setSrcObject(stream, video);
            video.play();
            video.muted = true;
            recorder = new RecordRTCPromisesHandler(stream, {
                mimeType: 'video/webm',
                bitsPerSecond: 1280000
            });
            recorder.startRecording().then(function() {
                console.info('Recording...');
            }).catch(function(error) {
                console.error('Cannot start recording: ', error);
            });
            recorder.stream = stream;
            document.getElementById('btn-stop-recording').disabled = false;
        }).catch(function(error) {
            console.error("Cannot access navigators media devices: ", error);
        });
    }, false);

    document.getElementById('btn-stop-recording').addEventListener("click", function(){
        this.disabled = true;
        recorder.stopRecording().then(function() {
            console.info('stopped Recording');
            var videoBlob = recorder.blob;
            var file = new File([videoBlob], 'filename.webm', {
                type: 'video/webm'
            });
        recorder.stream.stop();
        document.getElementById('btn-start-recording').disabled = false;
        formData.append('fileInput', file);
        }).catch(function(error) {
            console.error('failed to stopRecording', error);
        });
    }, false);

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
                window.location="index.php";
            }
        })
    }
</script>