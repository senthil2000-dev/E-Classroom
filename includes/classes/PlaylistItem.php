<?php
class PlaylistItem {
    private $playlist, $con, $userLoggedInObj;

    public function __construct($playlist, $con, $userLoggedInObj) {
        $this->playlist=$playlist->sqlData;
        $this->con=$con;
        $this->userLoggedInObj=$userLoggedInObj;
    }

    public function create() {
        $playlist1=$this->playlist;
        $playlistName=$playlist1["playlistName"].$playlist1["id"];
        $query=$this->con->prepare("SELECT VIDEOID FROM $playlistName");
        $query->execute();
        $i=$query->rowCount();
        $id=$query->fetchColumn();
        $video=new Video($this->con, $id, $this->userLoggedInObj->getUsername());
        $item=new VideoSegment($video, false);
        $thumbnail= $item->create2($playlist1["timeUploaded"], $playlist1["playlistName"], $playlist1["id"], $i);
        return $thumbnail;
    }
}
?>