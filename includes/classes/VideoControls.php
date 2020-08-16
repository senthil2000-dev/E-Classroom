<?php
require_once("includes/classes/VideoRemarks.php");
class  VideoControls {
    private $con, $video, $userLoggedInObj;

    public function __construct($con, $video, $userLoggedInObj) {
            $this->video=$video;
            $this->con=$con;
            $this->userLoggedInObj=$userLoggedInObj;
    }

    public function create() {
        return $this->createVideoInfo() . $this->createPublisherInfo();
    }

    private function createVideoInfo() {
        $title=$this->video->getTitle();
        $views=$this->video->getViews();

        $videoRemarks=new VideoRemarks($this->video, $this->userLoggedInObj);
        $controls=$videoRemarks->createSection();

        return "<div class='videoInfo'>
                    <h1>$title</h1>
                    
                    <div class='secondarySection'>
                        <span class='viewCount'>$views views</span>
                        $controls
                    </div>
                </div>";
    }

    private function createPublisherInfo() {

        $description=$this->video->getDescription();
        $uploadDate=$this->video->getUploadDate();
        $uploadedBy=$this->video->getUploadedBy();
        $channelButton=ButtonHtmlGenerator::createChannelButton($this->con, $uploadedBy);

        if($uploadedBy == $this->userLoggedInObj->getUsername()) {
            $actionButton=ButtonHtmlGenerator::createReviseVideoButton($this->video->getId());
        }
        else {
            $userToObj=new User($this->con, $uploadedBy);
            $actionButton=ButtonHtmlGenerator::createSubscriberButton($this->con, $userToObj, $this->userLoggedInObj);
            //$actionButton="";
        }
        $btn="btn";
        $share="<a class='iconMargin' id='$btn'>
                    <i class='fas fa-2x fa-share-square'></i>
                </a>";
        if(basename($_SERVER["PHP_SELF"])=="stream2.php") {
            $share="";
        }
        return "<div class='publisherInfo'>
                    <div class='topRow'>
                    $channelButton

                    <div class='uploadInfo'>
                        <span class='owner'>
                            <a href='channel.php?username=$uploadedBy'>
                                $uploadedBy
                            </a>
                        </span>
                        <span class='date'>Published on $uploadDate</span>
                    </div>
                    $share
                    $actionButton
                    </div>

                    <div class='descriptionContainer'>
                        $description
                    </div>
        
                </div>";
    }
}
?>
