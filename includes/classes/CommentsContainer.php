<?php
class  CommentsContainer {
    private $con, $video, $userLoggedInObj;

    public function __construct($con, $video, $userLoggedInObj) {
            $this->video=$video;
            $this->con=$con;
            $this->userLoggedInObj=$userLoggedInObj;
    }

    public function create() {
        return $this->createCommentsContainer();
    }

    private function createCommentsContainer() {
        $numOfComments=$this->video->getNumberOfComments();
        $postedBy = $this->userLoggedInObj->getUsername();
        $videoId=$this->video->getId();
        
        $profilePic=ButtonHtmlGenerator::createChannelButton($this->con, $postedBy);
        $postAction="postQuery(this, \"$postedBy\", $videoId, null, \"comments\")";
        $commentButton=ButtonHtmlGenerator::getButton("POST", null, $postAction, "postQuery");

        $comments=$this->video->getComments();
        $commentItems="";
        foreach($comments as $comment) {
            $commentItems.=$comment->formComment();
        }

        return "<div class='commentsContainer'>

                    <div class='header'>
                        <span class='commentCount'>$numOfComments Chats in forum</span>

                        <div class='commentForm'>
                            $profilePic
                            <textarea class='commentBodyClass' placeholder='Add a public question'></textarea>
                            $commentButton
                        </div>
                    </div>

                    <div class='comments'>
                        $commentItems
                    </div>

                </div>";
    }
 }
?>