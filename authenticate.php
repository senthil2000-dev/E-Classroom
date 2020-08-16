<?php
require_once("mailNow.php");
$success = "";
$error = "";
if(!isset($_SESSION["userLoggedIn"]))
	header("Location: signIn.php");
if(isset($_SESSION["userTrying"]))
	$redirectUrl=$_SESSION["userTrying"]==1?"record.php":"upload.php";
else
	$redirectUrl="upload.php";
if(!empty($_POST["submitEmail"])) {  
	$email=$_POST["submittedEmail"];
	$query=$con->prepare("SELECT * FROM users WHERE email=:email AND username=:username");
	$query->bindParam(":email", $email);
	$query->bindParam(":username", $username);
	$username=$_SESSION["userLoggedIn"];
    $query->execute();
	$exists  = $query->rowCount();
	if($exists>0) {
		$query=$con->prepare("SELECT * FROM authenticatedusers WHERE email=:email");
		$query->bindParam(":email", $email);
		$query->execute();
		$num  = $query->rowCount();
		if($num>0) {
			$expiryStatus=1;
			$query=$con->prepare("UPDATE validateotp SET expiry=:expiry WHERE email=:email");
			$query->bindParam(":email", $email);
			$query->bindParam(":expiry", $expiryStatus);
			$query->execute();
			$otp = rand(100000,999999);
			$expiryStatus=0;
			$mailStatus = mailOtp($email,$otp);
			if($mailStatus==1) {
				$query=$con->prepare("INSERT INTO validateotp(otp,expiry,email) VALUES (:otp, :expiry, :email)");
				$query->bindParam(":otp", $otp);
				$query->bindParam(":expiry", $expiryStatus);
				$query->bindParam(":email", $email);
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
	else {
		$error="This is not your login email";
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
		$error = "Invalid OTP!";
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
			<p style="margin-bottom:0;color:#31ab00;">Check your email for the OTP</p>
			<span style="color:red;">OTP will be valid only for 24 hours</span>	
			<div class="bodyInput">
				<input type="text" name="submittedOtp" placeholder="One Time Password" class="authenticateInput" required>
			</div>
			<div class="headerInfo"><input type="submit" name="submitOtp" value="Submit" class="submitBtn"><button class="submitBtn" onclick="resend()">Resend OTP</button></div>
			<?php 
				echo "<script>alert('OTP sent')</script>";
				}
				else if ($success == 2) {
					$_SESSION["success"]=1;
					header("Location: $redirectUrl");
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
<script>

function resend() {
	document.getElementsByClassName("authenticateInput")[0].value="";
	location.reload();
}
</script>
</body></html>