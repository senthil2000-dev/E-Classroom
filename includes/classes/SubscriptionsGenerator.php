<?php
class SubscriptionsGenerator {
    private $con, $userLoggedInObj;

    public function __construct($con, $userLoggedInObj) {
        $this->con=$con;
        $this->userLoggedInObj=$userLoggedInObj;
    }

    public function getVideos() {
        $videos=array();
        $subscriptions=$this->userLoggedInObj->getSubscriptions();

        if(sizeof($subscriptions)>0) {
            
            $condition="";
            $k=0;
            while($k<sizeof($subscriptions)) {
                
                if($k==0) {
                    $condition.= "WHERE uploadedBy=?";
                }
                else {
                    $condition.= " OR uploadedBy=?";
                }
                $k++;
            }

            $videoSql="SELECT * FROM videos $condition ORDER BY uploadDate DESC";
            $videoQuery=$this->con->prepare($videoSql);

            $k=1;
            foreach($subscriptions as $sub) {
                 $subUsername=$sub->getUsername();
                 $videoQuery->bindValue($k, $subUsername);
                 $k++;
            }

            $videoQuery->execute();
            while($row=$videoQuery->fetch(PDO::FETCH_ASSOC)) {
                $video=new Video($this->con, $row, $this->userLoggedInObj);
                array_push($videos, $video);
            }


        }
        
        return $videos;
    }
}
?>