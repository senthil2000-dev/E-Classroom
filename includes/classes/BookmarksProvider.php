<?php
class BookmarksProvider {
    private $con, $userLoggedInObj;

    public function __construct($con, $userLoggedInObj) {
        $this->con=$con;
        $this->userLoggedInObj=$userLoggedInObj;
    }

    public function getVideos() {
        $videos=array();
        $query=$this->con->prepare("SELECT videoId FROM bookmarks WHERE username=:username ORDER BY id DESC");
        $query->bindParam(":username", $username);
        $username=$this->userLoggedInObj->getUsername();
        $query->execute();

        while($row=$query->fetch(PDO::FETCH_ASSOC)) {
            $videos[]=new Video($this->con, $row["videoId"], $this->userLoggedInObj);
        }

        return $videos;
    }
}
?>