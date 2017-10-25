<?php
ob_start();
session_start();
require(dirname(__FILE__) . '/Uploader.php');
require_once("../../config/uploadConfig.php");

// Directory where we're storing uploaded images
// Remember to set correct permissions or it won't work
// if(!isset($_POST['country']) || $_POST['country'] == ''){
//   exit(json_encode(array('success' => false, 'msg' => "Error: No country specified!")));
// }
$country = $_POST['country'];
$remark = $_POST['remarks'];
$upload_dir = '../../'.UPLOAD_ROOT_DIRECTORY.'/'.$country;

if(!is_dir($upload_dir)){
  if(!mkdir($upload_dir, 0777, true)){
    exit(json_encode(array('success' => false, 'msg' => "Cant create directory ".$upload_dir.", please grant access or create manually.")));
  }
  else{
    //change the directory accessible to current user account instead of 'root' user
    chown($upload_dir, get_current_user());

  }
}
$valid_extensions = $ALLOWED_FILE_TYPES;

$uploader = new FileUpload('uploadfile');
$fileName = $uploader->getFileName();
$path = UPLOAD_ROOT_DIRECTORY.'/'.$country."/".$fileName;

if(file_exists($upload_dir."/".$fileName)){
    exit(json_encode(array('success' => false, 'msg' => "File already exists at ".$path)));
}

//set the max allowed size
$uploader->sizeLimit = 268435456;//256MegaByte
// Handle the upload
$result = $uploader->handleUpload($upload_dir, $valid_extensions);

if (!$result) {
  exit(json_encode(array('success' => false, 'msg' => $uploader->getErrorMsg())));
}

//if we are this far, we've had a success on upload
//Lets enter this query to DB
require_once("../../config/dbConfig.php");
$link = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
if (mysqli_connect_errno()){
    exit();
}

$query = "INSERT INTO osedetail (
                                country,
                                osename,
                                osezip,
                                remark,
                                user,
                                remoteaddress) VALUES ('" .
                                mysqli_real_escape_string($link, $country) . "','" .
                                mysqli_real_escape_string($link,  $fileName) . "','" .
                                mysqli_real_escape_string($link,  $path) . "','" .
                                mysqli_real_escape_string($link,  $remark) . "','" .
                                 $_SESSION['myusername'] . "','" .
                                 $_SERVER['REMOTE_ADDR'] . "')";

mysqli_autocommit($link,FALSE);
mysqli_query($link, $query);
$result = 0;
if(mysqli_errno($link)){
    echo mysqli_error($link);
    $result = -1;
  }
else{
  mysqli_commit($link);
  $result = 1;

}
mysqli_close($link);

if($result == 1) echo json_encode(array('success' => true));
else exit(json_encode(array('success' => false, 'msg' =>"File uploaded, but cant save data, plese try again.")));
