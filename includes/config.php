<?php
ob_start(); //Turns on output buffering

session_start();

date_default_timezone_set("Asia/Kolkata");
try{
  $con=new PDO("mysql:dbname=lectures;host=localhost;port=3306","root","");
  $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
}
catch (PDOException $e){
    echo "Connection failed".$e->getMessage();
}
?>