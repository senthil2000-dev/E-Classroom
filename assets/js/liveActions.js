$(document).ready(function () {
  Webcam.set({
    width: 470,
    height: 370,
    image_format: 'jpeg',
    jpeg_quality: 90
});

Webcam.attach( '#videoElement' );



});

function take_snapshot() {
    Webcam.snap( function(data_uri) {
        console.log(data_uri);
        $(".image-tag").val(data_uri);
        document.getElementById('results').innerHTML = '<img src="'+data_uri+'"/>';
    } );
}

function shouldSubmit(e) {
    console.log(e);
    if($(".image-tag").val())
        document.getElementById("pic").submit();
    else{
        e.preventDefault();
        alert("PLEASE TAKE A SNAPSHOT");
    }
  }