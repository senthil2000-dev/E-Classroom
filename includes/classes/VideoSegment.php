<?php
class VideoSegment{

    private $video, $largeMode;
    private $id2=0;

    public function __construct($video, $largeMode)  {
        $this->video=$video;
        $this->largeMode=$largeMode;
    }

    public function create() {
        $thumbnail=$this->createThumbnail();
        $details=$this->createDetails();
        $url = "stream.php?id=".$this->video->getId();
        $id=$this->video->getId();
        $selected=$this->id2==$this->video->getId() ? "playing" : "";

        if (basename($_SERVER["PHP_SELF"])=="createPlaylist.php") {
            return "<a onclick='addTo(this, $id)'>
                        <div class='videoSegment'>
                            $thumbnail
                            $details
                        </div>
                    </a>";
        }
        if(basename($_SERVER["PHP_SELF"])=="playlist.php") {
            return "<a id='$id'>
                        <div class='videoSegment'>
                            $thumbnail
                            $details
                        </div>
                    </a>";
        }

        if(basename($_SERVER["PHP_SELF"])=="stream2.php") {
            $url="stream2.php?id=".$this->video->getId();
            return "<a id='$selected' href='$url'>
                        <div class='videoSegment'>
                            $thumbnail
                            $details
                        </div>
                    </a>";
        }
        if(basename($_SERVER["PHP_SELF"])=="history.php") {
            $delete="<div class='dropdown'>
                        <ul class='dropbtn icons btn-right showLeft $id'>
                            <li></li>
                            <li></li>
                            <li></li>
                        </ul>
                        <div class='dropdown-content myDropdown' id='$id'>
                            <span class='removing $id'>Remove</span>
                        </div>
                    </div>";
            return "<a class='flexing' href='$url'>
                        <div class='videoSegment'>
                            $thumbnail
                            $details
                            $delete
                        </div>
                    </a>";
        }
        return "<a id='$id' href='$url'>
                    <div class='videoSegment'>
                        $thumbnail
                        $details
                    </div>
                </a>";
    }

    public function creating($id2) {
        $this->id2=$id2;
        return $this->create();
    }

    public function create2($a, $name, $id, $number) {
        $thumbnail=$this->createThumbnail2($number);
        $details=$this->createDetails2($a, $name);
        $url = "playlistvideos.php?id=".$id;

        return "<a href='$url'>
                    <div class='videoSegment'>
                        $thumbnail
                        $details
                    </div>
                </a>";
    }

    public function createThumbnail() {
        
        $thumbnail=$this->video->getThumbnail();
        $length=$this->video->getLength();

        return "<div class='thumbnail'>
                    <img src='$thumbnail'>
                    <div class='length'>
                        <span>$length</span>
                    </div>
                </div>";

    }

    public function createThumbnail2($number) {
        
        $thumbnail=$this->video->getThumbnail();
        $length=$this->video->getLength();
        $text=$number." videos";
        if($number==1)
        $text=$number." video";

        return "<div class='thumbnail'>
                    <img src='$thumbnail'>
                    <div class='length' id='playlist'>
                    <img src='assets/images/playlist.png' id='playlistThumbnail'>
                        $text
                    </div>
                </div>";

    }


    private function createDetails() {
        $title=$this->video->getTitle();
        $username=$this->video->getUploadedBy();
        $views=$this->video->getViews();
        $description=$this->createDescription();
        $timestamp=$this->video->getTimeStamp();

        return "<div class='details'>
                    <h3 class='title'>$title</h3>
                    <span class='username'>$username</span>
                    <div class='stats'>
                        <span class='viewCount'>$views views - </span>
                        <span class='timeStamp'>$timestamp</span>
                    </div>
                    $description
                </div>";

    }

    private function createDetails2($a, $name) {
        $title=$name;
        $username=$this->video->getUploadedBy();
        $timestamp=date("M j, Y", strtotime($a));

        return "<div class='details'>
                    <h3 class='title'>$title</h3>
                    <span class='username'>$username</span>
                    <div class='stats'>
                        <span class='timeStamp'>$timestamp</span>
                    </div>
                </div>";

    }

    private function createDescription() {
        if(!$this->largeMode) {
            return "";
        }
        else {
            $description=$this->video->getDescription();
            $description=(strlen($description)>350) ? substr($description, 0, 347) . "..." : $description;
            return "<span class='description'>$description</span>";
        }
    }
}
?>