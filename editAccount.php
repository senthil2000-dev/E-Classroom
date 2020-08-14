<?php
require_once("includes/header.php");
require_once("includes/classes/Account.php");
require_once("includes/classes/FormRefinery.php");
require_once("includes/classes/EditForm.php");
require_once("includes/classes/Constants.php");

if(!User::isLoggedIn()) {
    header("Location: signIn.php");
}
$detailsMessage="";
$passwordMessage="";
$formProvider = new EditForm();

if(isset($_POST["saveDetailsButton"])) {
    $account=new Account($con);

    $firstName=FormRefinery::sanitizeFormString($_POST["firstName"]);
    $lastName=FormRefinery::sanitizeFormString($_POST["lastName"]);
    $email=FormRefinery::sanitizeFormEmail($_POST["email"]);

    if($account->updateDetails($firstName, $lastName, $email, $userLoggedInObj->getUsername())) {
        $detailsMessage="<div class='alert alert-success'>
                            <strong>SUCCESS!</strong> Details updated successfully!
                        </div>";
    }
    else {
        $errorMessage=$account->getFirstError();
        if($errorMessage=="") $errorMessage="Something went wrong";

        $detailsMessage="<div class='alert alert-danger'>
                            <strong>ERROR!</strong> $errorMessage
                        </div>";
    }
}

if(isset($_POST["savePasswordButton"])) {
    $account=new Account($con);

    $oldPassword=FormRefinery::sanitizeFormPassword($_POST["oldPassword"]);
    $newPassword=FormRefinery::sanitizeFormPassword($_POST["newPassword"]);
    $newPassword2=FormRefinery::sanitizeFormPassword($_POST["newPassword2"]);

    if($account->updatePassword($oldPassword, $newPassword, $newPassword2, $userLoggedInObj->getUsername())) {
        $passwordMessage="<div class='alert alert-success'>
                            <strong>SUCCESS!</strong> Password updated successfully!
                        </div>";
    }
    else {
        $errorMessage=$account->getFirstError();
        if($errorMessage=="") $errorMessage="Something went wrong";

        $passwordMessage="<div class='alert alert-danger'>
                            <strong>ERROR!</strong> $errorMessage
                        </div>";
    }
}
?>
<div class="accountContainer column">

    <div class="formSection">
        <div class="message">
            <?php echo $detailsMessage; ?>
        </div>
        <?php
            echo $formProvider->createUserDetailsForm(
                isset($_POST["firstName"]) ? $_POST["firstName"] : $userLoggedInObj->getFirstName(),
                isset($_POST["lastName"]) ? $_POST["lastName"] : $userLoggedInObj->getLastName(),
                isset($_POST["email"]) ? $_POST["email"] : $userLoggedInObj->getEmail()
            );
        ?>
    </div>

    <div class="formSection">
        <div class="message">
            <?php echo $passwordMessage; ?>
        </div>
        <?php
            echo $formProvider->createPasswordForm();
        ?>
    </div>
</div>