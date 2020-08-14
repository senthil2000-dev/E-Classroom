<?php
require_once("includes/header.php");
require_once("includes/classes/BranchwiseProvider.php");
$branch=isset($_GET["branch"])?$_GET["branch"]:"CSE";
$degree=isset($_GET["degree"])?$_GET["degree"]:"0";
$branchwiseProvider = new BranchwiseProvider($con, $userLoggedInObj);
$videos=$branchwiseProvider->getVideos($branch, $degree);
$videoMatrix = new VideoMatrix($con, $userLoggedInObj);
$query=$con->prepare("SELECT * FROM departments");
$query->execute();
$html="<div class='form-group'>
        <select class='form-control' name='branch'>";
while($row=$query->fetch(PDO::FETCH_ASSOC)){
    $name=$row["name"];
    $selected=($name==$branch) ? "selected" : "";
    $html.="<option $selected>$name</option>";
}
$btechSelected=($degree==0) ? "selected" : "";
$mtechSelected=($degree==1) ? "selected" : "";
$html.="</select>
        </div>
        <div class='form-group'>
            <select name='degree' class='form-control'>
                <option value='0' $btechSelected>Btech</option>
                <option value='1' $mtechSelected>MTech</option>
            </select>
        </div>";
?>

<div class="largeVideoMatrixContainer">
<form method="GET">
    <?php echo $html; ?>
    <button type='submit' class='btn btn-primary' name='filterButton'>Filter results</button>
</form>
<?php 
if(sizeof($videos)>0) {
    echo $videoMatrix->createLarge($videos, $branch." lectures", false);
}
else {
    echo "No lectures to show";
}
?>
</div>