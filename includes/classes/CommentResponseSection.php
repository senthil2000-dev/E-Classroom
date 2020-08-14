<?php
require_once("ButtonHtmlGenerator.php");

class CommentResponseSection{
    private $con, $comment, $userLoggedInObj;

    public function __construct($con, $comment, $userLoggedInObj) {
        $this->con=$con;
        $this->comment=$comment;
        $this->userLoggedInObj=$userLoggedInObj;
}

public function create() {

    $answerButton=$this->createAnswerButton();
    $likesCount=$this->createLikesCount();
    $likeButton=$this->createLikeButton();
    $dislikeButton=$this->createDislikeButton();
    $answerSection=$this->createAnswerSection();

    return "<div class='controls'>
                $answerButton
                $likesCount
                $likeButton
                $dislikeButton
            </div>
            $answerSection";
}

private function createAnswerButton() {
    $text="ANSWER";
    $action="toggleReply(this)";
    return ButtonHtmlGenerator::getButton($text, null, $action, null);
}

private function createLikesCount() {
    $text=$this->comment->getLikes();

    if($text==0) $text="";

    return "<span class='likesCount'>$text</span>";
}

private function createAnswerSection() {
        $postedBy = $this->userLoggedInObj->getUsername();
        $videoId=$this->comment->getVideoId();
        $commentId=$this->comment->getId();
        
        $profilePic=ButtonHtmlGenerator::createChannelButton($this->con, $postedBy);

        $cancelButtonAction="toggleReply(this)";
        $cancelButton=ButtonHtmlGenerator::getButton("Cancel", null, $cancelButtonAction, "cancelComment");

        $postButtonAction="postQuery(this, \"$postedBy\", $videoId, $commentId, \"repliesSection\")";
        $postButton=ButtonHtmlGenerator::getButton("ANSWER", null, $postButtonAction, "postQuery");

        return "<div class='commentForm respondForm hidden'>
                    $profilePic
                    <textarea class='commentBodyClass' placeholder='Add a public answer'></textarea>
                    $cancelButton
                    $postButton
                </div>";
}

private function createLikeButton(){
    $videoId=$this->comment->getVideoId();
    $commentId=$this->comment->getId();
    $action="likeComment($commentId, this, $videoId)";
    $class="likeButton";

    $imageSrc="assets/images/icons/thumb-up.png";

    if($this->comment->wasLikedBy()) {
        $imageSrc="assets/images/icons/thumb-up-active.png";
    }

    return ButtonHtmlGenerator::getButton("", $imageSrc, $action, $class);
}

private function createDislikeButton(){
    $commentId=$this->comment->getId();
    $videoId=$this->comment->getVideoId();
    $action="dislikeComment($commentId, this, $videoId)";
    $class="dislikeButton";

    $imageSrc="assets/images/icons/thumb-down.png";

    if($this->comment->wasDislikedBy()) {
        $imageSrc="assets/images/icons/thumb-down-active.png";
    }

    return ButtonHtmlGenerator::getButton("", $imageSrc, $action, $class);
}

}
?>