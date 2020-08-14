<?php
class VideoDetailsFormProvider{
    private $con;

    public function __construct($con){
        $this->con=$con;
    }
    public function createUploadForm(){
        $fileInput=$this->createFileInput();
        $titleInput=$this->createTitleInput(null);
        $descriptionInput=$this->createDescriptionInput(null);
        $degreeInput=$this->createDegreeInput(null);
        $departmentsInput=$this->createDepartmentsInput(null);
        $uploadButton=$this->createUploadButton();
        $action="uploading.php";

        
        return "<form action='$action' method='POST' enctype='multipart/form-data'>
                    $fileInput
                    $titleInput
                    $descriptionInput
                    $degreeInput
                    $departmentsInput
                    $uploadButton
                </form>";
    }

    public function createEditDetailsForm($video){
        $titleInput=$this->createTitleInput($video->getTitle());
        $descriptionInput=$this->createDescriptionInput($video->getDescription());
        $degreeInput=$this->createDegreeInput($video->getDegree());
        $departmentsInput=$this->createDepartmentsInput($video->getCategory());
        $saveButton=$this->createSaveButton();
        return "<form method='POST'>
                    $titleInput
                    $descriptionInput
                    $degreeInput
                    $departmentsInput
                    $saveButton
                </form>";
    }

    private function createFileInput(){

    return "<div class='form-group'>
                <label for='exampleFormControlFile1'>Your file</label>
                <input type='file' class='form-control-file' id='exampleFormControlFile1' name='fileInput' required>
            </div>";
    }
    private function createTitleInput($value){
        if($value==null) $value="";
        return "<div class='form-group'>
                    <input class='form-control' type='text' placeholder='Title' name='titleInput' value='$value'>
                </div>";
    }
    private function createDescriptionInput($value){
        if($value==null) $value="";
        return "<div class='form-group'>
                    <textarea  class='form-control' placeholder='Description' name='descriptionInput' style='resize:none;' rows='3'>$value</textarea>
                </div>";
    }
    private function createDegreeInput($value){
        if($value==null) $value="";

        $btechSelected=($value==0) ? "selected='selected'" : "";
        $mtechSelected=($value==1) ? "selected='selected'" : "";

        return "<div class='form-group'>
                    <select class='form-control' name='degree'>
                        <option value='0' $btechSelected>Btech</option>
                        <option value='1' $mtechSelected>MTech</option>
                    </select>
                </div>
                ";
        
    }
    private function createDepartmentsInput($value) {
        if($value==null) $value="";
        $query=$this->con->prepare("SELECT * FROM departments");
        $query->execute();
        $html="<div class='form-group'>
        <select class='form-control' name='categoryInput'>";

        while($row=$query->fetch(PDO::FETCH_ASSOC)){
            $id=$row["id"];
            $name=$row["name"];
            $selected=($id==$value) ? "selected='selected'" : "";

            $html.="<option value='$id' $selected>$name</option>";
        }

        $html.="</select>
            </div>";
        
        return $html;
    }
    private function createUploadButton(){
        return "<button type='submit' class='btn btn-primary' name='uploadButton'>Upload</button>";
    }

    private function createSaveButton(){
        return "<button type='submit' class='btn btn-primary' name='saveButton'>Save</button>";
    }
}
?>