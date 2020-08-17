<?php
class NavigationBar {

    private $con, $userLoggedInObj;
    
    public function __construct($con, $userLoggedInObj) {
        $this->con=$con;
        $this->userLoggedInObj=$userLoggedInObj;
    }

    public function create() {
        $navContent=$this->createNavItem("Home", "assets/images/icons/home.png", "index.php");
        $navContent.=$this->createNavItem("Department", "assets/images/icons/branch.png", "branch.php");
        $navContent.=$this->createNavItem("Subscriptions", "assets/images/icons/subscriptions.png", "subscriptionVideos.php");
        $navContent.=$this->createNavItem("Bookmarks", "assets/images/icons/bookmark.png", "bookmarks.php");

        if(User::isLoggedIn()) {
            $navContent.=$this->createNavItem("Record Class", "assets/images/icons/record.png", "record.php");
            $navContent.=$this->createNavItem("Merge Videos", "assets/images/icons/merge.png", "mergeVideos.php");
            $navContent.=$this->createNavItem("Edit Account", "assets/images/icons/editform.png", "editAccount.php");
            $navContent.=$this->createNavItem("Create Playlist", "assets/images/playlist.png", "createPlaylist.php");
            $navContent.=$this->createNavItem("Watch History", "assets/images/icons/history.png", "history.php");
            $navContent.=$this->createNavItem("Search History", "assets/images/icons/history.png", "searchHistory.php");
            $navContent.=$this->createNavItem("Profilepic with webcam", "assets/images/profilePictures/default.png", "live.php");
            $navContent.=$this->createNavItem("Log Out", "assets/images/icons/logout.png", "logout.php");
            $navContent.=$this->createSubscriptionsSection();
        }

        

        return "<div class='navOptions'>
                    $navContent
                </div>";
    }

    private function createNavItem($menuOption, $iconPic, $relUrl) {
        return "<div class='navOption'>
                    <a href='$relUrl'>
                        <img src='$iconPic'>
                        <span>$menuOption</span>
                    </a>
                </div>";
    }

    private function createSubscriptionsSection() {
        $subscriptions=$this->userLoggedInObj->getSubscriptions();

        $html="<span class='heading'>Subscriptions</span>";
        foreach($subscriptions as $sub) {
            $subUsername=$sub->getUsername();
            $html.=$this->createNavItem($subUsername, $sub->getProfilePic(), "channel.php?username=$subUsername");
        }

        return $html;
    }
}
?>