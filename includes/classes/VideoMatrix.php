<?php
require_once("PlaylistItem.php");
class VideoMatrix {
    private $con, $userLoggedInObj;
    private $largeMode=false;
    private $gridClass="videoMatrix";
    private $playlistId=0;
    private $id2=0;

    public function __construct($con, $userLoggedInObj) {
        $this->con=$con;
        $this->userLoggedInObj=$userLoggedInObj;
    }

    public function create($videos, $title, $showFilter, $history=false, $days=0) {

        if($videos==null && $history==false) {
            $gridItems=$this->generateItems();
        }
        elseif($videos==1) {
            $gridItems=$this->generateItemsByCategory();
        }
        else {
            $gridItems=$this->generateItemsFromVideos($videos);
        }

        $header="";

        if($title != null) {
            $header=$this->createGridHeader($title, $showFilter, $history, $days);
        }

        return "$header
                <div class='$this->gridClass'>
                    $gridItems
                </div>";
    }

    public function createPlaylists($playlists) {
        $gridItems=$this->generatePlaylists($playlists);
        return "<div class='$this->gridClass'>
                    $gridItems
                </div>";
    }

    public function generatePlaylists($playlists) {
        $elementsHtml="";

        foreach($playlists as $playlist) {
            $item=new PlaylistItem($playlist, $this->con, $this->userLoggedInObj);
            $elementsHtml.=$item->create();
        }

        return $elementsHtml;
    }

    public function generateItems() {
        $query=$this->con->prepare("SELECT * FROM videos WHERE uploadDate>=now()-INTERVAL 7 DAY ORDER BY views DESC LIMIT 15");
        $query->execute();

        $elementsHtml="";
        while($row=$query->fetch(PDO::FETCH_ASSOC)) {
            $video = new Video($this->con, $row, $this->userLoggedInObj);
            $item=new VideoSegment($video, $this->largeMode);
            $elementsHtml.=$item->create();
        }

        return $elementsHtml;
    }

    public function generateItemsByCategory() {
        $video=new Video($this->con, $_GET["id"], $this->userLoggedInObj);
        $category=$video->getCategory();
        $query=$this->con->prepare("SELECT * FROM videos WHERE category=:category AND id!=:id ORDER BY RAND() LIMIT 15");
        $query->bindParam(":category", $category);
        $query->bindParam(":id", $_GET["id"]);
        $query->execute();

        $elementsHtml="";
        while($row=$query->fetch(PDO::FETCH_ASSOC)) {
            $video = new Video($this->con, $row, $this->userLoggedInObj);
            $item=new VideoSegment($video, $this->largeMode);
            $elementsHtml.=$item->create();
        }

        return $elementsHtml;
    }

    public function generateItemsFromVideos($videos) {
        $elementsHtml="";

        foreach($videos as $video) {
            $item=new VideoSegment($video, $this->largeMode);
            if($this->id2==0) {
                $elementsHtml.=$item->create();
            }
            else {
                $elementsHtml.=$item->creating($this->id2);
            }
            
        }

        return $elementsHtml;
    }

    public function createGridHeader($title, $showFilter, $history, $days) {
        $filter="";
        $values=[7, 31, 183, 365, 730, 1095, 1460, 1826];
        $text=["Last week", "Last Month", "Last 6 Months", "Last 1 year", "Last 2 years", "Last 3 years", "Last 4 years", "Last 5 years"];
        $html="";
        for($i=0 ; $i<sizeof($values); ++$i) {
            if($days!=$values[$i])
                $html.="<option value=$values[$i]>$text[$i]</option>";
            elseif($days==0){
                $html.="<option value=$values[$i] selected>$text[$i]</option>";
            }
            else{
                $html.="<option value=$values[$i] selected>$text[$i]</option>";
            }
        }

        if($history) {
            $query=$this->con->prepare("SELECT statusPaused FROM users WHERE username=:user");
            $query->bindParam(":user", $username);
            $username=$this->userLoggedInObj->getUsername();
            $query->execute();
            $_SESSION["status"]=$query->fetchColumn();
            $checked=$_SESSION["status"]?"checked":"";
            $filter="<form id='submitForm' action='history.php' method='GET'>
                    <div class='right'>
                        <span class='deleteMessage' onclick='deleteAll2()'>Delete full history</span>
                        <img class='deleteSearch' onmouseover='hover(this)' onmouseout='unhover(this)' onclick='deleteAll2()' src='assets\images\icons\deletefull.png' title='delete full history' alt='Delete Full History Button'></img>
                        <label>Pause watch history:</label>
                        <label class='switch'>
                        <input type='checkbox' id='checking' onchange='status()' $checked>
                        <span class='slider round'></span>
                        </label>
                         <label for='range' id='split'>Choose a range of time:</label>
                         <select name='rangeValue' id='range' onchange='submitForm2()'>
                         $html
                         </select>
                     </div>
                     </form>";
         }

        if($showFilter) {
           $link="http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
           $urlArray=parse_url($link);
           $query=$urlArray["query"];
           parse_str($query,$params);

           unset($params["orderBy"]);

           $newQuery=http_build_query($params);
           $newUrl=basename($_SERVER["PHP_SELF"]) . "?" . $newQuery;

           $filter="<div class='right'>
                        <span>Order by:</span>
                        <a href='$newUrl&orderBy=uploadDate'>Upload date</a>
                        <a href='$newUrl&orderBy=views'>Most viewed</a>
                    </div>";
        }

        if(basename($_SERVER["PHP_SELF"])=="playlistvideos.php") {
            $url="watchPlaylist.php?id=".$this->playlistId;
            $filter="<div class='right'>
                        <a class='btn btn-primary' id='playButton' href='$url'>Play Playlist</a>
                    </div>";
        }

        return "<div class='videoMatrixHeader'>
                    <div class='left'>
                        $title
                    </div>
                    $filter
                </div>";
    }

    public function createLarge($videos, $title, $showFilter, $history=false, $days=0) {
        $this->gridClass .= " large";
        $this->largeMode=true;
        return $this->create($videos, $title, $showFilter, $history, $days);
    }

    public function createLarge2($videos, $title, $showFilter, $id, $history=false, $days=0) {
        $this->gridClass .= " large";
        $this->largeMode=true;
        $this->playlistId=$id;
        return $this->create($videos, $title, $showFilter, $history, $days);
    }

    public function create2($videos, $title, $showFilter, $id, $history=false, $days=0) {
        $this->id2=$id;
        return $this->create($videos, $title, $showFilter, $history, $days);
    }
}
?>
