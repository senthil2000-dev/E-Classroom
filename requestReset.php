<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require_once 'includes/config.php';
$message="";
$flag=0;

if(isset($_POST["email"])) {
    $query=$con->prepare("SELECT * FROM users where email=:email");
    $query->bindParam(":email", $emailTo);
    $emailTo=$_POST["email"];
    $query->execute();
    if($query->rowCount()==0) {
        $flag=1;
        $message = "<br><div class='alert alert-danger'>This email does not have an account</div>";
    }

    if($flag!=1) {
    $code=uniqid(true);
    $query=$con->prepare("INSERT INTO resetpasswords(code, email) VALUES(:code, :emailTo)");
    $query->bindParam(":code", $code);
    $query->bindParam(":emailTo", $emailTo);
    if(!$query->execute()) {
        exit("Error");
    }

    // Instantiation and passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = '2senthil2018@gmail.com';                     // SMTP username
        $mail->Password   = 'muthu2006';                               // SMTP password
        $mail->SMTPSecure = 'tls';         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
        $mail->Port       = 587;                                    // TCP port to connect to

        //Recipients
        $mail->setFrom('2senthil2018@gmail.com', 'E-Classroom');
        $mail->addAddress($emailTo);     // Add a recipient
        $mail->addReplyTo('mailsenthilnathan2003@gmail.com', 'No reply');

        // Content
        $url=$_SERVER["HTTP_HOST"].dirname($_SERVER["PHP_SELF"])."/resetPassword.php?code=$code";
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Your password reset link';
        $mail->Body    = "<h1>You requested a password reset</h1>
                            Click <a href='$url'>this link</a> to do so";
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        $mail->send();
        if((strpos($_SERVER['HTTP_REFERER'], "requestReset")==true)&&($flag!=1)) {
            $message = "<br><div class='alert alert-success'>Reset password link has been sent via email</div>";
        }
    } catch (Exception $e) {
            $message = "<br><div class='alert alert-danger'>Message could not be sent. Mailer Error: {$mail->ErrorInfo}</div>";
    }
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
                <?php echo $message; 
                if($message!="") exit(); ?>
        <span>Enter your recovery email</span>
        </div>

        <div class="logInForm">
            <form method="POST">
                
                <input type="text" name="email" placeholder="Email" autocomplete="off">
                <input type="submit" name="submit" value="Send email">
            </form>
        </div>

    </div>

</div>

</body>
</html>