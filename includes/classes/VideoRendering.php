<?php
class VideoRendering
{
    private $con;
    private $permittedTypes=array("mp4","flv","webm","mkv","vob","ogv","ogg","avi","wmv","mov","mpeg","mpg");
    private $ceilSize=500000000;
    
    private $ffmpegPath;
    private $ffprobePath;

    public function __construct($con){
        $this->con=$con;
        $this->ffmpegPath=realpath("ffmpeg/bin/ffmpeg.exe");
        $this->ffprobePath=realpath("ffmpeg/bin/ffprobe.exe");
    }
    
    public function upload($videoDetails){
        $destinationDir="uploads/videos/";
        $videoData=$videoDetails->videoDataArray;

        $interimPath=$destinationDir.uniqid().basename($videoData["name"]);
        $interimPath=str_replace(" ","_",$interimPath);
        
        $validatedData=$this->validateData($videoData,$interimPath);

        if(!$validatedData){
            return false;
        }

        if(move_uploaded_file($videoData["tmp_name"],$interimPath)){
            $finalDestination=$destinationDir. uniqid().".mp4";

            if(!$this->pushVideoDetails($videoDetails,$finalDestination)){
                echo "Failed to insert video";
                return false;
            }

            if(!$this->renderMp4($interimPath, $finalDestination)){
                echo "Upload failed";
                return false;
            }
            if(!$this->unlinkFile($interimPath)){
                echo "Upload failed";
                return false;
            }
            $this->addOverlay($finalDestination);
            if(!$this->createThumbnails($finalDestination)){
                echo "Could not create thumbnails\n";
                return false;
            }

            return true;

        }
    }

    private function validateSize($data){
        return $data["size"]<=$this->ceilSize;
    }
    private function validateType($type){
        $lowercased=strtolower($type);
        return in_array($lowercased, $this->permittedTypes);
    }
    private function hasError($data){
        return $data["error"]!=0;
    }
    
    private function validateData($videoData,$filePath){
        $videoType=pathinfo($filePath, PATHINFO_EXTENSION);

        if(!$this->validateSize($videoData)){
            echo "File is too large than ".$this->ceilSize." bytes";
            return false;
        }
        else if(!$this->validateType($videoType)){
            echo "Not a valid video file type";
            return false;
        }
        else if($this->hasError($videoData)){
            echo "Error name:".$videoData["error"];
            return false;
        }

        return true;
    }

    
    private function pushVideoDetails($uploadData, $filePath){
        $query=$this->con->prepare("INSERT INTO videos(title,uploadedBy,description,degree,category,filePath)
                                    VALUES(:title, :uploadedBy, :description, :degree, :category, :filePath)");

        $query->bindParam(":title", $uploadData->title);
        $query->bindParam(":uploadedBy", $uploadData->uploadedBy);
        $query->bindParam(":description", $uploadData->description);
        $query->bindParam(":degree", $uploadData->degree);
        $query->bindParam(":category", $uploadData->category);
        $query->bindParam(":filePath", $filePath);

        return $query->execute();
    }

    public function renderMp4($interimPath, $finalDestination) {
        $cmd="$this->ffmpegPath -i $interimPath $finalDestination 2>&1";

        $outputLog=array();
        exec($cmd, $outputLog, $returnCode);
        if($returnCode!=0){
            foreach($outputLog as $line){
                echo $line."<br>";
            }
            return false;
        }

        return true;
    }
    private function unlinkFile($filePath){
        if (!unlink($filePath)){
            echo "Could not delete file\n";
            return false;
        }
        return true;
    }
    public function addOverlay($filePath) {
        $image = "assets/images/icons/watermark.png";
        
        $command = "$this->ffmpegPath -i " . $image . " -s 100x50 output.jpeg";
        system($command);
        $command = "$this->ffmpegPath -i " . $filePath . " -i output.jpeg";
        $command .= " -filter_complex \"[0:v][1:v]";
        $command .= " overlay=5:10\"";
        $command .= " -c:a copy output.mp4";
        system($command);
        unlink("output.jpeg");
        unlink($filePath);
        rename("output.mp4", $filePath);

    }
    public function createThumbnails($filePath){
        $thumbnailSize="210*118";
        $numThumbnails=3;
        $pathToThumbnail="uploads/videos/thumbnails";

        $length=$this->videoLength($filePath);
        $videoId=$this->con->lastInsertId();

        $this->formatVideoLength($length, $videoId);

        for($num=1;$num<=$numThumbnails;$num++){
            $imageName=uniqid().".jpg";
            $interval=($length*0.8)/$numThumbnails*$num;
            $fullThumbnailPath="$pathToThumbnail/$videoId-$imageName";

            $cmd="$this->ffmpegPath -i $filePath -ss $interval -s $thumbnailSize -vframes 1 $fullThumbnailPath 2>&1";

            $outputLog=array();
            exec($cmd, $outputLog, $returnCode);
            if($returnCode!=0){
                //Command failed
                foreach($outputLog as $line){
                    echo $line."<br>";
                }
            }

            $query=$this->con->prepare("INSERT INTO thumbnails(videoId, filePath, selected)
                                        VALUES(:videoId,:filePath,:selected)");
            $query->bindParam(":videoId",$videoId);
            $query->bindParam(":filePath",$fullThumbnailPath);
            $query->bindParam(":selected",$selected);

            $selected=$num==1?1:0;

            $success=$query->execute();

            if(!$success){
                echo "Error inserting thumbnail\n";
                return false;
            }
        }
        return true;

    }

    private function videoLength($filePath){
        return (int)shell_exec("$this->ffprobePath -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 $filePath");
    }

    private function formatVideoLength($length, $videoId){
        $hours=floor($length/3600);
        $mins=floor(($length-($hours*3600))/60);
        $secs=floor($length%60);

        $hours=($hours<1)?"":$hours.":";
        $mins=($mins<10)?"0".$mins.":":$mins.":";
        $secs=($secs<10)?"0".$secs:$secs;

        $length=$hours.$mins.$secs;

        $query=$this->con->prepare("UPDATE videos SET length=:length where id=:videoId");
        $query->bindParam(":length",$length);
        $query->bindParam(":videoId",$videoId);
        $query->execute();
    }
}
?>