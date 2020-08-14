<?php
require_once("includes/classes/ButtonHtmlGenerator.php");

class VideoRemarks{
    private $video, $userLoggedInObj;

    public function __construct($video, $userLoggedInObj) {
        $this->video=$video;
        $this->userLoggedInObj=$userLoggedInObj;
}

public function createSection() {

    $likeButton=$this->createLikeButton();
    $dislikeButton=$this->createDislikeButton();
    $bookmarkButton=$this->createBookmarkButton();

    return "<div class='controls'>
                $bookmarkButton
                $likeButton
                $dislikeButton
            </div>";
}

private function createBookmarkButton(){
    $text=($this->video->isBookmarked())?"BOOKMARKED":"BOOKMARK";
    $addClass=($this->video->isBookmarked())?"active":"";
    $videoId=$this->video->getId();
    $action="bookmark(this, $videoId)";
    $class="bookmarkButton $addClass";
    $imageSrc="assets/images/icons/bookmark.png";
    return ButtonHtmlGenerator::getButton($text, $imageSrc, $action, $class);
}

private function createLikeButton(){
    $text=$this->video->getLikes();
    $videoId=$this->video->getId();
    $action="likeVideo(this, $videoId)";
    $class="likeButton";

    $imageSrc="assets/images/icons/thumb-up.png";

    if($this->video->wasLikedBy()) {
        $imageSrc="assets/images/icons/thumb-up-active.png";
    }

    return ButtonHtmlGenerator::getButton($text, $imageSrc, $action, $class);
}

private function createDislikeButton(){
    $text=$this->video->getDislikes();
    $videoId=$this->video->getId();
    $action="dislikeVideo(this, $videoId)";
    $class="dislikeButton";

    $imageSrc="assets/images/icons/thumb-down.png";

    if($this->video->wasDislikedBy()) {
        $imageSrc="assets/images/icons/thumb-down-active.png";
    }

    return ButtonHtmlGenerator::getButton($text, $imageSrc, $action, $class);
}

}
?>