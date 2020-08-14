<?php
class VideoDetails{
    public $videoDataArray,$title,$description,$degree,$category,$uploadedBy;

    public function __construct($videoDataArray,$title,$description,$degree,$category,$uploadedBy){
        $this->videoDataArray=$videoDataArray;
        $this->title=$title;
        $this->description=$description;
        $this->degree=$degree;
        $this->category=$category;
        $this->uploadedBy=$uploadedBy;
    }

    public function updateDetails($con, $videoId) {
        $query=$con->prepare("UPDATE videos SET title=:title, description=:description, degree=:degree, category=:category WHERE id=:videoId");
        $query->bindParam(":title", $this->title);
        $query->bindParam(":description", $this->description);
        $query->bindParam(":degree", $this->degree);
        $query->bindParam(":category", $this->category);
        $query->bindParam(":videoId", $videoId);
        return $query->execute();
    }
}
?>