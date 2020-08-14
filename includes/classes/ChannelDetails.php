<?php
require_once("Playlist.php");
class ChannelDetails {
    private $con, $channelObj;

    public function __construct($con, $channelName) {
        $this->con=$con;
        $this->channelObj=new User($con, $channelName);
    }

    public function getChannelObj() {
        return $this->channelObj;
    }

    public function getChannelName() {
        return $this->channelObj->getUsername();
    }

    public function userExists() {
        $query=$this->con->prepare("SELECT * FROM users WHERE username=:username");
        $query->bindParam(":username", $channelName);
        $channelName=$this->getChannelName();
        $query->execute();

        return $query->rowCount() != 0;
    }

    public function getCoverPhoto() {
        return "assets/images/coverPhotos/default-cover-photo.jpg";
    }

    public function getChannelFullName() {
        return $this->channelObj->getName();
    }

    public function getProfilePic() {
        return $this->channelObj->getProfilePic();
    }

    public function getSubscriberCount() {
        return $this->channelObj->getSubscriberCount();
    }

    public function getUsersVideos() {
        $query=$this->con->prepare("SELECT * FROM videos WHERE uploadedBy=:uploadedBy ORDER BY uploadDate DESC");
        $query->bindParam(":uploadedBy", $username);
        $username=$this->getChannelName();
        $query->execute();

        $videos=array();
        while($row=$query->fetch(PDO::FETCH_ASSOC)) {
            $videos[]=new Video($this->con, $row, $this->channelObj->getUsername());
        }

        return $videos;
    }

    public function getUsersPlaylists() {
        $query=$this->con->prepare("SELECT * FROM playlists WHERE uploadedBy=:uploadedBy ORDER BY timeUploaded DESC");
        $query->bindParam(":uploadedBy", $username);
        $username=$this->getChannelName();
        $query->execute();

        $playlists=array();
        while($row=$query->fetch(PDO::FETCH_ASSOC)) {
            $playlists[]=new Playlist($this->con, $row, $this->channelObj->getUsername());
        }

        return $playlists;
    }

    public function getAllUserDetails() {
        return array(
          "Name"=>$this->getChannelFullName(),
          "Username"=>$this->getChannelName(),
          "Subscribers"=>$this->getSubscriberCount(),
          "Total views"=>$this->getTotalViews(),
          "Sign up date"=>$this->getSignUpDate()

        );
    }

    private function getTotalViews() {
        $query=$this->con->prepare("SELECT sum(views) FROM videos WHERE uploadedBy=:uploadedBy");
        $query->bindParam(":uploadedBy", $username);
        $username=$this->getChannelName();
        $query->execute();

        return $query->fetchColumn();
    }

    private function getSignUpDate() {
        $date=$this->channelObj->getSignUpDate();
        return date("F jS, Y", strtotime($date));
    }
}
?>