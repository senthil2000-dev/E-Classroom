<?php
require_once("ChannelDetails.php");
class ChannelGenerator {
    private $con, $userLoggedInObj, $channelDetails;

    public function __construct($con, $userLoggedInObj, $channelName) {
        $this->con=$con;
        $this->userLoggedInObj=$userLoggedInObj;
        $this->channelDetails=new ChannelDetails($con, $channelName);
    }

    public function create() {
        $channelName=$this->channelDetails->getChannelName();
        
        if(!$this->channelDetails->userExists()) {
            return "User does not exist";
        }

        $coverPhotoSection=$this->createCoverPhotoSection();
        $headerSection=$this->createHeaderSection();
        $tabsSection=$this->createTabsSection();
        $contentSection=$this->createContentSection();

        return "<div class='channelContainer'>
                    $coverPhotoSection
                    $headerSection
                    $tabsSection
                    $contentSection
                </div>";
    }

    public function createCoverPhotoSection() {
        $coverPhotoSrc=$this->channelDetails->getCoverPhoto();
        $name=$this->channelDetails->getChannelFullName();
        return "<div class='coverPhotoContainer'>
                    <img src='$coverPhotoSrc' class='coverPhoto'>
                    <div>
                    <span class='channelName'>$name</span>
                    </div>
                </div>";
}

    public function createHeaderSection() {
        $profileImage=$this->channelDetails->getProfilePic();
        $name=$this->channelDetails->getChannelFullName();
        $subCount=$this->channelDetails->getSubscriberCount();

        $button=$this->createHeaderButton();

        return "<div class='channelHeader'>
                    <div class='userInfoContainer'>
                        <img class='profileImage' src='$profileImage'>
                        <div class='userInfo'>
                            <span class='title'>$name</span>
                            <span class='subscriberCount'>$subCount subscribers</span>
                        </div>
                    </div>

                    <div class='buttonContainer'>
                        <div class='buttonItem'>
                            $button
                        </div>
                    </div>
                </div>";
    }

    public function createTabsSection() {
        return "<ul class='nav nav-tabs' role='tablist'>
                    <li class='nav-item'>
                    <a class='nav-link active' id='videos-tab' data-toggle='tab' href='#videos' role='tab' aria-controls='videos' aria-selected='true'>VIDEOS</a>
                    </li>
                    <li class='nav-item'>
                    <a class='nav-link' id='playlists-tab' data-toggle='tab' href='#playlists' role='tab' aria-controls='about' aria-selected='false'>PLAYLISTS</a>
                    </li>
                    <li class='nav-item'>
                    <a class='nav-link' id='about-tab' data-toggle='tab' href='#about' role='tab' aria-controls='about' aria-selected='false'>ABOUT</a>
                    </li>
                </ul>";
    }

    public function createContentSection() {

        $videos=$this->channelDetails->getUsersVideos();
        $playlists=$this->channelDetails->getUsersPlaylists();

        if(sizeof($playlists)>0) {
            $videoMatrix=new VideoMatrix($this->con, $this->userLoggedInObj);
            $playlistSection=$videoMatrix->createPlaylists($playlists);
        }
        else {
            $playlistSection="<span>This user has no playlists</span>";
        }


        if(sizeof($videos)>0) {
            $videoMatrix=new VideoMatrix($this->con, $this->userLoggedInObj);
            $videoMatrixHtml=$videoMatrix->create($videos, null, false);
        }
        else {
            $videoMatrixHtml="<span>This user has no videos</span>";
        }

        $aboutSection=$this->createAboutSection();

        return "<div class='tab-content channelContent'>
                    <div class='tab-pane fade show active' id='videos' role='tabpanel' aria-labelledby='videos-tab'>
                        $videoMatrixHtml
                    </div>
                    <div class='tab-pane fade' id='playlists' role='tabpanel' aria-labelledby='playlists-tab'>
                        $playlistSection
                    </div>
                    <div class='tab-pane fade' id='about' role='tabpanel' aria-labelledby='about-tab'>
                        $aboutSection
                    </div>
                </div>";
    }

    private function createHeaderButton() {
        if($this->userLoggedInObj->getUsername() == $this->channelDetails->getChannelName()) {
            return "<form id='file-upload' enctype='multipart/form-data'>
                            <label for='exampleFormControlFile1' class='btn btn-primary' >Change profile pictue</label>
                            <input type='file' class='form-control-file' name='image' onchange='submitForm()' id='exampleFormControlFile1' required hidden>
                            <button type='submit' id= 'press' hidden>Change Profile picture</button>
                     </form>";
        }
        else {
            return ButtonHtmlGenerator::createSubscriberButton($this->con, $this->channelDetails->getChannelObj(), $this->userLoggedInObj);
        }
     }

    private function createAboutSection() {
        $html="<div class='section'>
                    <div class='title'>
                        <span>Details</span>
                    </div>
                    <div class='values'>";

        $details=$this->channelDetails->getAllUserDetails();
        foreach($details as $key=>$value) {
            $html.= "<span>$key: $value</span>";
        }

        $html.= "</div></div>";

        return $html;
    }
}
?>