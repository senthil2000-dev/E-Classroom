<?php
class VideoElement {

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
        $filePath=$this->video->getFilePath();
        return "<video class='videoElement' controls $autoplay>
                    <source src='$filePath' type='video/mp4'>
                    Your browser not support the video tag
                </video>";
    }

}
?>