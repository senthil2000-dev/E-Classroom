<?php
class BranchwiseProvider {
    private $con, $userLoggedInObj;

    public function __construct($con, $userLoggedInObj) {
        $this->con=$con;
        $this->userLoggedInObj=$userLoggedInObj;
    }

    public function getVideos($branch, $deg) {
        $query=$this->con->prepare("SELECT id FROM departments WHERE name=:name");
        $query->bindParam(":name", $branch);
        $query->execute();
        $cat=$query->fetchColumn();
        $videos=array();
        $query=$this->con->prepare("SELECT * FROM videos WHERE category=:category AND degree=:deg");
        $query->bindParam(":category", $cat);
        $query->bindParam(":deg", $deg);
        $query->execute();
        while($row=$query->fetch(PDO::FETCH_ASSOC)) {
            $video=new Video($this->con, $row, $this->userLoggedInObj);
            array_push($videos, $video);
        }
        return $videos;
    }
}
?>