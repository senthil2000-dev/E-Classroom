<?php
class Playlist {
    public $con, $sqlData, $username;
    public function __construct($con, $row, $username) {
        $this->con=$con;
        $this->sqlData=$row;
        $this->username=$username;
    }
}
?>