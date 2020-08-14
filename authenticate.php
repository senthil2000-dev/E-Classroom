<?php
require_once("mailNow.php");
$success = "";
$error = "";
if(!empty($_POST["submitEmail"])) {
    
    $email=$_POST["submittedEmail"];
    $query=$con->prepare("SELECT * FROM authenticatedusers WHERE email=:email");
    $query->bindParam(":email", $email);
    $query->execute();
	$num  = $query->rowCount();
	if($num>0) {
        $otp = rand(100000,999999);
        $expiryStatus=0;
        $mailStatus = mailOtp($email,$otp);
        if($mailStatus==1) {
			$query=$con->prepare("INSERT INTO validateotp(otp,expiry) VALUES (:otp, :expiry)");
            $query->bindParam(":otp", $otp);
            $query->bindParam(":expiry", $expiryStatus);
			if($query->execute()) {
				$success=1;
			}
		}
		else {
			$error=$mailStatus;
		}
    }
    else {
		$error = "This email is not authorised to upload videos!";
	}
}
if(!empty($_POST["submitOtp"])) {
    $expiryStatus=0;
	$otp=$_POST["submittedOtp"];
	$query=$con->prepare("SELECT * FROM validateotp WHERE otp=:otp AND expiry=:expiry AND NOW() <= DATE_ADD(createdTime, INTERVAL 24 HOUR)");
    $query->bindParam(":otp", $otp);
    $query->bindParam(":expiry", $expiryStatus);
    $query->execute();
    $num  = $query->rowCount();
	if($num>0) {
        $expired=1;
        $query=$con->prepare("UPDATE validateotp SET expiry =:expiry WHERE otp =:otp");
        $query->bindParam(":otp", $otp);
		$query->bindParam(":expiry", $expired);
		$query->execute();
        $success = 2;
        $_SESSION["success"]=1;
	} else {
		$success =1;
		$error = "Invalid OTP! Try typing again or it may be expired";
	}	
}
?>
<html>
<head>
<title>Authenticate to upload</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>
<body>
	<div class="authenticateContainer">
		<?php
			if(!empty($error)) {
		?>
		<div class="alert alert-danger errorAuthenticateMessage"><?php echo $error; ?></div>
		<?php
			}
		?>

		<form name="tutor" method="POST" action="">
		<div class="tutorAuthenticate">
			<?php 
				if($success == 1) { 
			?>
			<div class="headerInfo">Enter OTP</div>
			<p style="color:#31ab00;">Check your email for the OTP</p>
				
			<div class="bodyInput">
				<input type="text" name="submittedOtp" placeholder="One Time Password" class="authenticateInput" required>
			</div>
			<div class="headerInfo"><input type="submit" name="submitOtp" value="Submit" class="submitBtn"></div>
			<?php 
				}
				else if ($success == 2) {
					$_SESSION["success"]=1;
					header("Location: upload.php");
				}
				else {
			?>
			<div class="headerInfo">Enter Your Login Email</div>
			<div class="bodyInput"><input type="text" name="submittedEmail" placeholder="Email" class="authenticateInput" required></div>
			<div class="headerInfo"><input type="submit" name="submitEmail" value="Submit" class="submitBtn"></div>
			<?php 
				}
			?>
		</div>
	</div>
</form>
</body></html>