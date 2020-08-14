<?php
require_once("includes/config.php");
if(!isset($_GET["code"])) {
    exit("Can't find page");
}

$code=$_GET["code"];

$query=$con->prepare("SELECT email FROM resetpasswords where code=:code");
$query->bindParam(":code", $code);
$query->execute();
if($query->rowCount()==0) {
    exit("Can't find page");
}

if(isset($_POST["password"])) {
    $pw=$_POST["password"];
    $pw=hash("sha512", $pw);

    $email=$query->fetchColumn();

    $query=$con->prepare("UPDATE users SET password=:pw WHERE email=:email");
    $query->bindParam(":pw", $pw);
    $query->bindParam(":email", $email);

    if($query->execute()) {
        $query=$con->prepare("DELETE FROM resetpasswords WHERE code=:code");
        $query->bindParam(":code", $code);
        $query->execute();
           
        header("Location: signIn.php");
    }
    else {
        exit("Something went wrong");
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>E-Classroom</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="assets/css/style.css">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</head>
<body>


<div class="signInContainer">

    <div class="column">

        <div class="header">
        <img src="assets/images/icons/eClassroomLogo.jpg" title="logo" alt="Site logo">
        <h3>Reset password</h3>
        <span>Enter your new password</span>
        </div>

        <div class="logInForm">
        <form method="POST">
            <input type="password" name="password" placeholder="New Password">
            <input type="submit" id= "wide" name="submit" value="Update password">
        </form>
        </div>
    </div>

</div>

</body>
</html>