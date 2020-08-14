<?php
class VideoElement2 {

    private $video;

    public function __construct($video) {
            $this->video=$video;
    }

    public function play($autoplay) {
        if($autoplay) {
            $autoplay="autoplay";
        }
        else {
            $autoplay = "";
        }
    
        if($this->video->getId()!=$_SESSION["ids"][$_SESSION["num"]]) {
            $_SESSION["num"]=array_search($this->video->getId(),$_SESSION["ids"]);
        }
        $i=$_SESSION["num"]+1;
        $filePath=$this->video->getFilePath();
        if(isset($_SESSION["ids"][$i])) {
            $url="stream2.php?id=".$_SESSION["ids"][++$_SESSION["num"]];
        }
        else {
            $url="";
        }
        
        return "<video class='videoElement' controls $autoplay onended='callf(\"$url\")' >
                    <source src='$filePath' type='video/mp4'>
                    Your browser not support the video tag
                </video>";
    }

}
?>