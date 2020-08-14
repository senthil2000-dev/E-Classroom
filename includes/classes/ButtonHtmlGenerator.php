<?php
class ButtonHtmlGenerator {

    public static $signInFunction="notSignedIn()";

    public static function createLink($link) {
        return User::isLoggedIn() ? $link: ButtonHtmlGenerator::$signInFunction;
    }

    public static function getButton($text, $imageSrc, $action, $class) {
       
       $image=($imageSrc==null)?"":"<img src='$imageSrc'>";

       $action=ButtonHtmlGenerator::createLink($action);
       
        return "<button class='$class' onclick='$action' >
                    $image
                    <span class='text'>$text</span>
                </button>";
    }

    public static function getAnchorButton($text, $imageSrc, $href, $class) {
       
        $image=($imageSrc==null)?"":"<img src='$imageSrc'>";
        
         return "<a href='$href'>
                    <button class='$class'>
                        $image
                        <span class='text'>$text</span>
                    </button>
                 </a>";
     }

    public static function createChannelButton($con, $username) {
        $userObj=new User($con, $username);
        $profilePic=$userObj->getProfilePic();
        $link="channel.php?username=$username";
        if($username=="") {
            $userProfile="<a onclick='notSignedIn2(this)'>";
        }
        else {
            $userProfile="<a href='$link'>";
        }
        return "$userProfile
                    <img src='$profilePic' class='profilePicture'>
                </a>";
    }

    public static function createReviseVideoButton($videoId) {
        $href="reviseVideo.php?videoId=$videoId";

        $button=ButtonHtmlGenerator::getAnchorButton("MODIFY VIDEO", null, $href, "edit button");

        return "<div class=reviseVideoButtonContainer>
            $button
        </div>";
    }

    public static function createSubscriberButton($con, $userToObj, $userLoggedInObj) {
        $userTo=$userToObj->getUsername();
        $userLoggedIn=$userLoggedInObj->getUsername();

        $isSubscribedTo=$userLoggedInObj->isSubscribedTo($userTo);
        $buttonText = $isSubscribedTo ? "SUBSCRIBED" : "SUBSCRIBE";
        $buttonText .= " " . $userToObj->getSubscriberCount();
        $buttonClass=$isSubscribedTo?"unsubscribe button":"subscribe button";
        $action="subscribe(\"$userTo\", \"$userLoggedIn\", this)";

        $button=ButtonHtmlGenerator::getButton($buttonText, null, $action, $buttonClass);

        return "<div class='subscribeButtonContainer'>
                    $button
                </div>";
    }

    public static function createSelfChannelNavButton($con, $username) {
        if(User::isLoggedIn()) {
            return ButtonHtmlGenerator::createChannelButton($con, $username);
        }
        else {
            return "<a href='signIn.php'>
                        <span class='signInLink'>SIGN IN</span>
                    </a>";
        }
    }
}
?>